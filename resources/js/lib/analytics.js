export const CONSENT_KEY = 'devsprendimai.cookie-consent.v1'
export const CONSENT_EVENT = 'devsprendimai:consent-changed'
export const OPEN_CONSENT_EVENT = 'devsprendimai:open-consent'
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
    let choice = readChoice()

    function readChoice() {
        try {
            const saved = JSON.parse(win.localStorage.getItem(CONSENT_KEY))
            return saved?.version === 1 && typeof saved.analytics === 'boolean'
                && saved.expiresAt > Date.now() ? saved : null
        } catch {
            return null
        }
    }

    function getConsent() {
        return choice?.expiresAt > Date.now() ? choice.analytics : null
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
            page_title: doc.title,
            page_referrer: lastPage || (doc.referrer ? safeUrl(doc.referrer) : ''),
        }
        win.gtag('set', fields)
        win.gtag('event', 'page_view', { ...fields, send_to: measurementId })
        lastPage = location
    }

    function configure(id) {
        measurementId = /^G-[A-Z0-9]+$/.test(id || '') ? id : null
        if (getConsent() !== true) clearAnalyticsCookies()
        activate()
        trackPage()
    }

    function setConsent(analytics) {
        choice = { version: 1, analytics: analytics === true, updatedAt: Date.now(), expiresAt: Date.now() + consentLifetime }
        try { win.localStorage.setItem(CONSENT_KEY, JSON.stringify(choice)) } catch { /* In-memory choice still works. */ }
        if (!choice.analytics) {
            pause()
            if (loadedId) win.gtag('consent', 'update', denied)
            clearAnalyticsCookies()
        } else {
            activate()
            trackPage()
        }
        win.dispatchEvent(new win.CustomEvent(CONSENT_EVENT))
        // Unload Google's history/engagement listeners when consent is withdrawn.
        if (!choice.analytics && loadedId) win.location.reload()
    }

    win.addEventListener('storage', (event) => {
        if (event.key !== CONSENT_KEY && event.key !== null) return
        choice = readChoice()
        if (getConsent() !== true) {
            pause()
            clearAnalyticsCookies()
            if (loadedId) win.location.reload()
        } else {
            activate()
            trackPage()
        }
        win.dispatchEvent(new win.CustomEvent(CONSENT_EVENT))
    })

    return { configure, getConsent, setConsent, pause }
}

export const analytics = typeof window !== 'undefined' ? createAnalytics(window, document) : null
export function openConsentSettings() {
    window.dispatchEvent(new CustomEvent(OPEN_CONSENT_EVENT))
}
