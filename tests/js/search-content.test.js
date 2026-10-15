import test from 'node:test'
import assert from 'node:assert/strict'
import { websites, ecommerce, crm, automation, applications, maintenance } from '../../resources/js/data/services.js'
import { englishServices } from '../../resources/js/data/services.en.js'
import { formatPrice, getHomePackages, pricedService, servicePricing } from '../../resources/js/data/pricing.js'
import { serviceGuides } from '../../resources/js/data/serviceGuides.js'
import { siteContent } from '../../resources/js/data/siteContent.js'

const services = [websites, ecommerce, crm, automation, applications, maintenance]
test('all public service prices use one source in LT and EN', () => {
    for (const service of services) {
        for (const english of [false, true]) {
            const original = english ? englishServices[service.title] : service
            const result = pricedService(original, service.title, english)
            const pricing = servicePricing[service.title]
            assert.equal(result.offers.length, pricing.amounts.length)
            result.offers.forEach((offer, index) => assert.equal(offer.price, formatPrice(pricing.amounts[index], english, pricing.monthly)))
            assert(!JSON.stringify(result).includes('undefined'))
            assert.equal(result.pricingTitle, pricing[english ? 'en' : 'lt'])
        }
    }
    assert.equal(pricedService({ ...websites, offers: [{ price: 'old price' }] }, websites.title).offers[0].price, '449 €')
    assert.equal(formatPrice(39, true, true), '€39/month')
    assert.equal(formatPrice(1490), '1 490 €')
})

test('homepage, metadata and service starter prices agree', () => {
    for (const english of [false, true]) {
        const packages = getHomePackages(english)
        for (const item of packages) {
            assert.equal(item.price, formatPrice(servicePricing[item.pricingKey].amounts[item.tier], english))
        }
        assert(siteContent[english ? 'en' : 'lt'].seoDescription.includes(packages[0].price))
    }
    assert(websites.offers[0].points.some(text => text.includes('5')))
    assert(englishServices[websites.title].offers[0].points.some(text => text.includes('5')))
})

test('every guide is localized, distinct and links only to existing complementary services', () => {
    const slugs = ['interneto-svetaines', 'el-parduotuves', 'crm-sistemos', 'automatizacija', 'aplikaciju-kurimas', 'svetainiu-prieziura']
    for (const locale of ['lt', 'en']) {
        assert.deepEqual(Object.keys(serviceGuides[locale]).sort(), [...slugs].sort())
        const questions = []
        for (const [slug, guide] of Object.entries(serviceGuides[locale])) {
            assert.equal(guide.considerations.length, 3)
            assert.equal(guide.faq.length, 4)
            assert(guide.intro && guide.title)
            for (const item of guide.faq) {
                assert(item.q && item.a)
                questions.push(item.q)
            }
            for (const link of guide.related) {
                assert(slugs.includes(link.slug) && link.slug !== slug)
                assert(link.label)
            }
        }
        assert.equal(new Set(questions).size, questions.length)
    }
})
