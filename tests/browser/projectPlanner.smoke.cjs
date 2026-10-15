// No real enquiries are submitted. Set APP_SMOKE_URL to a local/staging URL or production.
const { chromium } = require('playwright')
const assert = require('node:assert/strict')
const { mkdtemp } = require('node:fs/promises')
const { tmpdir } = require('node:os')
const { join } = require('node:path')

async function main() {
    assert(process.env.APP_SMOKE_URL, 'APP_SMOKE_URL is required')
    const base = process.env.APP_SMOKE_URL.replace(/\/$/, '')
    const output = await mkdtemp(join(tmpdir(), 'devsprendimai-planner-'))
    const browser = await chromium.launch({ headless: true, channel: 'msedge' })
    try {
        for (const [width, locale] of [[1440, 'lt'], [390, 'lt'], [320, 'en']]) {
            const context = await browser.newContext({ viewport: { width, height: 960 }, reducedMotion: 'reduce', isMobile: width < 500 })
            const page = await context.newPage()
            const errors = [], submissions = [], google = []
            page.on('pageerror', (error) => errors.push(error.message))
            page.on('request', (request) => {
                if (request.method() === 'POST' && new URL(request.url()).pathname.endsWith('/contact')) submissions.push(request.url())
                if (/google-analytics\.com|googletagmanager\.com/.test(request.url())) google.push(request.url())
            })
            await page.goto(`${base}${locale === 'en' ? '/en' : '/'}`, { waitUntil: 'networkidle' })
            const refusal = page.getByRole('button', { name: locale === 'en' ? 'Essential only' : 'Tik būtinieji', exact: true })
            if (await refusal.count()) await refusal.click()
            const planner = page.locator('#project-planner')
            await planner.scrollIntoViewIfNeeded()
            const overflow = await planner.evaluate((element) => [...element.querySelectorAll('input, select, button, h2, h3, .goal-option')].filter((child) => {
                const box = child.getBoundingClientRect()
                return box.width > 1 && (box.left < 0 || box.right > window.innerWidth + 1)
            }).map((element) => element.outerHTML.slice(0, 150)))
            assert.deepEqual(overflow, [], 'Planner controls and headings fit the viewport')
            await page.locator('label:has(input[value="customers"])').click()
            await page.locator('#planner-stage').selectOption('improve')
            await page.locator('#planner-budget').selectOption('3000to6000')
            await planner.screenshot({ path: join(output, `planner-${width}-${locale}.png`) })
            await page.locator('#contact-message').fill('My own important project detail.')
            await planner.locator('.planner-action').click()
            assert.equal(await page.locator('#contact-service').inputValue(), locale === 'en' ? 'CRM system' : 'CRM sistema')
            let draft = await page.locator('#contact-message').inputValue()
            assert.match(draft, /My own important project detail/)
            assert.match(draft, /3 000–6 000|3,000–6,000/)
            assert.equal(await page.evaluate(() => document.activeElement?.id), 'contact-name', 'Form is keyboard-focused')
            await planner.locator('.planner-action').click()
            assert.equal(await page.locator('#contact-message').inputValue(), draft, 'Repeated click does not duplicate a draft')
            await page.locator('label:has(input[value="sales"])').click()
            await planner.locator('.planner-action').click()
            draft = await page.locator('#contact-message').inputValue()
            assert.match(draft, /My own important project detail/)
            assert.equal(await page.locator('#contact-service').inputValue(), locale === 'en' ? 'Online store' : 'El. parduotuvė')
            assert.equal(draft.split(locale === 'en' ? 'My project brief' : 'Mano projekto planas').length - 1, 1)
            await page.locator('.pricing-card').first().locator('a').click()
            assert.equal(await page.locator('#contact-service').inputValue(), locale === 'en' ? 'Website' : 'Interneto svetainė')
            assert.match(await page.locator('#contact-message').inputValue(), /449/)
            assert.equal(await page.locator('#contact-message').getAttribute('maxlength'), '5000')
            assert.equal(await page.locator('#contact-website').inputValue(), '')
            assert.deepEqual(submissions, [], 'No contact request is sent by selecting a brief')
            assert.deepEqual(google, [], 'Planner does not require or trigger analytics')
            assert.deepEqual(errors, [], 'No browser errors')
            console.log(`PASS ${locale} ${width}px: viewport, localized draft, input preservation, no duplicate, package prefill, no submission/Google`)
            await context.close()
        }
        console.log(`Screenshots: ${output}`)
    } finally {
        await browser.close()
    }
}

main().catch((error) => { console.error(error); process.exitCode = 1 })
