/* Read-only checks against the actual deployed HTTP responses (no browser/GA traffic).
 * Usage: SEO_SMOKE_URL=https://devsprendimai.lt node tests/browser/seo.smoke.cjs
 */
const assert = require('node:assert/strict')

const target = process.env.SEO_SMOKE_URL
assert(target, 'Set SEO_SMOKE_URL explicitly to the deployment to verify.')
const base = new URL(target)
const origin = base.origin
const decode = (value) => value.replaceAll('&amp;', '&').replaceAll('&quot;', '"').replaceAll('&apos;', "'").replaceAll('&lt;', '<').replaceAll('&gt;', '>')
const get = async (url) => {
    const response = await fetch(url, { redirect: 'manual', signal: AbortSignal.timeout(20000) })
    assert.equal(response.status, 200, `${url}: expected HTTP 200`)
    return { response, text: await response.text() }
}
async function run() {
    const sitemap = await get(`${origin}/sitemap.xml`)
    assert.match(sitemap.response.headers.get('content-type'), /application\/xml/)
    const urls = [...sitemap.text.matchAll(/<loc>(.*?)<\/loc>/gs)].map((match) => decode(match[1]))
    assert(urls.length >= 20, 'Expected the LT/EN home, service and legal pages.')
    assert.equal(new Set(urls).size, urls.length, 'No duplicate sitemap URLs')
    for (const url of urls) {
        assert.equal(new URL(url).origin, origin, 'Sitemap contains a different host')
        const { response, text: html } = await get(url)
        assert(!response.headers.get('x-robots-tag')?.includes('noindex'), `${url}: noindex header`)
        const canonical = [...html.matchAll(/<link\b[^>]*\brel="canonical"[^>]*\bhref="([^"]+)"[^>]*>/g)]
        assert.equal(canonical.length, 1, `${url}: exactly one canonical`)
        assert.equal(decode(canonical[0][1]), url, `${url}: canonical must match sitemap`)
        assert.match(html, /<meta[^>]+name="robots"[^>]+content="index, follow/, `${url}: indexable`)
        assert.equal([...html.matchAll(/hreflang="(?:lt-LT|en|x-default)"/g)].length, 3, `${url}: reciprocal languages`)
        assert.match(html, /class="initial-content"/, `${url}: readable initial content`)
        const main = html.match(/<main>([\s\S]*?)<\/main>/)?.[1] || ''
        assert.match(main, /<h1>[^<]+<\/h1>/, `${url}: one readable page title`)
        assert(main.replace(/<[^>]+>/g, '').trim().length > 100, `${url}: meaningful initial text`)
        const schema = html.match(/<script id="devsprendimai-structured-data" type="application\/ld\+json">([\s\S]*?)<\/script>/)?.[1]
        assert(schema, `${url}: page schema present before JS`)
        const graph = JSON.parse(schema)['@graph']
        assert(graph.some((entry) => entry['@type'] === 'WebPage' && entry.url === url), `${url}: accurate WebPage schema`)
        console.log(`PASS ${new URL(url).pathname}`)
    }
    const robots = (await get(`${origin}/robots.txt`)).text
    assert(robots.includes(`Sitemap: ${origin}/sitemap.xml`), 'robots.txt points to the canonical sitemap')
    assert(!/^Disallow:\s*\/\s*$/m.test(robots), 'No global crawler block')
    const login = await get(`${origin}/login`)
    assert(login.response.headers.get('x-robots-tag')?.includes('noindex'), 'Login must be noindex')
    const missing = await fetch(`${origin}/seo-smoke-missing-page-8edc`, { redirect: 'manual' })
    assert.equal(missing.status, 404, 'Missing pages must return real 404, not soft 404')
    console.log(`SEO smoke passed: ${urls.length} indexable pages, robots, sitemap, login exclusion and real 404.`)
}
run().catch((error) => { console.error(error); process.exitCode = 1 })
