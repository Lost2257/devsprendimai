import { test } from 'node:test'
import assert from 'node:assert/strict'
import { createAnalytics, CONSENT_KEY, isPublicPath } from '../../resources/js/lib/analytics.js'

const id = 'G-LYW20LSJG9'
function browser(saved = null, blockedStorage = false) {
    const scripts = [], cookies = [], events = {}, storage = new Map()
    if (saved) storage.set(CONSENT_KEY, JSON.stringify(saved))
    let reloads = 0
    const win = {
        location: { hostname: 'devsprendimai.lt', pathname: '/', reload: () => reloads++ },
        localStorage: {
            getItem: (key) => { if (blockedStorage) throw new Error('Blocked'); return storage.get(key) || null },
            setItem: (key, value) => { if (blockedStorage) throw new Error('Blocked'); storage.set(key, value) },
        },
        CustomEvent: class { constructor(type) { this.type = type } },
        addEventListener: (name, handler) => events[name] = handler,
        dispatchEvent: (event) => events[event.type]?.(event),
    }
    const doc = {
        createElement: () => ({}), head: { appendChild: (node) => scripts.push(node) },
        get cookie() { return '_ga=123; _ga_LYW20LSJG9=abc; XSRF-TOKEN=safe' },
        set cookie(value) { cookies.push(value) },
    }
    const analytics = createAnalytics(win, doc)
    return { analytics, win, scripts, cookies, storage, events, reloads: () => reloads,
        commands: () => (win.dataLayer || []).map((item) => [...item]) }
}
const stored = (analytics, expiresAt = Date.now() + 100000) => ({ version: 1, analytics, expiresAt })

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
    assert.equal(commands.filter(([type]) => type === 'event').length, 0)
    assert.equal(commands.at(-1)[2].allow_google_signals, false)
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
    for (const saved of [stored(true, Date.now() - 1), { version: 99, analytics: true }, { version: 1, analytics: 'yes', expiresAt: Date.now() + 10000 }]) {
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
})

test('withdrawal in another tab disables this tab too', () => {
    const b = browser(stored(true))
    b.analytics.configure(id)
    b.storage.set(CONSENT_KEY, JSON.stringify(stored(false)))
    b.events.storage({ key: CONSENT_KEY })
    assert.equal(b.win[`ga-disable-${id}`], true)
    assert.equal(b.reloads(), 1)
})
