<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import Hero from '@/Components/Hero.vue'
import Services from '@/Components/Services.vue'
import Pricing from '@/Components/Pricing.vue'
import ProjectPlanner from '@/Components/ProjectPlanner.vue'
import Projects from '@/Components/Projects.vue'
import About from '@/Components/About.vue'
import Process from '@/Components/Process.vue'
import FAQ from '@/Components/FAQ.vue'
import WebsiteCTA from '@/Components/Services/Shared/WebsiteCTA.vue'
import { faqItems, faqItemsEn } from '@/data/faq'
import { useLocale } from '@/composables/useLocale'
import { siteContent } from '@/data/siteContent'

const props = defineProps({
    projects: { type: Array, default: () => [] },
    settings: { type: Object, default: () => ({}) },
})

const page = usePage()
const { isEnglish } = useLocale()
const baseUrl = computed(() => (page.props.appUrl || '').replace(/\/$/, ''))
const copy = computed(() => siteContent[isEnglish.value ? 'en' : 'lt'])
const seoTitle = computed(() => (!isEnglish.value && props.settings?.site_title) || copy.value.seoTitle)
const seoDescription = computed(() => (!isEnglish.value && props.settings?.site_description) || copy.value.seoDescription)
const homeUrl = computed(() => `${baseUrl.value}${isEnglish.value ? '/en' : '/'}`)
const socialImage = computed(() => props.settings?.og_image
    ? `${page.props.appUrl || ''}/storage/${props.settings.og_image}`
    : `${page.props.appUrl || ''}/og.png`)
const organizationId = computed(() => `${baseUrl.value}/#organization`)
const homeStructuredData = computed(() => [
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
        <ProjectPlanner />
        <Projects :projects="projects" />
        <About />
        <Process />
        <FAQ />
        <WebsiteCTA />
    </PublicLayout>
</template>
