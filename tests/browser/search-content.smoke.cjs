/* Read-only search-content and accessibility smoke against a built deployment.
 * Requires explicit SEO_UI_SMOKE_URL and Playwright with installed Edge.
 * Blocks every third-party browser request and every first-party non-GET/HEAD
 * request. Consent is refused. No real enquiries, emails or analytics events.
 * Example (PowerShell):
 * $env:NODE_PATH='C:\Users\Aivaras\.cache\codex-runtimes\codex-primary-runtime\dependencies\node\node_modules'
 * $env:SEO_UI_SMOKE_URL='https://devsprendimai.lt'
 * node tests/browser/search-content.smoke.cjs
 */
const assert = require('node:assert/strict')
const path = require('node:path')
const { mkdir } = require('node:fs/promises')
const { pathToFileURL } = require('node:url')
const { chromium } = require('playwright')

const target = process.env.SEO_UI_SMOKE_URL
assert(target, 'Explicit SEO_UI_SMOKE_URL required; no default production target')
const base = new URL(target)
assert(['http:', 'https:'].includes(base.protocol), 'HTTP(S) target required')
assert(!base.username && !base.password, 'Do not pass credentials in the target URL')
assert(base.pathname === '/' && !base.search && !base.hash, 'Use the site origin, without a path or query')
const origin = base.origin
const screenshotDirectory = process.env.SEO_UI_SCREENSHOT_DIR ? path.resolve(process.env.SEO_UI_SCREENSHOT_DIR) : null
const normal = value => String(value ?? '').replace(/\s+/gu, ' ').trim()
const include = (actual, expected, message) => assert(normal(actual).includes(normal(expected)), message)
const localized = (locale, slug = '') => `${locale === 'en' ? '/en' : ''}${slug ? `/${slug}` : locale === 'en' ? '' : '/'}`
const loadData = name => import(pathToFileURL(path.resolve(__dirname, '../../resources/js/data', `${name}.js`)).href)
const projectSlug = initial => {
    const projects = initial.props.projects || []
    return projects.find(project => project.slug === 'devsprendimai')?.slug
        || projects[0]?.slug
        || initial.links.map(link => new URL(link.href, origin).pathname.match(/^\/(?:en\/)?projektai\/([^/]+)$/)?.[1]).find(Boolean)
}
async function capture(page, name, selector = null) {
    if (!screenshotDirectory) return
    await mkdir(screenshotDirectory, { recursive: true })
    const options = { path: path.join(screenshotDirectory, `${name}.png`), animations: 'disabled' }
    if (selector) await page.locator(selector).screenshot(options)
    else {
        await page.evaluate(() => scrollTo(0, 0))
        await page.screenshot(options)
    }
}

async function safeContext(browser, width, height = 1000) {
    const context = await browser.newContext({ viewport: { width, height }, reducedMotion: 'reduce', serviceWorkers: 'block' })
    const blocked = { thirdParty: 0, writes: 0 }
    await context.route('**/*', async route => {
        const request = route.request()
        const url = new URL(request.url())
        if (url.origin !== origin) {
            blocked.thirdParty++
            return route.abort('blockedbyclient')
        }
        if (!['GET', 'HEAD'].includes(request.method())) {
            blocked.writes++
            return route.abort('blockedbyclient')
        }
        return route.continue()
    })
    await context.addInitScript(() => {
        localStorage.setItem('devsprendimai.cookie-consent.v2', JSON.stringify({
            version: 2, analytics: false, updatedAt: Date.now(), expiresAt: Date.now() + 86400000,
        }))
    })
    // Refusal also applies to the first HTTP request, before the JS preference sync.
    await context.addCookies([{ name: 'devsprendimai_analytics_consent', value: 'denied', url: origin, sameSite: 'Lax', secure: base.protocol === 'https:' }])
    const page = await context.newPage()
    const errors = []
    page.on('pageerror', error => errors.push(error.message))
    page.setDefaultTimeout(15000)
    page.setDefaultNavigationTimeout(45000)
    return { context, page, blocked, errors }
}

