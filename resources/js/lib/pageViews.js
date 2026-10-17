import { CONSENT_COOKIE, CONSENT_EVENT, CONSENT_KEY, isPublicPath } from './analytics.js'

const campaignKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']
const uuidPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i

// Dashboard views describe rendered pages, not GET requests: an Inertia
// prefetch is not a view, while displaying a cached page is a view.
export function createPageViews(win, doc, { submit } = {}) {
    let enabled = false
    let displayed = null
    let hasDisplayedPublicPage = false

    function cookie(name) {
        return doc.cookie.split(';').map((part) => part.trim())
            .find((part) => part.startsWith(`${name}=`))?.slice(name.length + 1) || ''
    }

    function permitted() {
        try {
            const choice = JSON.parse(win.localStorage.getItem(CONSENT_KEY))
            return enabled && choice?.version === 2 && choice.analytics === true
                && Number.isFinite(choice.expiresAt) && choice.expiresAt > Date.now()
                && cookie(CONSENT_COOKIE) === 'granted'
        } catch { return false }
    }

    function publicUrl(value) {
        try {
            const actual = new URL(win.location.href)
            const url = new URL(value, actual)
            if (!/^https?:$/.test(url.protocol) || url.origin !== actual.origin || !isPublicPath(url.pathname)) return null
            const query = new URLSearchParams()
            for (const key of campaignKeys) {
                if (url.searchParams.has(key)) query.set(key, url.searchParams.get(key).slice(0, 100))
            }
            return url.pathname + (query.size ? `?${query}` : '')
        } catch { return null }
    }

    function origin(value) {
        try {
            const url = new URL(value)
            return /^https?:$/.test(url.protocol) ? url.origin : ''
        } catch { return '' }
    }

    function eventId() {
        try {
            const value = win.crypto?.randomUUID?.()
            if (uuidPattern.test(value || '')) return value
        } catch { /* Older secure contexts can still provide getRandomValues. */ }
        try {
            const bytes = win.crypto.getRandomValues(new Uint8Array(16))
            bytes[6] = (bytes[6] & 0x0f) | 0x40
            bytes[8] = (bytes[8] & 0x3f) | 0x80
            const hex = Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0'))
            return `${hex.slice(0, 4).join('')}-${hex.slice(4, 6).join('')}-${hex.slice(6, 8).join('')}-${hex.slice(8, 10).join('')}-${hex.slice(10).join('')}`
        } catch { return null }
    }

    function send(payload) {
        if (submit) return submit(payload)
        // Use Laravel's existing first-party CSRF protection, including after
        // an Inertia response rotates the cookie. Never use sendBeacon here.
        const token = decodeURIComponent(cookie('XSRF-TOKEN'))
        if (!token || typeof win.fetch !== 'function') return
        return win.fetch('/analytics/page-view', {
            method: 'POST', credentials: 'same-origin', cache: 'no-store',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-XSRF-TOKEN': token },
            body: JSON.stringify(payload),
        })
    }

    function record() {
        if (!displayed || displayed.attempted || !permitted()
            || displayed.url !== publicUrl(win.location.href)) return
        const id = eventId()
        if (!id) return
        // One attempt per displayed page, even when a request fails. There is
        // no background retry, persistent visitor ID or pre-consent queue.
        displayed.attempted = true
        try {
            Promise.resolve(send({ event_id: id, url: displayed.url, referrer: displayed.referrer })).catch(() => {})
        } catch { /* Statistics must never affect navigation or enquiry forms. */ }
    }

    function configure(allowed, renderedUrl = win.location.href) {
        enabled = allowed === true
        if (!enabled) {
            displayed = null
            return
        }
        const url = publicUrl(renderedUrl)
        if (!url || url !== publicUrl(win.location.href)) {
            enabled = false
            return
        }
        if (displayed?.url !== url) {
            displayed = {
                url, attempted: false,
                // Only the landing view uses the external referring origin.
                // Subsequent pages are internal navigation, including pages
                // reached before the visitor eventually gives consent.
                referrer: hasDisplayedPublicPage ? origin(win.location.href) : origin(doc.referrer),
            }
            hasDisplayedPublicPage = true
        }
        record()
    }

    function pause() {
        enabled = false
    }

    win.addEventListener(CONSENT_EVENT, record)
    win.addEventListener('storage', (event) => {
        if (event.key === CONSENT_KEY || event.key === null) record()
    })

    return { configure, pause }
}

export const pageViews = typeof window !== 'undefined' ? createPageViews(window, document) : null
