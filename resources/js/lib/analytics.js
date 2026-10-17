export const CONSENT_KEY = 'devsprendimai.cookie-consent.v2'
export const CONSENT_EVENT = 'devsprendimai:consent-changed'
export const OPEN_CONSENT_EVENT = 'devsprendimai:open-consent'
export const CONSENT_COOKIE = 'devsprendimai_analytics_consent'
const consentLifetime = 180 * 24 * 60 * 60 * 1000
const denied = {
    analytics_storage: 'denied', ad_storage: 'denied',
    ad_user_data: 'denied', ad_personalization: 'denied',
}

export function isPublicPath(path) {
    return !/^\/(?:en\/)?(?:dashboard|login|register|forgot-password|reset-password|verify-email|confirm-password|profile)(?:\/|$)/.test(path)
}

// Inertia owns page changes. GA4's separate enhanced-history page views should
// remain disabled for this stream to avoid counting each navigation twice.
export function createAnalytics(win, doc) {
    let measurementId = null
    let loadedId = null
    let active = false
    let lastPage = null
    let pageTitle = ''
    let consentRevision = 0
    const consumedLeads = new Set()
    let canPersistChoice = true
    let choice = readChoice()
    try { win.localStorage.removeItem('devsprendimai.cookie-consent.v1') } catch { /* Older preferences are never reused. */ }

    function readChoice() {
        try {
            const saved = JSON.parse(win.localStorage.getItem(CONSENT_KEY))
            if (saved?.version === 2 && typeof saved.analytics === 'boolean' && saved.expiresAt > Date.now()) return saved
            win.localStorage.removeItem(CONSENT_KEY)
            return null
        } catch {
            canPersistChoice = false
            return null
        }
    }

    function getConsent() {
        return choice?.expiresAt > Date.now() ? choice.analytics : null
    }

    function syncConsentCookie() {
        const consent = getConsent()
        const maxAge = consent === null || !canPersistChoice ? 0 : Math.max(0, Math.floor((choice.expiresAt - Date.now()) / 1000))
        // No identity or form data. The server reads only the visitor's preference.
        doc.cookie = `${CONSENT_COOKIE}=${consent === true ? 'granted' : 'denied'}; Max-Age=${maxAge}; Path=/; SameSite=Lax${win.location.protocol === 'https:' ? '; Secure' : ''}`
    }

    function clearAnalyticsCookies() {
        const host = win.location.hostname
        const domains = ['', host, `.${host}`]
        const labels = host.split('.')
        for (let i = 1; i < labels.length - 1; i++) domains.push(`.${labels.slice(i).join('.')}`)
        for (const cookie of doc.cookie.split(';')) {
            const name = cookie.trim().split('=')[0]
            if (!/^(_ga(?:_|$)|_gid$|_gat(?:_|$))/.test(name)) continue
            for (const domain of domains) {
                doc.cookie = `${name}=; Max-Age=0; Path=/; SameSite=Lax${domain ? `; Domain=${domain}` : ''}`
            }
        }
    }

    function pause() {
        active = false
        lastPage = null
        if (loadedId) win[`ga-disable-${loadedId}`] = true
    }

    function activate() {
        if (!measurementId || getConsent() !== true || !isPublicPath(win.location.pathname)) {
            pause()
            return
        }
        if (active) return
        active = true
        win[`ga-disable-${measurementId}`] = false

        if (!loadedId) {
            loadedId = measurementId
            win.dataLayer = win.dataLayer || []
            win.gtag = function () { win.dataLayer.push(arguments) }
            win.gtag('consent', 'default', denied)
            win.gtag('consent', 'update', { ...denied, analytics_storage: 'granted' })
            win.gtag('js', new Date())
            // Only injected after affirmative consent; no pre-consent Google pings.
            const script = doc.createElement('script')
            script.id = 'devsprendimai-google-analytics'
            script.async = true
            script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`
            doc.head.appendChild(script)
        }

        win.gtag('config', measurementId, {
            send_page_view: false,
            allow_google_signals: false,
            allow_ad_personalization_signals: false,
            cookie_expires: consentLifetime / 1000,
            cookie_update: false,
            cookie_flags: 'SameSite=Lax;Secure',
        })
    }

    function safeUrl(value, includeCampaign = false) {
        try {
            const url = new URL(value, win.location.href)
            if (!/^https?:$/.test(url.protocol) || !isPublicPath(url.pathname)) return ''
            const campaign = new URLSearchParams()
            if (includeCampaign) {
                for (const key of ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term']) {
                    if (url.searchParams.has(key)) campaign.set(key, url.searchParams.get(key).slice(0, 100))
                }
            }
            return url.origin + url.pathname + (campaign.size ? `?${campaign}` : '')
        } catch { return '' }
    }

    function trackPage() {
        if (!active || getConsent() !== true) return
        const location = safeUrl(win.location.href, true)
        if (!location || location === lastPage) return
        const fields = {
            page_location: location,
            page_title: pageTitle || doc.title,
            page_referrer: lastPage || (doc.referrer ? safeUrl(doc.referrer) : ''),
        }
        win.gtag('set', fields)
        win.gtag('event', 'page_view', { ...fields, send_to: measurementId })
        lastPage = location
    }

    function configure(id, title = '') {
        measurementId = /^G-[A-Z0-9]+$/.test(id || '') ? id : null
        pageTitle = title
        syncConsentCookie()
        if (getConsent() !== true) clearAnalyticsCookies()
        activate()
        trackPage()
    }

    function beginLeadSubmission() {
        // Capture permission at submission time. A later opt-in must not cause
        // an earlier enquiry to be tracked, even if its response is still pending.
        const permitted = active && getConsent() === true && isPublicPath(win.location.pathname)
        const revision = consentRevision
        const submissionId = measurementId
        let completed = false

        return (marker, page = {}) => {
            if (completed) return false
            completed = true
            if (!marker || !/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(marker.id || '')
                || marker.form_id !== 'project_enquiry' || !['lt', 'en'].includes(marker.locale)) return false
            if (consumedLeads.has(marker.id)) return false
            consumedLeads.add(marker.id)

            if (!permitted || !active || revision !== consentRevision || getConsent() !== true
                || !measurementId || submissionId !== measurementId
                || page.auth?.user || page.analytics?.measurementId !== measurementId
                || !isPublicPath(win.location.pathname)) return false

            // Fixed, allowlisted parameters only. Do not send the marker, form
            // values, inquiry ID or invented monetary value to Google.
            try {
                win.gtag('event', 'generate_lead', {
                    form_id: 'project_enquiry',
                    language: marker.locale,
                    send_to: measurementId,
                })
                return true
            } catch {
                // Analytics must never prevent the enquiry success/reset UI.
                return false
            }
        }
    }

    function setConsent(analytics) {
        consentRevision++
        choice = { version: 2, analytics: analytics === true, updatedAt: Date.now(), expiresAt: Date.now() + consentLifetime }
        try {
            win.localStorage.setItem(CONSENT_KEY, JSON.stringify(choice))
            canPersistChoice = true
        } catch { canPersistChoice = false }
        syncConsentCookie()
        if (!choice.analytics) {
            pause()
            try {
                if (loadedId) win.gtag('consent', 'update', denied)
            } catch { /* Consent withdrawal must work even if Google is blocked. */ }
            clearAnalyticsCookies()
        } else {
            try {
                activate()
                trackPage()
            } catch { /* Consent UI and first-party statistics are independent. */ }
        }
        win.dispatchEvent(new win.CustomEvent(CONSENT_EVENT))
        // Unload Google's history/engagement listeners when consent is withdrawn.
        if (!choice.analytics && loadedId) win.location.reload()
    }

    win.addEventListener('storage', (event) => {
        if (event.key !== CONSENT_KEY && event.key !== null) return
        consentRevision++
        choice = readChoice()
        syncConsentCookie()
        if (getConsent() !== true) {
            pause()
            clearAnalyticsCookies()
            if (loadedId) win.location.reload()
        } else {
            try {
                activate()
                trackPage()
            } catch { /* Still notify this tab about the changed preference. */ }
        }
        win.dispatchEvent(new win.CustomEvent(CONSENT_EVENT))
    })

    return { configure, getConsent, setConsent, pause, beginLeadSubmission }
}

export const analytics = typeof window !== 'undefined' ? createAnalytics(window, document) : null
export function openConsentSettings() {
    window.dispatchEvent(new CustomEvent(OPEN_CONSENT_EVENT))
}
