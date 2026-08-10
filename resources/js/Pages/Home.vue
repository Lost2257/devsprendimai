<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import Hero from '@/Components/Hero.vue'
import Services from '@/Components/Services.vue'
import Pricing from '@/Components/Pricing.vue'
import Projects from '@/Components/Projects.vue'
import About from '@/Components/About.vue'
import Process from '@/Components/Process.vue'
import FAQ from '@/Components/FAQ.vue'
import WebsiteCTA from '@/Components/Services/Shared/WebsiteCTA.vue'
import { faqItems, faqItemsEn } from '@/data/faq'
import { useLocale } from '@/composables/useLocale'

const props = defineProps({
    projects: { type: Array, default: () => [] },
    settings: { type: Object, default: () => ({}) },
})

const page = usePage()
const { isEnglish } = useLocale()
const baseUrl = computed(() => (page.props.appUrl || '').replace(/\/$/, ''))
const seoTitle = computed(() => isEnglish.value ? 'DevSprendimai — digital products for business growth' : (props.settings?.site_title || 'DevSprendimai — skaitmeniniai sprendimai verslui'))
const seoDescription = computed(() => isEnglish.value ? 'Websites, applications, e-commerce, CRM systems and business process automation.' : (props.settings?.site_description || 'Interneto svetainės, aplikacijos, el. parduotuvės, CRM sistemos ir verslo procesų automatizacija.'))
const homeUrl = computed(() => `${baseUrl.value}${isEnglish.value ? '/en' : '/'}`)
const socialImage = computed(() => props.settings?.og_image
    ? `${page.props.appUrl || ''}/storage/${props.settings.og_image}`
    : `${page.props.appUrl || ''}/og.png`)
const organizationId = computed(() => `${baseUrl.value}/#organization`)
const homeStructuredData = computed(() => [
    {
        '@type': 'Organization',
        '@id': organizationId.value,
        name: 'DevSprendimai',
        legalName: 'Aivaras Razvodovskis',
        alternateName: 'DS',
        url: `${baseUrl.value}/`,
        logo: `${baseUrl.value}/favicon.png`,
        email: 'info@devsprendimai.lt',
        telephone: '+37060606227',
        identifier: 'Individualios veiklos pažyma Nr. 1400067',
        address: {
            '@type': 'PostalAddress',
            streetAddress: 'Pramonės pr. 28',
            addressLocality: 'Kaunas',
            addressCountry: 'LT',
        },
        contactPoint: {
            '@type': 'ContactPoint',
            contactType: 'customer service',
            telephone: '+37060606227',
            email: 'info@devsprendimai.lt',
            availableLanguage: ['lt', 'en'],
        },
    },
    {
        '@type': 'WebSite',
        '@id': `${baseUrl.value}/#website`,
        url: `${baseUrl.value}/`,
        name: 'DevSprendimai',
        inLanguage: isEnglish.value ? 'en' : 'lt-LT',
        publisher: { '@id': organizationId.value },
    },
    {
        '@type': 'WebPage',
        '@id': `${homeUrl.value}#webpage`,
        url: homeUrl.value,
        name: seoTitle.value,
        description: seoDescription.value,
        inLanguage: isEnglish.value ? 'en' : 'lt-LT',
        isPartOf: { '@id': `${baseUrl.value}/#website` },
        about: { '@id': organizationId.value },
    },
    {
        '@type': 'FAQPage',
        '@id': `${homeUrl.value}#faq`,
        mainEntity: (isEnglish.value ? faqItemsEn : faqItems).map((item) => ({
            '@type': 'Question',
            name: item.q,
            acceptedAnswer: { '@type': 'Answer', text: item.a },
        })),
    },
])
</script>

<template>
    <SeoHead
        :title="seoTitle"
        :description="seoDescription"
        :image="socialImage"
        :image-alt="isEnglish ? 'DevSprendimai digital product studio' : 'DevSprendimai skaitmeninių sprendimų studija'"
        :structured-data="homeStructuredData"
    />

    <PublicLayout>
        <Hero />
        <Services />
        <Pricing />
        <Projects :projects="projects" />
        <About />
        <Process />
        <FAQ />
        <WebsiteCTA />
    </PublicLayout>
</template>
