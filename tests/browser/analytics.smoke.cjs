// Opt-in production smoke test; accepting analytics sends a few real test page views.
// Requires Playwright. Run with GA_SMOKE_URL=https://devsprendimai.lt.
const { chromium } = require('playwright')
const assert = require('node:assert/strict')
const { mkdtemp } = require('node:fs/promises')
const { tmpdir } = require('node:os')
const { join } = require('node:path')

async function main() {
    assert(process.env.GA_SMOKE_URL, 'Explicit GA_SMOKE_URL required (this can send real GA events)')
    const base = process.env.GA_SMOKE_URL
    const output = await mkdtemp(join(tmpdir(), 'devsprendimai-analytics-'))
    const browser = await chromium.launch({ headless: true, channel: 'msedge' })
    try {
        const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } })
        const page = await context.newPage()
        const google = [], hits = [], errors = []
        const waitForHit = async (predicate, timeout = 15000) => {
            const deadline = Date.now() + timeout
            while (!hits.some(predicate) && Date.now() < deadline) await page.waitForTimeout(200)
        }
        page.on('pageerror', (error) => errors.push(error.message))
        page.on('request', (request) => {
            const url = new URL(request.url())
            if (/google-analytics\.com|googletagmanager\.com|analytics\.google\.com/.test(url.hostname)) google.push(request.url())
            if (!url.pathname.endsWith('/collect')) return
            const body = request.postData() || ''
            for (const line of body.split('\n')) {
                const params = new URLSearchParams(url.search)
                for (const [key, value] of new URLSearchParams(line)) params.set(key, value)
                if (params.get('en')) hits.push({ event: params.get('en'), id: params.get('tid'), path: params.get('dl'), title: params.get('dt') })
            }
        })
        await page.goto(base, { waitUntil: 'networkidle' })
        await page.getByRole('button', { name: 'Tik būtinieji', exact: true }).waitFor()
        assert.equal(google.length, 0, 'No Google requests before consent')
        await page.screenshot({ path: join(output, 'desktop.png') })
        await page.getByRole('button', { name: 'Tik būtinieji', exact: true }).click()
        await page.reload({ waitUntil: 'networkidle' })
        assert.equal(await page.locator('.cookie-panel').count(), 0, 'Refusal is remembered')
        assert.equal(google.length, 0, 'No Google requests after refusal')
        console.log('PASS before-consent and persisted refusal: zero Google requests')

        await page.getByRole('button', { name: 'Slapukų nustatymai', exact: true }).click()
        await page.getByRole('button', { name: 'Leisti analitiką', exact: true }).click()
        await waitForHit((hit) => hit.event === 'page_view')
        console.log('INITIAL_HITS', JSON.stringify(hits))
        assert.equal(hits.filter((hit) => hit.event === 'page_view').length, 1, 'One initial page view')
        assert(hits.every((hit) => hit.id === 'G-LYW20LSJG9'), 'Correct GA4 stream')
        const gaCookies = (await context.cookies()).filter((cookie) => cookie.name.startsWith('_ga'))
        assert(gaCookies.length > 0, 'GA cookies created after consent')
        assert(gaCookies.every((cookie) => cookie.expires <= Date.now() / 1000 + 181 * 86400), 'Cookie lifetime <= 180 days')

        const before = hits.filter((hit) => hit.event === 'page_view').length
        await page.locator('a[href="/interneto-svetaines"]').first().click()
        await page.waitForURL('**/interneto-svetaines')
        const navigationStarted = Date.now()
        // GA may batch an already-enqueued event; wait on the actual matching hit.
        await waitForHit((hit) => hit.event === 'page_view' && new URL(hit.path).pathname === '/interneto-svetaines')
        console.log('NAVIGATION_HIT_WAIT_MS', Date.now() - navigationStarted)
        console.log('NAVIGATION_HITS', JSON.stringify(hits))
        assert.equal(hits.filter((hit) => hit.event === 'page_view').length, before + 1, 'One page view per Inertia navigation')
        const serviceView = hits.filter((hit) => hit.event === 'page_view').at(-1)
        assert.equal(new URL(serviceView.path).pathname, '/interneto-svetaines')
        assert.equal(serviceView.title, await page.title(), 'SPA view uses updated page title')
        await page.goBack()
        await page.waitForTimeout(6000)
        const manualViews = await page.evaluate(() => window.dataLayer
            .filter((entry) => entry[0] === 'event' && entry[1] === 'page_view')
            .map((entry) => entry[2]))
        assert.equal(manualViews.length, before + 2, 'Application sends one view per navigation, including Back')
        const backCount = hits.filter((hit) => hit.event === 'page_view').length
        if (backCount !== before + 2) {
            console.warn('GA STREAM SETTING REQUIRED: disable Page changes based on browser history events')
            console.warn('BACK_HITS', JSON.stringify(hits))
        }
        console.log('PASS consent, correct stream, cookie lifetime and application SPA/back page views')

        await page.getByRole('button', { name: 'Slapukų nustatymai', exact: true }).click()
        await Promise.all([
            page.waitForEvent('load'),
            page.getByRole('button', { name: 'Tik būtinieji', exact: true }).click(),
        ])
        await page.waitForLoadState('networkidle')
        assert.equal((await context.cookies()).filter((cookie) => cookie.name.startsWith('_ga')).length, 0, 'Withdrawal removes GA cookies')
        const afterRevoke = google.length
        await page.locator('a[href="/interneto-svetaines"]').first().click()
        await page.waitForURL('**/interneto-svetaines')
        await page.waitForTimeout(2500)
        assert.equal(google.length, afterRevoke, 'Withdrawal stops requests on later navigation')
        console.log('PASS withdrawal removes cookies and stops collection')

        for (const [width, locale] of [[390, ''], [320, '/en']]) {
            const mobile = await browser.newContext({ viewport: { width, height: 844 }, isMobile: true })
            const tab = await mobile.newPage()
            await tab.goto(base + locale, { waitUntil: 'networkidle' })
            const panel = tab.locator('.cookie-panel')
            await panel.waitFor()
            const box = await panel.boundingBox()
            assert(box.x >= 0 && box.x + box.width <= width && box.y >= 0 && box.y + box.height <= 844, 'Mobile banner stays within viewport')
            await tab.getByRole('button', { name: locale ? 'Allow analytics' : 'Leisti analitiką', exact: true }).waitFor()
            await tab.screenshot({ path: join(output, `mobile-${width}.png`) })
            await mobile.close()
        }
        console.log('PASS Lithuanian / English mobile consent layout at 390px and 320px')

        const privateContext = await browser.newContext()
        await privateContext.addInitScript(() => localStorage.setItem('devsprendimai.cookie-consent.v2', JSON.stringify({ version: 2, analytics: true, expiresAt: Date.now() + 86400000 })))
        const privatePage = await privateContext.newPage()
        const privateRequests = []
        privatePage.on('request', (req) => { if (/google-analytics\.com|googletagmanager\.com/.test(req.url())) privateRequests.push(req.url()) })
        await privatePage.goto(base + '/login', { waitUntil: 'networkidle' })
        assert.equal(privateRequests.length, 0, 'Login excluded even with saved consent')
        console.log('PASS login excluded; PAGE_ERRORS', JSON.stringify(errors))
        assert.equal(errors.length, 0)
        console.log('SCREENSHOTS', output)
        // Finish the independent privacy/layout checks even when GA's remote
        // Enhanced Measurement setting still duplicates browser-history views.
        assert.equal(backCount, before + 2, 'GA stream must not add an automatic Back view')
    } finally {
        await browser.close()
    }
}
main().catch((error) => { console.error(error); process.exitCode = 1 })
