import { test } from 'node:test'
import assert from 'node:assert/strict'
import { createPageViews } from '../../resources/js/lib/pageViews.js'
import { CONSENT_COOKIE, CONSENT_EVENT, CONSENT_KEY } from '../../resources/js/lib/analytics.js'

const stored = (analytics, expiresAt = Date.now() + 100000) => ({ version: 2, analytics, expiresAt })
const uuid = /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/

function browser({ choice = null, href = 'https://devsprendimai.lt/', referrer = '', fetch: fetchOverride, crypto } = {}) {
    const storage = new Map(), cookies = new Map([['XSRF-TOKEN', 'csrf%2Ftoken%3D']]), listeners = new Map(), requests = []
    let ids = 0
    if (choice) {
        storage.set(CONSENT_KEY, JSON.stringify(choice))
        cookies.set(CONSENT_COOKIE, choice.analytics ? 'granted' : 'denied')
    }
    const win = {
        location: { href },
        crypto: crypto ?? { randomUUID: () => `00000000-0000-4000-8000-${String(++ids).padStart(12, '0')}` },
        localStorage: { getItem: (key) => storage.get(key) || null },
        addEventListener: (name, fn) => listeners.set(name, [...(listeners.get(name) || []), fn]),
        fetch: (url, options) => {
            requests.push({ url, options, payload: JSON.parse(options.body) })
            return fetchOverride ? fetchOverride(url, options) : Promise.resolve({ status: 204 })
        },
    }
    const doc = { referrer, get cookie() { return [...cookies].map(([key, value]) => `${key}=${value}`).join('; ') } }
    const emit = (type, event = {}) => (listeners.get(type) || []).forEach((fn) => fn(event))
    const tracker = createPageViews(win, doc)
    return {
        win, doc, tracker, storage, cookies, requests, emit,
        ids: () => ids,
        grant: () => {
            storage.set(CONSENT_KEY, JSON.stringify(stored(true)))
            cookies.set(CONSENT_COOKIE, 'granted')
            emit(CONSENT_EVENT)
        },
        navigate: (path, allowed = true) => {
            win.location.href = new URL(path, win.location.href).href
            tracker.configure(allowed, path)
        },
    }
}

test('no request or event identifier is created before consent, after refusal or for invalid choices', () => {
    for (const choice of [null, stored(false), stored(true, Date.now() - 1), { ...stored(true), version: 1 }, { ...stored(true), analytics: 'true' }, { ...stored(true), expiresAt: '9999999999999' }]) {
        const b = browser({ choice })
        b.tracker.configure(true, '/')
        b.emit(CONSENT_EVENT)
        b.navigate('/interneto-svetaines')
        assert.equal(b.requests.length, 0)
        assert.equal(b.ids(), 0)
    }
})

test('initial grant immediately records the rendered landing view exactly once', () => {
    const b = browser()
    b.tracker.configure(true, '/')
    b.grant()
    b.grant()
    b.tracker.configure(true, '/')
    b.emit('storage', { key: CONSENT_KEY })
    assert.equal(b.requests.length, 1)
    assert.match(b.requests[0].payload.event_id, uuid)
    assert.equal(b.requests[0].payload.url, '/')
})

test('returning consent records one initial view independently of any Google tag or ID', () => {
    const b = browser({ choice: stored(true) })
    b.win.gtag = () => { throw new Error('Google blocked') }
    b.tracker.configure(true, '/')
    b.tracker.configure(true, '/')
    assert.equal(b.requests.length, 1)
    assert.equal(b.win.dataLayer, undefined)
})

test('hover/prefetch does not record; displaying cached pages, Back and Forward each records once', () => {
    const b = browser({ choice: stored(true) })
    b.tracker.configure(true, '/')
    b.emit('prefetching', { url: '/interneto-svetaines' })
    b.emit('prefetched', { url: '/interneto-svetaines' })
    assert.equal(b.requests.length, 1)
    b.navigate('/interneto-svetaines')
    b.tracker.configure(true, '/interneto-svetaines')
    b.navigate('/')
    b.navigate('/interneto-svetaines')
    assert.deepEqual(b.requests.map(({ payload }) => payload.url), ['/', '/interneto-svetaines', '/', '/interneto-svetaines'])
    assert.equal(new Set(b.requests.map(({ payload }) => payload.event_id)).size, 4)
})