async function initialDocument(context, page, routePath) {
    // API requests do not inherit browser routing: keep this operation explicitly
    // GET-only and same-origin. Never replace it with form submission or POST.
    const url = new URL(routePath, origin)
    assert.equal(url.origin, origin)
    const response = await context.request.get(url.href, { headers: { Accept: 'text/html' }, maxRedirects: 0 })
    assert.equal(response.status(), 200, `${routePath}: initial HTTP 200`)
    assert.match(response.headers()['content-type'], /text\/html/i)
    const document = await page.evaluate(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html')
        const main = doc.querySelector('.initial-content main')
        const state = doc.querySelector('#app')?.getAttribute('data-page')
        return {
            main: main?.textContent || '',
            headings: [...(main?.querySelectorAll('h1') || [])].map(element => element.textContent),
            links: [...(main?.querySelectorAll('a[href]') || [])].map(element => ({ href: element.getAttribute('href'), text: element.textContent })),
            title: doc.title,
            description: doc.querySelector('meta[name="description"]')?.content,
            canonicals: [...doc.querySelectorAll('link[rel="canonical"]')].map(element => element.getAttribute('href')),
            props: state ? JSON.parse(state).props : {},
        }
    }, await response.text())
    assert.equal(document.headings.length, 1, `${routePath}: one initial H1`)
    assert.deepEqual(document.canonicals, [`${origin}${routePath}`], `${routePath}: canonical points at this exact public URL`)
    assert(document.main.length > 150, `${routePath}: useful HTML exists before JavaScript`)
    return document
}

async function checkMetadata(page, initial, routePath, locale, expectedDescription) {
    await page.waitForFunction(expected => document.title === expected, initial.title)
    assert.equal(await page.title(), initial.title, `${routePath}: SPA and initial title agree`)
    assert.equal(normal(initial.description), normal(expectedDescription), `${routePath}: shared initial description`)
    assert.equal(normal(await page.locator('meta[name="description"]').getAttribute('content')), normal(expectedDescription), `${routePath}: rendered description updates`)
    assert.equal(await page.locator('link[rel="canonical"]').count(), 1)
    assert.equal(await page.locator('link[rel="canonical"]').getAttribute('href'), `${origin}${routePath}`)
    assert.equal(await page.locator('meta[property="og:url"]').getAttribute('content'), `${origin}${routePath}`)
    assert.equal(await page.locator('html').getAttribute('lang'), locale)
    const unprefixed = routePath.replace(/^\/en(?=\/|$)/, '') || '/'
    assert.equal(await page.locator('link[hreflang="lt-LT"]').getAttribute('href'), `${origin}${unprefixed}`)
    assert.equal(await page.locator('link[hreflang="en"]').getAttribute('href'), `${origin}/en${unprefixed === '/' ? '' : unprefixed}`)
    assert.equal(await page.locator('.site-shell h1').count(), 1, 'Interactive page has one H1')
}

async function checkViewport(page, label, selectors = []) {
    const dimensions = await page.evaluate(() => ({ width: innerWidth, height: innerHeight, document: document.documentElement.scrollWidth, body: document.body.scrollWidth }))
    assert(dimensions.document <= dimensions.width + 1 && dimensions.body <= dimensions.width + 1, `${label}: no horizontal document overflow (${JSON.stringify(dimensions)})`)
    for (const selector of selectors) {
        const element = page.locator(selector).first()
        const bounds = await element.boundingBox()
        assert(bounds && bounds.x >= -1 && bounds.x + bounds.width <= dimensions.width + 1, `${label}: ${selector} stays horizontally inside viewport`)
    }
}

async function checkHome(page, initial, locale, data) {
    const copy = data.siteContent[locale]
    const title = `${copy.headline} ${copy.highlight}`
    assert.equal(normal(await page.locator('.home-hero h1').textContent()), normal(title))
    assert.equal(normal(initial.headings[0]), normal(title), 'Home headline agrees before/after JS')
    include(initial.main, copy.description, 'Home introduction in initial HTML')
    include(await page.locator('.home-hero').textContent(), copy.description, 'Home introduction in rendered UI')
    const packages = data.getHomePackages(locale === 'en')
    assert.deepEqual((await page.locator('.pricing-card > strong').allTextContents()).map(normal), packages.map(item => normal(item.price)))
    for (const item of packages) {
        include(initial.main, item.name, 'Home package name in initial HTML')
        include(initial.main, item.price, 'Shared home price in initial HTML')
    }
    include(initial.main, copy.pricingNote, 'Price scope note in initial HTML')
    for (const item of locale === 'en' ? data.faqItemsEn : data.faqItems) {
        include(initial.main, item.q, 'Home FAQ question in initial HTML')
        include(initial.main, item.a, 'Home FAQ answer in initial HTML')
        include(await page.locator('#duk').textContent(), item.a, 'Home FAQ answer remains rendered in DOM')
    }
    const button = page.locator('#duk .faq-item button').first()
    const expanded = await button.getAttribute('aria-expanded')
    await button.focus()
    await page.keyboard.press('Enter')
    assert.equal(await button.getAttribute('aria-expanded'), expanded === 'true' ? 'false' : 'true', 'Home FAQ toggles using keyboard')
    await checkMetadata(page, initial, localized(locale), locale, locale === 'lt' && initial.props.settings?.site_description || copy.seoDescription)
}

