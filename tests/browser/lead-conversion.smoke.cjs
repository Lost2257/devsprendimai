// Mock-only integration smoke: contact and internal-statistics POSTs plus every
// Google request are intercepted. Other writes and third parties are blocked.
// This must never send a real enquiry, email, internal visit, or GA event.
// Requires Playwright. Run with LEAD_SMOKE_URL=https://devsprendimai.lt.
const { chromium } = require('playwright')
const assert = require('node:assert/strict')

const marker = { id: 'b3ce4c48-dc1e-43b2-9070-d3a2ab67a995', form_id: 'project_enquiry', locale: 'lt' }
const newMarker = { ...marker, id: 'f670416d-c8af-433c-837b-7d5c4a3c6cfe' }

async function main() {
    assert(process.env.LEAD_SMOKE_URL, 'Explicit LEAD_SMOKE_URL required')
    const base = process.env.LEAD_SMOKE_URL.replace(/\/$/, '')
    const origin = new URL(base).origin
    const browser = await chromium.launch({ headless: true, channel: 'msedge' })
    try {
        for (const scenario of [{ locale: 'lt', width: 1440, consent: true }, { locale: 'en', width: 390, consent: true }, { locale: 'lt', width: 320, consent: false }, { locale: 'en', width: 320, consent: false }]) {
            const { locale, width, consent } = scenario
            const context = await browser.newContext({ viewport: { width, height: 1000 } })
            const page = await context.newPage()
            const errors = []
            page.on('pageerror', error => errors.push(error.message))
            let initialPage, responseMarker = { ...marker, locale }, responseErrors = {}, postCount = 0, googleMockCount = 0
            await context.route('**/*', async route => {
                const request = route.request()
                const url = new URL(request.url())
                if (/(^|\.)(googletagmanager\.com|google-analytics\.com|analytics\.google\.com)$/.test(url.hostname)) {
                    googleMockCount++
                    return route.fulfill({ status: 200, contentType: 'application/javascript', body: '' })
                }
                if (url.origin !== origin) return route.abort('blockedbyclient')
                if (url.pathname === '/analytics/page-view' && request.method() === 'POST') {
                    return route.fulfill({ status: 204 })
                }
                if (/^\/(?:en\/)?contact$/.test(url.pathname)) {
                    assert.equal(request.method(), 'POST')
                    assert(initialPage, 'Read the actual Inertia page before mocking the response')
                    postCount++
                    return route.fulfill({ status: 200, contentType: 'application/json', headers: { 'x-inertia': 'true' }, body: JSON.stringify({
                        ...initialPage,
                        props: { ...initialPage.props, errors: responseErrors, flash: { success: Object.keys(responseErrors).length ? null : 'Mock enquiry saved', error: null, enquiryConversion: responseMarker } },
                    }) })
                }
                if (!['GET', 'HEAD'].includes(request.method())) return route.abort('blockedbyclient')
                return route.continue()
            })

            await page.goto(`${base}${locale === 'en' ? '/en' : '/'}`, { waitUntil: 'networkidle' })
            initialPage = JSON.parse(await page.locator('#app').getAttribute('data-page'))
            assert(initialPage.props.analytics?.measurementId, 'Test target must expose production public analytics configuration')
            const panel = page.locator('.cookie-panel')
            await panel.waitFor({ state: 'visible' })
            const bounds = await panel.boundingBox()
            assert(bounds.x >= 0 && bounds.y >= 0 && bounds.x + bounds.width <= width + 1 && bounds.y + bounds.height <= 1001, 'Consent panel stays inside the viewport')
            assert(await panel.evaluate(element => element.scrollWidth <= element.clientWidth + 1), 'Consent copy has no horizontal overflow')
            for (const action of await panel.locator('button').all()) {
                assert((await action.boundingBox()).height >= 44, 'Consent choices retain accessible touch targets')
            }
            const button = consent ? (locale === 'en' ? 'Allow analytics' : 'Leisti analitiką') : (locale === 'en' ? 'Essential only' : 'Tik būtinieji')
            await page.getByRole('button', { name: button, exact: true }).click()
            const leads = () => page.evaluate(() => (window.dataLayer || []).map(item => [...item]).filter(([kind, name]) => kind === 'event' && name === 'generate_lead'))
            const submit = async () => {
                await page.locator('#contact-name').fill('Private smoke name')
                await page.locator('#contact-email').fill('private-smoke@example.test')
                await page.locator('#contact-phone').fill('+37060000000')
                await page.locator('#contact-company').fill('Private smoke company')
                await page.locator('#contact-message').fill('Private smoke project message, never sent to a server.')
                const expected = postCount + 1
                await page.locator('#contact button[type="submit"]').click()
                await page.waitForFunction(() => !document.querySelector('#contact button[type="submit"]').disabled)
                assert.equal(postCount, expected, 'Submission was intercepted, not sent to production')
            }
            await submit()
            assert.equal(await page.locator('#contact-name').inputValue(), '', 'Successful enquiry resets even if Google is blocked')
            assert.equal((await leads()).length, consent ? 1 : 0)
            if (consent) {
                assert.deepEqual((await leads())[0][2], { form_id: 'project_enquiry', language: locale, send_to: initialPage.props.analytics.measurementId })
                await submit()
                assert.equal((await leads()).length, 1, 'Repeated success marker is deduplicated')
                responseMarker = null
                responseErrors = { name: 'Mock validation error' }
                await submit()
                assert.equal(await page.locator('#contact-name').inputValue(), 'Private smoke name')
                assert.equal((await leads()).length, 1, 'Validation failure is not a lead')
                responseErrors = {}
                await submit()
                assert.equal((await leads()).length, 1, 'Success without saved-enquiry marker (honeypot) is not a lead')
                responseMarker = { ...newMarker, locale }
                await page.evaluate(() => { window.gtag = () => { throw new Error('Mock blocked Google') } })
                await submit()
                assert.equal(await page.locator('#contact-name').inputValue(), '', 'Throwing Google cannot break success UI')
                assert.equal((await leads()).length, 1)
                await page.evaluate(() => { window.gtag = function () { window.dataLayer.push(arguments) } })
                await page.locator(`a[href="${locale === 'en' ? '/en' : ''}/interneto-svetaines"]:visible`).first().click()
                await page.waitForURL('**/interneto-svetaines')
                await page.goBack({ waitUntil: 'networkidle' })
                assert.equal((await leads()).length, 1, 'History restoration cannot replay the success marker')
                assert(googleMockCount > 0, 'Google script was intercepted')
            } else {
                assert.equal(googleMockCount, 0, 'Refusal makes no Google request')
            }
            assert.deepEqual(errors, [])
            console.log(`PASS ${locale} ${width}px consent=${consent}: mock-only form, lead privacy, dedupe and failure isolation (${postCount} intercepted POSTs)`)
            await context.close()
        }
    } finally {
        await browser.close()
    }
}

main().catch(error => { console.error(error); process.exitCode = 1 })