test('new history URL cannot be recorded with an old rendered page, including during consent changes', () => {
    const b = browser()
    b.tracker.configure(true, '/')
    b.win.location.href = 'https://devsprendimai.lt/interneto-svetaines'
    b.grant()
    b.tracker.configure(true, '/')
    assert.equal(b.requests.length, 0)
    b.tracker.configure(true, '/interneto-svetaines')
    assert.equal(b.requests.length, 1)
    assert.equal(b.requests[0].payload.url, '/interneto-svetaines')
})

test('grant on a later page never replays unconsented views or the landing attribution', () => {
    const b = browser({ href: 'https://devsprendimai.lt/?utm_source=google', referrer: 'https://www.google.com/search?q=private' })
    b.tracker.configure(true, '/?utm_source=google')
    b.navigate('/interneto-svetaines')
    b.navigate('/en')
    b.grant()
    assert.deepEqual(b.requests.map(({ payload }) => ({ url: payload.url, referrer: payload.referrer })), [
        { url: '/en', referrer: 'https://devsprendimai.lt' },
    ])
})

test('landing allows only bounded campaign fields and referring origin, never unrelated query or fragment data', () => {
    const source = 'x'.repeat(150)
    const b = browser({
        choice: stored(true),
        href: `https://devsprendimai.lt/?utm_source=${source}&utm_medium=search&utm_campaign=autumn&utm_term=web&utm_content=one&email=private%40example.com&token=secret#private`,
        referrer: 'https://user:password@www.google.com/search?q=private#secret',
    })
    b.tracker.configure(true, b.win.location.href)
    const payload = b.requests[0].payload
    assert.deepEqual(Object.keys(payload).sort(), ['event_id', 'referrer', 'url'])
    assert.equal(payload.referrer, 'https://www.google.com')
    assert.equal(payload.url, `/?utm_source=${'x'.repeat(100)}&utm_medium=search&utm_campaign=autumn&utm_term=web&utm_content=one`)
    assert(!JSON.stringify(payload).includes('private'))
    b.navigate('/el-parduotuves')
    assert.equal(b.requests[1].payload.referrer, 'https://devsprendimai.lt')
    assert.equal(b.requests[1].payload.url, '/el-parduotuves')
})

test('hash changes and unrelated query changes never create duplicate views', () => {
    const b = browser({ choice: stored(true) })
    b.tracker.configure(true, '/')
    b.navigate('/#contact')
    b.navigate('/?email=private@example.com#projects')
    assert.equal(b.requests.length, 1)
})

test('missing production/guest permission, private pages and cross-origin targets do not record', () => {
    for (const path of ['/login', '/dashboard', '/dashboard/projects/14/edit', '/reset-password/private-token', '/en/login']) {
        const b = browser({ choice: stored(true), href: `https://devsprendimai.lt${path}` })
        b.tracker.configure(true, path)
        b.grant()
        assert.equal(b.requests.length, 0)
    }
    for (const allowed of [false, undefined, null, 1, 'true']) {
        const b = browser({ choice: stored(true) })
        b.tracker.configure(allowed, '/')
        b.grant()
        assert.equal(b.requests.length, 0)
    }
    const b = browser({ choice: stored(true) })
    b.tracker.configure(true, 'https://other.example/')
    assert.equal(b.requests.length, 0)
})

test('private navigation pauses collection immediately and only a later rendered public page resumes it', () => {
    const b = browser()
    b.tracker.configure(true, '/')
    b.tracker.pause()
    b.grant()
    assert.equal(b.requests.length, 0)
    b.navigate('/login', false)
    b.grant()
    assert.equal(b.requests.length, 0)
    b.navigate('/')
    assert.equal(b.requests.length, 1)
    b.navigate('/login', false)
    b.navigate('/')
    assert.equal(b.requests.length, 2)
})

test('cancelled private navigation followed by rerender does not duplicate a measured page', () => {
    const b = browser({ choice: stored(true) })
    b.tracker.configure(true, '/')
    b.tracker.pause()
    b.tracker.configure(true, '/')
    assert.equal(b.requests.length, 1)
})