async function checkService(page, initial, locale, slug, service, data) {
    const english = locale === 'en'
    const config = data.pricedService(english ? data.englishServices[service.title] : service, service.title, english)
    const heading = `${config.headline} ${config.highlight}`
    assert.equal(normal(await page.locator('.service-hero h1').textContent()), normal(heading))
    assert.equal(normal(initial.headings[0]), normal(heading), `${slug}: headline agrees before/after JS`)
    include(initial.main, config.description, `${slug}: initial introduction`)
    assert.deepEqual((await page.locator('.offer-price strong').allTextContents()).map(normal), config.offers.map(item => normal(item.price)))
    for (const offer of config.offers) {
        include(initial.main, offer.title, `${slug}: initial offer title`)
        include(initial.main, offer.price, `${slug}: shared initial price`)
    }
    const guide = data.serviceGuides[locale][slug]
    assert(guide, `${slug}: guide fixture exists`)
    assert.equal(normal(await page.locator('#guide-title').textContent()), normal(guide.title))
    for (const text of [guide.title, guide.intro, ...guide.considerations.flatMap(item => [item.title, item.text])]) {
        include(initial.main, text, `${slug}: buyer guidance in initial HTML`)
        include(await page.locator('.service-guide').textContent(), text, `${slug}: buyer guidance in rendered DOM`)
    }
    assert.equal(await page.locator('.guide-faq details').count(), guide.faq.length)
    for (const item of guide.faq) {
        for (const text of [item.q, item.a]) {
            include(initial.main, text, `${slug}: full FAQ in initial HTML`)
            include(await page.locator('.guide-faq').textContent(), text, `${slug}: full FAQ remains in rendered DOM`)
        }
    }
    const first = page.locator('.guide-faq details').first()
    const summary = first.locator('summary')
    await summary.focus()
    assert.equal(await first.evaluate(element => element.open), false)
    await page.keyboard.press('Enter')
    assert.equal(await first.evaluate(element => element.open), true, 'Native FAQ opens with Enter')
    await page.keyboard.press('Space')
    assert.equal(await first.evaluate(element => element.open), false, 'Native FAQ closes with Space')
    for (const link of guide.related) {
        const href = localized(locale, link.slug)
        assert.equal(await page.locator(`.guide-links a[href="${href}"]`).count(), 1, `${slug}: localized related-service link`)
        include(await page.locator(`.guide-links a[href="${href}"]`).textContent(), link.label, 'Useful descriptive anchor text')
        assert(initial.links.some(item => new URL(item.href, origin).href === `${origin}${href}` && normal(item.text).includes(normal(link.label))), `${slug}: related link exists before JS`)
    }
    assert.equal(await page.locator(`.guide-links a[href="${localized(locale)}#projects"]`).count(), 1)
    await checkMetadata(page, initial, localized(locale, slug), locale, config.seoDescription)
}

async function navigateInertia(page, routePath) {
    await page.evaluate(() => { window.__seoUiNavigationGuard = 'preserve-this-document' })
    const link = page.locator(`a[href="${routePath}"]:visible`).first()
    assert(await link.count(), `A visible real link to ${routePath} exists`)
    await link.click()
    await page.waitForURL(url => url.pathname === routePath && !url.hash)
    await page.locator('.site-shell').waitFor()
    assert.equal(await page.evaluate(() => window.__seoUiNavigationGuard), 'preserve-this-document', `${routePath}: tested Inertia navigation, not a fresh page load`)
}

