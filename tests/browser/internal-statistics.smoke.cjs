/* Consent/render-based dashboard-statistics integration checks.
 * Explicit STATS_SMOKE_URL required. By default EVERY analytics POST is mocked,
 * all other writes and third parties are blocked: safe for a live deployment.
 * STATS_SMOKE_WRITE=1 permits ONLY this endpoint on localhost, for an isolated
 * disposable test database. Never enable it against an ordinary development DB.
 * Requires Playwright with Edge installed (or available through NODE_PATH).
 */
const assert = require('node:assert/strict')
const { chromium } = require('playwright')

const target = process.env.STATS_SMOKE_URL
assert(target, 'Explicit STATS_SMOKE_URL is required')
const base = new URL(target)
assert(['http:', 'https:'].includes(base.protocol), 'HTTP(S) target required')
assert(!base.username && !base.password, 'Do not provide credentials in the target URL')
assert(base.pathname === '/' && !base.search && !base.hash, 'Provide only the site origin')
const realWrites = process.env.STATS_SMOKE_WRITE === '1'
assert(!realWrites || ['localhost', '127.0.0.1', '[::1]'].includes(base.hostname), 'Real statistics writes are restricted to localhost')
const endpoint = '/analytics/page-view'
const uuidV4 = /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i
const campaignKeys = new Set(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'])
const privateMarker = 'private-smoke@example.test'

async function waitForCount(events, expected, message) {
    for (let iteration = 0; events.length < expected && iteration < 100; iteration++) {
        await new Promise(resolve => setTimeout(resolve, 100))
    }
    assert.equal(events.length, expected, message)
}

function validateEvent(request, event) {
    assert.equal(request.method(), 'POST')
    assert.deepEqual(Object.keys(event).sort(), ['event_id', 'referrer', 'url'], 'Only allowlisted event fields are sent')
    assert.match(event.event_id, uuidV4, 'Each displayed page receives an idempotency UUID')
    assert(event.url.startsWith('/') && !event.url.startsWith('//'), 'Only relative public page URLs are sent')
    const url = new URL(event.url, base.origin)
    assert.equal(url.origin, base.origin)
    assert.equal(url.hash, '', 'Fragments are never collected')
    for (const key of url.searchParams.keys()) assert(campaignKeys.has(key), 'Non-campaign query parameters are excluded')
    assert(!JSON.stringify(event).includes(privateMarker), 'Sensitive test URL/referrer content is not sent')
    assert(!event.url.includes('secret-token'), 'Arbitrary query parameters are not sent')
    if (event.referrer) {
        const referrer = new URL(event.referrer)
        assert(['http:', 'https:'].includes(referrer.protocol))
        assert.equal(referrer.pathname, '/', 'Referrer path is excluded')
        assert.equal(referrer.search, '', 'Referrer query is excluded')
        assert.equal(referrer.hash, '', 'Referrer fragment is excluded')
        assert(!referrer.username && !referrer.password)
    }
    const headers = request.headers()
    assert(headers['x-xsrf-token'] || headers['x-csrf-token'], 'Same-origin tracking retains CSRF protection')
}

async function main() {
    const browser = await chromium.launch({ headless: true, channel: 'msedge' })
    try {
        for (const { locale, width } of [{ locale: 'lt', width: 1440 }, { locale: 'en', width: 390 }]) {
            const context = await browser.newContext({
                viewport: { width, height: 1000 }, reducedMotion: 'reduce', serviceWorkers: 'block',
            })
            const events = [], endpointResponses = [], failures = [], pageErrors = [], serviceRequests = []
            let thirdParty = 0, otherWrites = 0
            const home = locale === 'en' ? '/en' : '/'
            const service = `${locale === 'en' ? '/en' : ''}/interneto-svetaines`
            const start = `${base.origin}${home}?utm_source=smoke&utm_medium=qa&email=${encodeURIComponent(privateMarker)}&token=secret-token#not-collected`
            // This value exists only inside the test document; never navigate to
            // an external site or send these synthetic private parameters there.
            await context.addInitScript(marker => {
                Object.defineProperty(document, 'referrer', {
                    configurable: true, get: () => `https://www.google.com/search?q=${marker}#private`,
                })
            }, privateMarker)
            await context.route('**/*', async route => {
                const request = route.request()
                const url = new URL(request.url())
                if (url.origin !== base.origin) {
                    thirdParty++
                    return route.abort('blockedbyclient')
                }
                if (url.pathname === endpoint && request.method() === 'POST') {
                    try {
                        const event = request.postDataJSON()
                        validateEvent(request, event)
                        events.push(event)
                    } catch (error) {
                        failures.push(error.message)
                        return route.abort('blockedbyclient')
                    }
                    return realWrites ? route.continue() : route.fulfill({ status: 204 })
                }
                if (!['GET', 'HEAD'].includes(request.method())) {
                    otherWrites++
                    return route.abort('blockedbyclient')
                }
                if (url.pathname === service && request.headers()['x-inertia']) {
                    serviceRequests.push({ purpose: request.headers().purpose || request.headers()['sec-purpose'] || '' })
                }
                return route.continue()
            })
            const page = await context.newPage()
            page.setDefaultTimeout(15000)
            page.setDefaultNavigationTimeout(45000)
            page.on('pageerror', error => pageErrors.push(error.message))
            page.on('response', response => {
                if (new URL(response.url()).pathname === endpoint && response.request().method() === 'POST') endpointResponses.push(response.status())
            })
            try {
                await page.goto(start, { waitUntil: 'networkidle' })
                const initial = JSON.parse(await page.locator('#app').getAttribute('data-page'))
                assert.equal(initial.props.analytics?.pageViews, true, 'Target must expose the new production public-guest statistics feature before consent')
                assert.equal(events.length, 0, 'No internal event before consent')
                assert.equal(thirdParty, 0, 'No optional third-party request before consent')
                const panel = page.locator('.cookie-panel')
                await panel.waitFor({ state: 'visible' })
                const bounds = await panel.boundingBox()
                assert(bounds.x >= 0 && bounds.x + bounds.width <= width + 1 && bounds.y >= 0 && bounds.y + bounds.height <= 1001, 'Consent fits the viewport')
                for (const choice of await panel.locator('button').all()) assert((await choice.boundingBox()).height >= 44, 'Consent choices retain accessible touch targets')
                await page.getByRole('button', { name: locale === 'en' ? 'Allow analytics' : 'Leisti analitiką', exact: true }).click()
                await waitForCount(events, 1, 'Accepting consent records the already-displayed initial page once')
                assert.equal(new URL(events[0].url, base.origin).pathname, home)
                assert.equal(new URL(events[0].url, base.origin).searchParams.get('utm_source'), 'smoke')
                assert.equal(new URL(events[0].referrer).origin, 'https://www.google.com')

                await page.evaluate(() => { window.__statisticsDocument = 'same-inertia-document' })
                const link = page.locator(`a[href="${service}"]:visible`).first()
                await link.hover()
                await page.waitForTimeout(900)
                assert(serviceRequests.some(request => request.purpose.includes('prefetch')), 'A real hover prefetch was exercised')
                assert.equal(events.length, 1, 'Hover prefetch does not count as a displayed page')
                const requestsBeforeClick = serviceRequests.length
                await link.click()
                await page.waitForURL(url => url.pathname === service)
                await waitForCount(events, 2, 'Clicking the prefetched page records one actual view')
                await page.waitForTimeout(400)
                assert.equal(serviceRequests.length, requestsBeforeClick, 'The click used the prefetched response rather than a new GET')
                assert.equal(await page.evaluate(() => window.__statisticsDocument), 'same-inertia-document', 'Navigation stayed inside Inertia')
                assert.equal(new URL(events[1].url, base.origin).pathname, service)
                assert.equal(new URL(events[1].referrer).origin, base.origin, 'Internal navigation is not misattributed to the external landing source')

                await page.goBack({ waitUntil: 'networkidle' })
                await waitForCount(events, 3, 'Browser Back records the newly displayed page once')
                assert.equal(new URL(events[2].url, base.origin).pathname, home)
                await page.evaluate(() => window.dispatchEvent(new CustomEvent('devsprendimai:open-consent')))
                await page.getByRole('button', { name: locale === 'en' ? 'Allow analytics' : 'Leisti analitiką', exact: true }).click()
                await page.waitForTimeout(600)
                assert.equal(events.length, 3, 'Re-confirming consent/component updates cannot duplicate the current view')
                await page.reload({ waitUntil: 'networkidle' })
                await waitForCount(events, 4, 'Reload with persisted consent records one new display')
                assert.equal(new Set(events.map(event => event.event_id)).size, events.length, 'Distinct page displays use distinct idempotency keys')
                assert.equal(new URL(events[3].url, base.origin).pathname, home)
                // Google is blocked in every scenario, independently of whether
                // this deployment has a measurement ID configured.
                if (initial.props.analytics.measurementId) assert(thirdParty > 0, 'Google was actually blocked and did not break internal measurement')

                await page.evaluate(() => window.dispatchEvent(new CustomEvent('devsprendimai:open-consent')))
                await page.getByRole('button', { name: locale === 'en' ? 'Essential only' : 'Tik būtinieji', exact: true }).click()
                await page.waitForTimeout(700)
                await page.waitForLoadState('networkidle')
                await page.goto(`${base.origin}${service}`, { waitUntil: 'networkidle' })
                assert.equal(events.length, 4, 'Withdrawal and a subsequent public page send no event')
                await page.goto(`${base.origin}/login`, { waitUntil: 'networkidle' })
                const privatePage = JSON.parse(await page.locator('#app').getAttribute('data-page'))
                assert.equal(privatePage.props.analytics?.pageViews, false, 'Private pages cannot enable tracking')
                assert.equal(events.length, 4, 'Login is not tracked')
                assert.deepEqual(failures, [], 'Every event satisfies the privacy/CSRF contract')
                assert.deepEqual(pageErrors, [], 'No uncaught JavaScript errors')
                assert.equal(otherWrites, 0, 'No contact or other write workflow was attempted')
                assert.equal(endpointResponses.length, events.length, 'Every test event received a response')
                assert(endpointResponses.every(status => status === 204), 'Tracking endpoint consistently responds without page content')
                console.log(`PASS ${locale} ${width}px: consent, cached navigation, Back, dedupe, reload, withdrawal, private route, blocked Google; ${events.length} ${realWrites ? 'LOCAL FIXTURE' : 'MOCKED'} events`)
            } finally { await context.close() }
        }
    } finally { await browser.close() }
    console.log(realWrites
        ? 'Internal statistics passed against the explicitly enabled LOCAL test endpoint. Expected fixture rows: 8; inspect only the isolated test database.'
        : 'Internal statistics passed with all event POSTs mocked. No real analytics events, enquiries or emails were sent.')
}

main().catch(error => { console.error(error); process.exitCode = 1 })
