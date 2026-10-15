// Own-app visual regression smoke. Does not submit forms or grant analytics consent.
const { chromium } = require('playwright')
const assert = require('node:assert/strict')
const { mkdtemp } = require('node:fs/promises')
const { tmpdir } = require('node:os')
const { join } = require('node:path')

async function main() {
    assert(process.env.APP_SMOKE_URL, 'APP_SMOKE_URL is required')
    const base = process.env.APP_SMOKE_URL.replace(/\/$/, '')
    const output = await mkdtemp(join(tmpdir(), 'devsprendimai-hero-'))
    console.log(`Screenshots: ${output}`)
    const browser = await chromium.launch({ headless: true, channel: 'msedge' })
    try {
        for (const locale of ['lt', 'en']) {
            for (const width of [320, 360, 390, 768, 1440]) {
                const context = await browser.newContext({ viewport: { width, height: 1000 }, reducedMotion: 'reduce', isMobile: width < 500 })
                const page = await context.newPage()
                const errors = []
                page.on('pageerror', (error) => errors.push(error.message))
                await page.goto(`${base}${locale === 'en' ? '/en' : '/'}`, { waitUntil: 'networkidle' })
                const refusal = page.getByRole('button', { name: locale === 'en' ? 'Essential only' : 'Tik būtinieji', exact: true })
                if (await refusal.count()) await refusal.click()
                await page.locator('.hero-copy').waitFor()
                const overflow = await page.locator('.home-hero').evaluate((hero) => [...hero.querySelectorAll('.hero-copy, .hero-copy h1, .hero-copy > p, .hero-actions, .hero-panel, .panel-title')].flatMap((element) => {
                    const rect = element.getBoundingClientRect()
                    const outside = rect.left < -1 || rect.right > window.innerWidth + 1
                    const clippedText = element.matches('h1, p, .panel-title') && element.scrollWidth > element.clientWidth + 1
                    return outside || clippedText ? [{ element: element.className || element.tagName, left: rect.left, right: rect.right, scrollWidth: element.scrollWidth, width: element.clientWidth }] : []
                }))
                await page.screenshot({ path: join(output, `hero-${width}-${locale}.png`) })
                assert.deepEqual(overflow, [], `Hero content fits ${width}px ${locale}`)
                assert.equal(await page.locator('h1').count(), 1, 'Vue replaced initial SEO fallback, only one H1 remains')
                assert.deepEqual(errors, [], 'No browser errors')
                console.log(`PASS hero ${locale} ${width}px`)
                await context.close()
            }
        }
    } finally { await browser.close() }
}
main().catch((error) => { console.error(error); process.exitCode = 1 })
