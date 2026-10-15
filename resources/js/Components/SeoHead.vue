<script setup>
import { computed, onBeforeUnmount, onMounted, watch, watchEffect } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { analytics } from '@/lib/analytics'

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, required: true },
    image: { type: String, default: '/og.png' },
    imageAlt: { type: String, default: 'DevSprendimai' },
    type: { type: String, default: 'website' },
    robots: { type: String, default: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' },
    structuredData: { type: Array, default: () => [] },
})

const page = usePage()
const robots = computed(() => page.props.seoIndexable === false ? 'noindex, nofollow, noarchive' : props.robots)
const baseUrl = computed(() => (page.props.appUrl || '').replace(/\/$/, ''))
const cleanPath = computed(() => (page.url || '/').split('?')[0].split('#')[0] || '/')
const canonicalUrl = computed(() => `${baseUrl.value}${cleanPath.value}`)
const languagePath = computed(() => cleanPath.value.replace(/^\/en(?=\/|$)/, '') || '/')
const lithuanianUrl = computed(() => `${baseUrl.value}${languagePath.value}`)
const englishUrl = computed(() => `${baseUrl.value}/en${languagePath.value === '/' ? '' : languagePath.value}`)
const isEnglishPage = computed(() => cleanPath.value === '/en' || cleanPath.value.startsWith('/en/'))
const imageUrl = computed(() => /^https?:\/\//i.test(props.image)
    ? props.image
    : `${baseUrl.value}/${props.image.replace(/^\//, '')}`)
const jsonLd = computed(() => props.structuredData.length
    ? JSON.stringify({ '@context': 'https://schema.org', '@graph': props.structuredData }).replace(/</g, '\\u003c')
    : '')
const structuredDataId = 'devsprendimai-structured-data'
let mounted = false
function recordPageView() {
    if (mounted) analytics?.configure(page.props.analytics?.measurementId, props.title)
}
onMounted(() => {
    mounted = true
    recordPageView()
})
watch(() => [page.url, props.title, page.props.analytics?.measurementId], recordPageView, { flush: 'post' })

watchEffect(() => {
    if (typeof document === 'undefined') return
    document.documentElement.lang = isEnglishPage.value ? 'en' : 'lt'
    document.querySelectorAll('link[data-seo-canonical]').forEach((link) => {
        if (link.hreflang === 'lt-LT') link.href = lithuanianUrl.value
        else if (link.hreflang === 'en') link.href = englishUrl.value
        else if (link.hreflang === 'x-default') link.href = lithuanianUrl.value
        else link.href = canonicalUrl.value
    })
    const values = {
        description: props.description,
        robots: robots.value,
        googlebot: robots.value,
        'og:locale': isEnglishPage.value ? 'en_US' : 'lt_LT',
        'og:locale:alternate': isEnglishPage.value ? 'lt_LT' : 'en_US',
        'og:type': props.type,
        'og:title': props.title,
        'og:description': props.description,
        'og:url': canonicalUrl.value,
        'og:image': imageUrl.value,
        'og:image:secure_url': imageUrl.value,
        'og:image:alt': props.imageAlt,
        'twitter:title': props.title,
        'twitter:description': props.description,
        'twitter:image': imageUrl.value,
        'twitter:image:alt': props.imageAlt,
    }
    Object.entries(values).forEach(([key, value]) => {
        const meta = document.querySelector(`meta[data-seo-meta="${key}"]`)
        if (meta) meta.content = value
    })

    let script = document.getElementById(structuredDataId)
    if (!jsonLd.value) {
        script?.remove()
        return
    }
    if (!script) {
        script = document.createElement('script')
        script.id = structuredDataId
        script.type = 'application/ld+json'
        document.head.appendChild(script)
    }
    script.textContent = jsonLd.value
})

onBeforeUnmount(() => {
    mounted = false
    if (typeof document !== 'undefined') document.getElementById(structuredDataId)?.remove()
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>
</template>
