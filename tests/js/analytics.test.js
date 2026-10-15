import { test } from 'node:test'
import assert from 'node:assert/strict'
import { createAnalytics, CONSENT_KEY, CONSENT_COOKIE, isPublicPath } from '../../resources/js/lib/analytics.js'

const id = 'G-LYW20LSJG9'
function browser(saved = null, blockedStorage = false) {
    const scripts = [], cookies = [], events = {}, storage = new Map()
    if (saved) storage.set(CONSENT_KEY, JSON.stringify(saved))
    let reloads = 0
    const win = {
        location: { protocol: 'https:', hostname: 'devsprendimai.lt', pathname: '/', href: 'https://devsprendimai.lt/', reload: () => reloads++ },
        localStorage: {
            getItem: (key) => { if (blockedStorage) throw new Error('Blocked'); return storage.get(key) || null },
            setItem: (key, value) => { if (blockedStorage) throw new Error('Blocked'); storage.set(key, value) },
            removeItem: (key) => { if (blockedStorage) throw new Error('Blocked'); storage.delete(key) },
        },
        CustomEvent: class { constructor(type) { this.type = type } },
        addEventListener: (name, handler) => events[name] = handler,
        dispatchEvent: (event) => events[event.type]?.(event),
    }
    const doc = {
        title: 'DevSprendimai', referrer: '',
        createElement: () => ({}), head: { appendChild: (node) => scripts.push(node) },
        get cookie() { return '_ga=123; _ga_LYW20LSJG9=abc; XSRF-TOKEN=safe' },
        set cookie(value) { cookies.push(value) },
    }
    const analytics = createAnalytics(win, doc)
    return { analytics, win, scripts, cookies, storage, events, reloads: () => reloads,
        doc, commands: () => (win.dataLayer || []).map((item) => [...item]) }
}
const stored = (analytics, expiresAt = Date.now() + 100000) => ({ version: 2, analytics, expiresAt })

test('no Google requests before choice, after refusal, or on repeat refused visits', () => {
    const b = browser()
    b.analytics.configure(id)
    assert.equal(b.scripts.length, 0)
    b.analytics.setConsent(false)
    b.analytics.configure(id)
    assert.equal(b.scripts.length, 0)
    assert.equal(b.win.gtag, undefined)
    const returning = browser(JSON.parse(b.storage.get(CONSENT_KEY)))
    returning.analytics.configure(id)
    assert.equal(returning.scripts.length, 0)
})

test('affirmative consent loads tag once and sets consent before configuration', () => {
    const b = browser()
    b.analytics.configure(id)
    b.analytics.setConsent(true)
    b.win.location.pathname = '/en/interneto-svetaines'
    b.win.location.href = 'https://devsprendimai.lt/en/interneto-svetaines'
    b.analytics.configure(id)
    b.analytics.setConsent(true)
    assert.equal(b.scripts.length, 1)
    assert.equal(b.scripts[0].src, `https://www.googletagmanager.com/gtag/js?id=${id}`)
    const commands = b.commands()
    assert.equal(commands[0][0], 'consent')
    assert.equal(commands[0][2].analytics_storage, 'denied')
    assert.equal(commands[1][2].analytics_storage, 'granted')
    assert.equal(commands[1][2].ad_storage, 'denied')
    assert.equal(commands.filter(([type]) => type === 'config').length, 1)
    assert.equal(commands.filter(([type]) => type === 'event').length, 2)
    assert.equal(commands.find(([type]) => type === 'config')[2].allow_google_signals, false)
    assert.equal(commands.find(([type]) => type === 'config')[2].send_page_view, false)
})

test('revocation stops collection, clears only analytics cookies and unloads tag', () => {
    const b = browser(stored(true))
    b.analytics.configure(id)
    b.analytics.setConsent(false)
    assert.equal(b.win[`ga-disable-${id}`], true)
    assert.equal(b.reloads(), 1)
    assert.equal(b.analytics.getConsent(), false)
    assert(b.cookies.some((cookie) => cookie.startsWith('_ga=;')))
    assert(!b.cookies.some((cookie) => cookie.startsWith('XSRF-TOKEN')))
})

test('private pages and missing configuration never initialise analytics', () => {
    for (const path of ['/login', '/dashboard/projects/14/edit', '/reset-password/token', '/en/login']) {
        const b = browser(stored(true))
        b.win.location.pathname = path
        b.analytics.configure(id)
        assert.equal(b.scripts.length, 0)
        assert.equal(isPublicPath(path), false)
    }
    const b = browser(stored(true))
    b.analytics.configure(null)
    assert.equal(b.scripts.length, 0)
    b.analytics.configure(id)
    b.analytics.configure(null)
    assert.equal(b.win[`ga-disable-${id}`], true)
})