async function checkProjectReader(context, page, locale, slug, width) {
    const routePath = localized(locale, `projektai/${slug}`)
    await page.goto(`${origin}${routePath}`, { waitUntil: 'domcontentloaded' })
    await page.locator('.project-experience').waitFor()
    const initial = await initialDocument(context, page, routePath)
    const project = initial.props.project
    assert(project?.title, 'Project fixture comes from its actual public response')
    const dialog = page.locator('#project-story-reader')
    const trigger = page.getByRole('button', { name: locale === 'en' ? 'Read full case study' : 'Visa projekto istorija', exact: true })
    assert.equal(await dialog.count(), 1, 'Reader is present before interaction')
    assert.equal(await dialog.isVisible(), false, 'Native reader starts closed')
    const text = await dialog.textContent()
    for (const key of ['title', 'subtitle', 'description', 'challenge', 'solution', 'result']) {
        if (project[key]) {
            include(text, project[key], `Closed reader retains complete real ${key}`)
            include(initial.main, project[key], `Project ${key} is readable before JavaScript`)
        }
    }
    for (const key of ['services', 'technologies']) {
        for (const item of String(project[key] || '').split(/[,\n]/).map(value => value.trim()).filter(Boolean)) include(text, item, `Reader contains all ${key}`)
    }
    assert.equal(await page.locator('.site-shell h1').count(), 1, 'Reader does not create a second H1')
    await checkViewport(page, `Project ${locale} ${width}px`, ['.story-trigger', '.experience-topbar'])
    const triggerBox = await trigger.boundingBox()
    assert(triggerBox.height >= 44 && triggerBox.y >= 0, 'Visible reader trigger has a 44px touch target')
    const activeStep = () => page.locator('.slide-tabs').evaluate(element => [...element.children].findIndex(button => button.classList.contains('active')))
    const originalStep = await activeStep()
    await trigger.click()
    await dialog.waitFor({ state: 'visible' })
    if (screenshotDirectory) await page.screenshot({ path: path.join(screenshotDirectory, `${locale}-${width}-project-reader.png`), animations: 'disabled' })
    const box = await dialog.boundingBox()
    const viewport = page.viewportSize()
    assert(box.x >= 0 && box.y >= 0 && box.x + box.width <= viewport.width + 1 && box.y + box.height <= viewport.height + 1, 'Reader fits the viewport')
    assert(await dialog.evaluate(element => element.scrollWidth <= element.clientWidth + 1), 'Reader has no horizontal overflow')
    assert(await page.locator('#project-story-title').evaluate(element => element === document.activeElement), 'Reader focuses its title')
    await page.keyboard.press('ArrowDown')
    await page.keyboard.press('PageDown')
    const body = page.locator('.story-reader-body')
    if (await body.evaluate(element => element.scrollHeight > element.clientHeight + 10)) {
        await page.waitForFunction(() => document.querySelector('.story-reader-body').scrollTop > 0)
        await body.evaluate(element => { element.scrollTop = element.scrollHeight })
        assert(await body.evaluate(element => element.scrollTop + element.clientHeight >= element.scrollHeight - 2), 'Full reader content can be scrolled to the end')
    }
    await page.keyboard.press('Tab')
    assert(await dialog.evaluate(element => element.contains(document.activeElement)), 'Keyboard focus stays inside modal')
    assert.equal(await activeStep(), originalStep, 'Reading keyboard input does not advance the scene')
    await page.keyboard.press('Escape')
    await dialog.waitFor({ state: 'hidden' })
    await page.waitForFunction(() => document.activeElement === document.querySelector('.story-trigger'))
    assert.equal(await activeStep(), originalStep, 'Closing reader preserves current scene')
    await trigger.click()
    await dialog.getByRole('button', { name: locale === 'en' ? 'Close' : 'Uždaryti', exact: true }).click()
    await dialog.waitFor({ state: 'hidden' })
    await page.waitForFunction(() => document.activeElement === document.querySelector('.story-trigger'))
    assert(await trigger.evaluate(element => element === document.activeElement), 'Close button returns focus')
    console.log(`PASS project reader ${locale} ${width}px: complete DOM copy, internal scroll, keyboard isolation and focus return`)
}

