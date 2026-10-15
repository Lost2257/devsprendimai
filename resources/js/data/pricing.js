// One source for public starting prices: home, service pages and initial HTML.
export const servicePricing = {
    'Interneto svetainių kūrimas': { amounts: [449, 749, 1490], lt: 'Svetainių kūrimo kainos', en: 'Website development prices' },
    'El. parduotuvių kūrimas': { amounts: [1090, 1790, 2990], lt: 'El. parduotuvių kūrimo kainos', en: 'Online store development prices' },
    'CRM sistemų kūrimas': { amounts: [1990, 3490, 5990], lt: 'CRM sistemos kūrimo kainos', en: 'CRM development prices' },
    'Verslo procesų automatizacija': { amounts: [390, 990, 2490], lt: 'Procesų automatizavimo kainos', en: 'Process automation prices' },
    'Svetainių priežiūra': { amounts: [39, 89, 179], monthly: true, lt: 'Svetainių priežiūros kainos', en: 'Website maintenance prices' },
    'Aplikacijų kūrimas': { amounts: [2490, 4490, 7990], lt: 'Aplikacijų kūrimo kainos', en: 'Application development prices' },
}

export function formatPrice(amount, english = false, monthly = false) {
    const number = String(amount).replace(/\B(?=(\d{3})+(?!\d))/g, english ? ',' : ' ')
    return (english ? `€${number}` : `${number} €`) + (monthly ? (english ? '/month' : '/mėn.') : '')
}

export function pricedService(service, originalTitle, english = false) {
    const pricing = servicePricing[originalTitle]
    return {
        ...service,
        pricingTitle: pricing?.[english ? 'en' : 'lt'] || service.title,
        offers: service.offers.map((offer, index) => ({
            ...offer,
            price: pricing ? formatPrice(pricing.amounts[index], english, pricing.monthly) : (offer.price || null),
        })),
    }
}

const homePackages = {
    lt: [
        { name: 'Startinė svetainė', service: 'Interneto svetainė', pricingKey: 'Interneto svetainių kūrimas', tier: 0, text: 'Koncentruotas nukreipimo puslapis arba nedidelė įmonės svetainė.', points: ['Responsyvus dizainas', 'Kontaktų forma', 'Techninis SEO pagrindas'] },
        { name: 'Verslo svetainė', service: 'Interneto svetainė', pricingKey: 'Interneto svetainių kūrimas', tier: 1, tag: 'Augančiam verslui', text: 'Pilna svetainė, skirta pristatyti verslą ir priimti užklausas.', points: ['Individuali struktūra ir dizainas', 'Iki 7 pagrindinių puslapių', 'Analitika ir konversijų paruošimas'], featured: true },
        { name: 'El. parduotuvė', service: 'El. parduotuvė', pricingKey: 'El. parduotuvių kūrimas', tier: 0, text: 'Parduotuvė su mokėjimų ir pristatymo integracijomis.', points: ['Prekių katalogas', 'Mokėjimai ir pristatymas', 'Užsakymų valdymas'] },
        { name: 'Web aplikacija / CRM', service: 'Aplikacijų kūrimas', pricingKey: 'Aplikacijų kūrimas', tier: 0, text: 'Individualus MVP, klientų portalas arba vidinė verslo sistema.', points: ['UX/UI prototipas', 'Esminė verslo logika', 'Plėtrai paruošta architektūra'] },
    ],
    en: [
        { name: 'Starter website', service: 'Interneto svetainė', pricingKey: 'Interneto svetainių kūrimas', tier: 0, text: 'A focused landing page or compact company website.', points: ['Responsive design', 'Contact form', 'Technical SEO foundation'] },
        { name: 'Business website', service: 'Interneto svetainė', pricingKey: 'Interneto svetainių kūrimas', tier: 1, tag: 'For growing businesses', text: 'A complete website designed to present your business and receive enquiries.', points: ['Custom structure and design', 'Up to 7 core pages', 'Analytics and conversion setup'], featured: true },
        { name: 'Online store', service: 'El. parduotuvė', pricingKey: 'El. parduotuvių kūrimas', tier: 0, text: 'An online store with payments and delivery integrations.', points: ['Product catalogue', 'Payments and shipping', 'Order management'] },
        { name: 'Web app / CRM', service: 'Aplikacijų kūrimas', pricingKey: 'Aplikacijų kūrimas', tier: 0, text: 'A custom MVP, client portal or internal business system.', points: ['UX/UI prototype', 'Core business logic', 'Scalable architecture'] },
    ],
}

export const getHomePackages = (english = false) => homePackages[english ? 'en' : 'lt'].map(item => ({
    ...item,
    price: formatPrice(servicePricing[item.pricingKey].amounts[item.tier], english),
}))