test('expired or malformed stored choices do not activate analytics', () => {
    for (const saved of [stored(true, Date.now() - 1), { version: 99, analytics: true }, { version: 1, analytics: true, expiresAt: Date.now() + 10000 }, { version: 2, analytics: 'yes', expiresAt: Date.now() + 10000 }]) {
        const b = browser(saved)
        b.analytics.configure(id)
        assert.equal(b.analytics.getConsent(), null)
        assert.equal(b.scripts.length, 0)
    }
})

test('storage restrictions preserve a working in-memory choice', () => {
    const b = browser(null, true)
    b.analytics.configure(id)
    assert.equal(b.scripts.length, 0)
    b.analytics.setConsent(true)
    assert.equal(b.scripts.length, 1)
    assert.equal(b.analytics.getConsent(), true)
    assert(b.cookies.filter((cookie) => cookie.startsWith(CONSENT_COOKIE)).every((cookie) => cookie.includes('Max-Age=0')))
})

test('server-side analytics preference follows explicit consent and expiration', () => {
    const b = browser()
    b.analytics.configure(id)
    assert(b.cookies.at(-1).includes('Max-Age=0'))
    b.analytics.setConsent(true)
    const granted = b.cookies.findLast((cookie) => cookie.startsWith(CONSENT_COOKIE))
    assert(granted.startsWith(`${CONSENT_COOKIE}=granted;`))
    assert(granted.includes('SameSite=Lax; Secure'))
    assert(Number(granted.match(/Max-Age=(\d+)/)[1]) <= 180 * 24 * 60 * 60)
    b.analytics.setConsent(false)
    assert(b.cookies.findLast((cookie) => cookie.startsWith(CONSENT_COOKIE)).startsWith(`${CONSENT_COOKIE}=denied;`))
    const expired = browser(stored(true, Date.now() - 1))
    expired.analytics.configure(id)
    assert(expired.cookies.find((cookie) => cookie.startsWith(CONSENT_COOKIE)).includes('Max-Age=0'))
})

test('withdrawal in another tab disables this tab too', () => {
    const b = browser(stored(true))
    b.analytics.configure(id)
    b.storage.set(CONSENT_KEY, JSON.stringify(stored(false)))
    b.events.storage({ key: CONSENT_KEY })
    assert.equal(b.win[`ga-disable-${id}`], true)
    assert.equal(b.reloads(), 1)
})

test('page views use the new title, preserve attribution and redact unrelated query fields', () => {
    const b = browser(stored(true))
    b.win.location.href = 'https://devsprendimai.lt/?utm_source=google&email=private%40example.com#contact'
    b.doc.referrer = 'https://google.com/search?q=private'
    b.analytics.configure(id)
    b.win.location.href = 'https://devsprendimai.lt/interneto-svetaines'
    b.doc.title = 'Old title pending Inertia Head update'
    b.analytics.configure(id, 'Interneto svetainės')
    b.analytics.configure(id)
    const views = b.commands().filter(([type, name]) => type === 'event' && name === 'page_view')
    assert.equal(views.length, 2)
    assert.equal(views[0][2].page_location, 'https://devsprendimai.lt/?utm_source=google')
    assert.equal(views[0][2].page_referrer, 'https://google.com/search')
    assert.equal(views[1][2].page_title, 'Interneto svetainės')
    assert.equal(views[1][2].page_referrer, 'https://devsprendimai.lt/?utm_source=google')
})

const leadMarker = (overrides = {}) => ({ id: 'b3ce4c48-dc1e-43b2-9070-d3a2ab67a995', form_id: 'project_enquiry', locale: 'lt', ...overrides })
const publicPage = { analytics: { measurementId: id }, auth: { user: null } }
const leadEvents = (b) => b.commands().filter(([type, name]) => type === 'event' && name === 'generate_lead')