test('withdrawal, cleared storage, missing preference cookie and expired consent gate every send', (t) => {
    const now = Date.now()
    const b = browser({ choice: stored(true, now + 100) })
    b.tracker.configure(true, '/')
    b.storage.set(CONSENT_KEY, JSON.stringify(stored(false)))
    b.emit('storage', { key: CONSENT_KEY })
    b.navigate('/en')
    assert.equal(b.requests.length, 1)
    b.storage.clear()
    b.emit('storage', { key: null })
    b.navigate('/interneto-svetaines')
    assert.equal(b.requests.length, 1)
    b.storage.set(CONSENT_KEY, JSON.stringify(stored(true, now + 100)))
    b.cookies.delete(CONSENT_COOKIE)
    b.emit(CONSENT_EVENT)
    assert.equal(b.requests.length, 1)
    b.cookies.set(CONSENT_COOKIE, 'granted')
    t.mock.method(Date, 'now', () => now + 200)
    b.emit(CONSENT_EVENT)
    b.navigate('/en/interneto-svetaines')
    assert.equal(b.requests.length, 1)
})

test('cross-tab affirmative consent records the current page once without replaying earlier pages', () => {
    const b = browser()
    b.tracker.configure(true, '/')
    b.navigate('/en')
    b.storage.set(CONSENT_KEY, JSON.stringify(stored(true)))
    b.cookies.set(CONSENT_COOKIE, 'granted')
    b.emit('storage', { key: CONSENT_KEY })
    b.emit(CONSENT_EVENT)
    assert.deepEqual(b.requests.map(({ payload }) => payload.url), ['/en'])
})

test('malformed or blocked storage cannot enable collection', () => {
    const b = browser({ choice: stored(true) })
    b.storage.set(CONSENT_KEY, 'not JSON')
    assert.doesNotThrow(() => b.tracker.configure(true, '/'))
    b.win.localStorage.getItem = () => { throw new Error('Blocked') }
    assert.doesNotThrow(() => b.grant())
    assert.equal(b.requests.length, 0)
})

test('requests use same-origin credentials and decoded current CSRF cookie', () => {
    const b = browser({ choice: stored(true) })
    b.tracker.configure(true, '/')
    const request = b.requests[0]
    assert.equal(request.url, '/analytics/page-view')
    assert.equal(request.options.method, 'POST')
    assert.equal(request.options.credentials, 'same-origin')
    assert.equal(request.options.cache, 'no-store')
    assert.deepEqual(request.options.headers, { 'Content-Type': 'application/json', Accept: 'application/json', 'X-XSRF-TOKEN': 'csrf/token=' })
    b.cookies.set('XSRF-TOKEN', 'rotated%3D')
    b.navigate('/en')
    assert.equal(b.requests[1].options.headers['X-XSRF-TOKEN'], 'rotated=')
})

test('missing or malformed CSRF tokens skip requests without disrupting page rendering', () => {
    for (const value of ['', '%invalid']) {
        const b = browser({ choice: stored(true) })
        b.cookies.set('XSRF-TOKEN', value)
        assert.doesNotThrow(() => b.tracker.configure(true, '/'))
        assert.equal(b.requests.length, 0)
    }
})

test('failed, rejected and throwing requests do not break UI or retry the same view', async () => {
    for (const fetch of [() => { throw new Error('Offline') }, () => Promise.reject(new Error('Blocked')), () => Promise.resolve({ status: 500 })]) {
        const b = browser({ choice: stored(true), fetch })
        assert.doesNotThrow(() => b.tracker.configure(true, '/'))
        b.tracker.configure(true, '/')
        b.emit(CONSENT_EVENT)
        await Promise.resolve()
        assert.equal(b.requests.length, 1)
        b.navigate('/en')
        await Promise.resolve()
        assert.equal(b.requests.length, 2)
    }
})

test('event identifiers use cryptographic randomness only, with secure UUID fallback', () => {
    const b = browser({ choice: stored(true), crypto: { getRandomValues: (array) => array.fill(255) } })
    b.tracker.configure(true, '/')
    assert.match(b.requests[0].payload.event_id, uuid)
    assert.equal(b.requests[0].payload.event_id, 'ffffffff-ffff-4fff-bfff-ffffffffffff')
    const unavailable = browser({ choice: stored(true), crypto: {} })
    unavailable.tracker.configure(true, '/')
    assert.equal(unavailable.requests.length, 0)
})

test('non-HTTP or invalid referring addresses never enter the payload', () => {
    for (const referrer of ['not a URL', 'file:///private/path', 'javascript:secret()', '']) {
        const b = browser({ choice: stored(true), referrer })
        b.tracker.configure(true, '/')
        assert.equal(b.requests[0].payload.referrer, '')
    }
})