async function main() {
    const [services, english, pricing, copy, guides, faq] = await Promise.all(['services', 'services.en', 'pricing', 'siteContent', 'serviceGuides', 'faq'].map(loadData))
    const data = { ...english, ...pricing, ...copy, ...guides, ...faq }
    const servicePages = {
        'interneto-svetaines': services.websites,
        'el-parduotuves': services.ecommerce,
        'crm-sistemos': services.crm,
        automatizacija: services.automation,
        'aplikaciju-kurimas': services.applications,
        'svetainiu-prieziura': services.maintenance,
    }
    const browser = await chromium.launch({ headless: true, channel: 'msedge' })
    try {
        if (screenshotDirectory) await mkdir(screenshotDirectory, { recursive: true })
        if (process.argv.includes('--project-reader-only')) {
            for (const { locale, width } of [{ locale: 'lt', width: 320 }, { locale: 'en', width: 390 }]) {
                const { context, page, blocked, errors } = await safeContext(browser, width, 844)
                try {
                    await page.goto(`${origin}${localized(locale)}`, { waitUntil: 'domcontentloaded' })
                    await page.locator('.home-hero').waitFor()
                    const home = await initialDocument(context, page, localized(locale))
                    const slug = projectSlug(home)
                    if (slug) await checkProjectReader(context, page, locale, slug, width)
                    else console.warn(`SKIP project reader ${locale}: no public project in this environment`)
                    assert.deepEqual(errors, [])
                    assert.equal(blocked.writes, 0)
                } finally { await context.close() }
            }
            return
        }
        for (const locale of ['lt', 'en']) {
            const { context, page, blocked, errors } = await safeContext(browser, 1440)
            try {
                await page.goto(`${origin}${localized(locale)}`, { waitUntil: 'domcontentloaded' })
                await page.locator('.home-hero').waitFor()
                const home = await initialDocument(context, page, localized(locale))
                await checkHome(page, home, locale, data)
                await checkViewport(page, `${locale} home desktop`, ['.home-hero h1'])
                const slugToRead = projectSlug(home)
                await capture(page, `${locale}-1440-home`)
                console.log(`PASS ${locale} home: initial/rendered headline, prices, FAQ, metadata`)
                for (const [slug, service] of Object.entries(servicePages)) {
                    const routePath = localized(locale, slug)
                    await navigateInertia(page, routePath)
                    const initial = await initialDocument(context, page, routePath)
                    await checkService(page, initial, locale, slug, service, data)
                    await checkViewport(page, `${locale} ${slug} desktop`, ['.service-hero h1', '.service-guide'])
                    if (slug === 'interneto-svetaines') await capture(page, `${locale}-1440-website-guide`, '.service-guide')
                    console.log(`PASS ${routePath}: shared initial/rendered prices, guide FAQ, links and SPA metadata`)
                }
                if (slugToRead) await checkProjectReader(context, page, locale, slugToRead, 1440)
                else console.warn(`SKIP project reader ${locale}: no public project in this environment`)
                assert.deepEqual(errors, [], 'No uncaught browser errors')
                assert.equal(blocked.writes, 0, 'Read-only checks should not attempt first-party writes')
                console.log(`PASS desktop ${locale}: third-party requests blocked=${blocked.thirdParty}; first-party writes=0`)
            } finally { await context.close() }
        }

        for (const { locale, width } of [{ locale: 'lt', width: 320 }, { locale: 'en', width: 390 }]) {
            const { context, page, blocked, errors } = await safeContext(browser, width, 844)
            try {
                await page.goto(`${origin}${localized(locale)}`, { waitUntil: 'domcontentloaded' })
                await page.locator('.home-hero').waitFor()
                const home = await initialDocument(context, page, localized(locale))
                await checkHome(page, home, locale, data)
                await checkViewport(page, `${locale} home ${width}px`, ['.home-hero h1', '#pricing .pricing-heading', '#duk'])
                await capture(page, `${locale}-${width}-home`)
                for (const [serviceSlug, service] of Object.entries(servicePages)) {
                    const routePath = localized(locale, serviceSlug)
                    await page.goto(`${origin}${routePath}`, { waitUntil: 'domcontentloaded' })
                    await page.locator('.service-guide').waitFor()
                    const initial = await initialDocument(context, page, routePath)
                    await checkService(page, initial, locale, serviceSlug, service, data)
                    await checkViewport(page, `${locale} ${serviceSlug} ${width}px`, ['.service-hero h1', '.service-guide', '.guide-heading', '.guide-faq', '.guide-links'])
                    if (serviceSlug === 'interneto-svetaines') {
                        await capture(page, `${locale}-${width}-website-guide`, '.service-guide')
                        await capture(page, `${locale}-${width}-website-hero`)
                    }
                    console.log(`PASS ${routePath} ${width}px: shared content, FAQ keyboard, metadata and viewport bounds`)
                }
                const slug = projectSlug(home)
                if (slug) await checkProjectReader(context, page, locale, slug, width)
                else console.warn(`SKIP project reader ${locale} ${width}px: no public project in this environment`)
                assert.deepEqual(errors, [], 'No uncaught mobile browser errors')
                assert.equal(blocked.writes, 0)
                console.log(`PASS mobile ${locale} ${width}px: home, all six service guides and project reader; no horizontal overflow`)
            } finally { await context.close() }
        }
    } finally { await browser.close() }
    console.log('Search content smoke passed: LT/EN home + six services, desktop/mobile, initial HTML parity and Inertia metadata. Project reader outcomes are reported separately (empty fixtures skip). No real form submissions or analytics events.')
}

main().catch(error => { console.error(error); process.exitCode = 1 })