test('one saved enquiry emits one generate_lead with fixed privacy-safe parameters', () => {
    const b = browser(stored(true))
    b.analytics.configure(id)
    const complete = b.analytics.beginLeadSubmission()
    const marker = leadMarker({ name: 'Private name', email: 'secret@example.com', service: 'arbitrary service', message: 'private message' })
    assert.equal(complete(marker, publicPage), true)
    assert.equal(complete(marker, publicPage), false)
    assert.equal(b.analytics.beginLeadSubmission()(marker, publicPage), false)
    assert.deepEqual(leadEvents(b), [['event', 'generate_lead', { form_id: 'project_enquiry', language: 'lt', send_to: id }]])
    b.win.location.pathname = '/en'
    b.win.location.href = 'https://devsprendimai.lt/en'
    assert.equal(b.analytics.beginLeadSubmission()(leadMarker({ id: 'f670416d-c8af-433c-837b-7d5c4a3c6cfe', locale: 'en' }), publicPage), true)
    assert.equal(leadEvents(b)[1][2].language, 'en')
})

test('no consent, refusal, expiration and delayed opt-in never replay enquiries', () => {
    for (const choice of [null, stored(false), stored(true, Date.now() - 1)]) {
        const b = browser(choice)
        b.analytics.configure(id)
        const complete = b.analytics.beginLeadSubmission()
        b.analytics.setConsent(true)
        assert.equal(complete(leadMarker(), publicPage), false)
        assert.equal(b.analytics.beginLeadSubmission()(leadMarker(), publicPage), false)
        assert.equal(leadEvents(b).length, 0)
    }
    const b = browser(stored(true))
    b.analytics.configure(id)
    const complete = b.analytics.beginLeadSubmission()
    b.analytics.setConsent(false)
    b.analytics.setConsent(true)
    assert.equal(complete(leadMarker(), publicPage), false)
    assert.equal(leadEvents(b).length, 0)
})

test('lead completion rejects missing, malformed, arbitrary form and locale markers', () => {
    const b = browser(stored(true))
    b.analytics.configure(id)
    for (const marker of [null, {}, leadMarker({ id: '1' }), leadMarker({ form_id: 'secret@example.com' }), leadMarker({ locale: 'secret@example.com' })]) {
        assert.equal(b.analytics.beginLeadSubmission()(marker, publicPage), false)
    }
    assert.equal(leadEvents(b).length, 0)
})

test('private or signed-in responses and paused configuration cannot create leads', () => {
    for (const response of [{}, { ...publicPage, auth: { user: { id: 1 } } }, { analytics: { measurementId: null } }]) {
        const b = browser(stored(true))
        b.analytics.configure(id)
        assert.equal(b.analytics.beginLeadSubmission()(leadMarker(), response), false)
        assert.equal(leadEvents(b).length, 0)
    }
    const b = browser(stored(true))
    b.analytics.configure(id)
    const complete = b.analytics.beginLeadSubmission()
    b.win.location.pathname = '/login'
    assert.equal(complete(leadMarker(), publicPage), false)
    b.win.location.pathname = '/'
    const paused = b.analytics.beginLeadSubmission()
    b.analytics.pause()
    assert.equal(paused(leadMarker({ id: 'f670416d-c8af-433c-837b-7d5c4a3c6cfe' }), publicPage), false)
    assert.equal(leadEvents(b).length, 0)
})

test('cross-tab withdrawal and regrant cancel an in-flight enquiry conversion', () => {
    const b = browser(stored(true))
    b.analytics.configure(id)
    const complete = b.analytics.beginLeadSubmission()
    b.storage.set(CONSENT_KEY, JSON.stringify(stored(false)))
    b.events.storage({ key: CONSENT_KEY })
    b.storage.set(CONSENT_KEY, JSON.stringify(stored(true)))
    b.events.storage({ key: CONSENT_KEY })
    assert.equal(complete(leadMarker(), publicPage), false)
    assert.equal(leadEvents(b).length, 0)
})

test('consent expiring while an enquiry is pending does not track its success', (t) => {
    const now = Date.now()
    const b = browser(stored(true, now + 100))
    b.analytics.configure(id)
    const complete = b.analytics.beginLeadSubmission()
    t.mock.method(Date, 'now', () => now + 200)
    assert.equal(complete(leadMarker(), publicPage), false)
    assert.equal(leadEvents(b).length, 0)
})

test('blocked or throwing Google code cannot throw from enquiry completion', () => {
    const b = browser(stored(true))
    b.analytics.configure(id)
    const complete = b.analytics.beginLeadSubmission()
    b.win.gtag = () => { throw new Error('Blocked analytics') }
    assert.equal(complete(leadMarker(), publicPage), false)
    assert.equal(complete(leadMarker(), publicPage), false)
})
