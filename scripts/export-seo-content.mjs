// The server's initial HTML uses the same content as the interactive Vue pages.
// This file is deliberately generated at build time: PHP does not need Node in production.
import { mkdir, readFile, writeFile } from 'node:fs/promises'
import { fileURLToPath } from 'node:url'
import { websites, ecommerce, crm, automation, applications, maintenance } from '../resources/js/data/services.js'
import { englishServices } from '../resources/js/data/services.en.js'
import { faqItems, faqItemsEn } from '../resources/js/data/faq.js'
import { legalPagesLt, legalPagesEn } from '../resources/js/data/legal.js'
import { siteContent } from '../resources/js/data/siteContent.js'
import { pricedService, getHomePackages } from '../resources/js/data/pricing.js'
import { serviceGuides } from '../resources/js/data/serviceGuides.js'

const services = {
    'interneto-svetaines': websites,
    'el-parduotuves': ecommerce,
    'crm-sistemos': crm,
    'automatizacija': automation,
    'aplikaciju-kurimas': applications,
    'svetainiu-prieziura': maintenance,
}
const pickContent = (service) => Object.fromEntries([
    'title', 'seoDescription', 'eyebrow', 'headline', 'highlight', 'description',
    'benefitsTitle', 'benefitsLead', 'benefits', 'featureTitle', 'featureLead',
    'features', 'process', 'offers', 'pricingTitle',
].map((key) => [key, service[key]]))
const content = {
    lt: { home: siteContent.lt, packages: getHomePackages(), guides: serviceGuides.lt, services: {}, faq: faqItems, legal: legalPagesLt },
    en: { home: siteContent.en, packages: getHomePackages(true), guides: serviceGuides.en, services: {}, faq: faqItemsEn, legal: legalPagesEn },
}
for (const [slug, service] of Object.entries(services)) {
    content.lt.services[slug] = pickContent(pricedService(service, service.title))
    content.en.services[slug] = pickContent(pricedService(englishServices[service.title], service.title, true))
}

const target = new URL('../resources/data/seo-content.json', import.meta.url)
const output = `${JSON.stringify(content, null, 2)}\n`
if (process.argv.includes('--check')) {
    const existing = await readFile(target, 'utf8').catch(() => '')
    if (existing !== output) {
        console.error('Server-rendered SEO content is stale. Run npm run seo:content and commit the generated JSON.')
        process.exitCode = 1
    } else console.log('Server-rendered SEO content matches the public Vue content.')
} else {
    await mkdir(new URL('../resources/data/', import.meta.url), { recursive: true })
    await writeFile(target, output)
    console.log(`Exported shared LT/EN content to ${fileURLToPath(target)}`)
}
