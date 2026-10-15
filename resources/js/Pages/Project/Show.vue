<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ProjectScene from '@/Components/ProjectScene.vue'
import { useLocale } from '@/composables/useLocale'

const props = defineProps({
    project: { type: Object, required: true },
    nextProject: { type: Object, default: null },
})
const page = usePage()
const { isEnglish, localize } = useLocale()
const baseUrl = computed(() => (page.props.appUrl || '').replace(/\/$/, ''))
const cleanPath = computed(() => (page.url || '/').split('?')[0])
const canonicalUrl = computed(() => `${baseUrl.value}${cleanPath.value}`)

const activeStep = ref(0)
const pointerX = ref(0)
const pointerY = ref(0)
const touchStart = ref({ x: 0, y: 0 })
const storyDialog = ref(null)
const storyTrigger = ref(null)
const storyTitle = ref(null)
const storyOpen = ref(false)
let wheelLocked = false
let wheelTimer

const metrics = computed(() => [
    { value: props.project.metric_one_value, label: props.project.metric_one_label },
    { value: props.project.metric_two_value, label: props.project.metric_two_label },
    { value: props.project.metric_three_value, label: props.project.metric_three_label },
].filter((metric) => metric.value || metric.label))

const services = computed(() => (props.project.services || '').split(/[,\n]/).map((item) => item.trim()).filter(Boolean))
const technologies = computed(() => (props.project.technologies || '').split(/[,\n]/).map((item) => item.trim()).filter(Boolean))
const storySections = computed(() => [
    { key: 'description', title: isEnglish.value ? 'About the project' : 'Apie projektą', text: props.project.description },
    { key: 'challenge', title: isEnglish.value ? 'Challenge' : 'Iššūkis', text: props.project.challenge },
    { key: 'solution', title: isEnglish.value ? 'Solution' : 'Sprendimas', text: props.project.solution },
    { key: 'result', title: isEnglish.value ? 'Result' : 'Rezultatas', text: props.project.result },
].filter((section) => section.text?.trim()))
const projectDetails = computed(() => [
    { label: isEnglish.value ? 'Client' : 'Klientas', value: props.project.client },
    { label: isEnglish.value ? 'Industry' : 'Industrija', value: props.project.industry },
    { label: isEnglish.value ? 'Year' : 'Metai', value: props.project.year },
    { label: isEnglish.value ? 'Duration' : 'Trukmė', value: props.project.duration },
].filter((detail) => detail.value))
const relatedServices = computed(() => {
    const text = services.value.join(' ')
    return [
        { path: '/interneto-svetaines', label: isEnglish.value ? 'Website development' : 'Interneto svetainių kūrimas', match: /svetain|website|web design/i },
        { path: '/el-parduotuves', label: isEnglish.value ? 'E-commerce development' : 'El. parduotuvių kūrimas', match: /parduot|commerce|woocommerce/i },
        { path: '/aplikaciju-kurimas', label: isEnglish.value ? 'Application development' : 'Aplikacijų kūrimas', match: /aplikacij|application|mobile app/i },
        { path: '/crm-sistemos', label: isEnglish.value ? 'CRM systems' : 'CRM sistemos', match: /\bcrm\b|customer relationship/i },
        { path: '/automatizacija', label: isEnglish.value ? 'Business automation' : 'Verslo automatizacija', match: /automat/i },
        { path: '/svetainiu-prieziura', label: isEnglish.value ? 'Website maintenance' : 'Svetainių priežiūra', match: /priežiūr|maintenance/i },
    ].filter((service) => service.match.test(text))
})
const visualImages = computed(() => [...new Set([
    props.project.thumbnail,
    ...(props.project.gallery || []),
    props.project.mobile_thumbnail,
].filter(Boolean))])
const projectDescription = computed(() => props.project.description || props.project.subtitle || (isEnglish.value ? `${props.project.title}, a digital project by DevSprendimai.` : `${props.project.title} projektas, sukurtas DevSprendimai.`))
const projectSocialImage = computed(() => visualImages.value[0]
    ? `${baseUrl.value}/storage/${visualImages.value[0]}`
    : `${baseUrl.value}/og.png`)
const projectStructuredData = computed(() => [
    {
        '@type': 'BreadcrumbList',
        '@id': `${canonicalUrl.value}#breadcrumb`,
        itemListElement: [
            { '@type': 'ListItem', position: 1, name: isEnglish.value ? 'Home' : 'Pradžia', item: `${baseUrl.value}${isEnglish.value ? '/en' : '/'}` },
            { '@type': 'ListItem', position: 2, name: props.project.title, item: canonicalUrl.value },
        ],
    },
    {
        '@type': 'CreativeWork',
        '@id': `${canonicalUrl.value}#project`,
        name: props.project.title,
        headline: props.project.subtitle || props.project.title,
        description: projectDescription.value,
        url: canonicalUrl.value,
        image: visualImages.value.map((image) => `${baseUrl.value}/storage/${image}`),
        dateModified: props.project.updated_at,
        creator: { '@id': `${baseUrl.value}/#organization` },
        keywords: [...services.value, ...technologies.value].join(', '),
        inLanguage: isEnglish.value ? 'en' : 'lt-LT',
    },
    {
        '@type': 'WebPage',
        '@id': `${canonicalUrl.value}#webpage`,
        url: canonicalUrl.value,
        name: `${props.project.title} — ${isEnglish.value ? 'project' : 'projektas'} | DevSprendimai`,
        description: projectDescription.value,
        inLanguage: isEnglish.value ? 'en' : 'lt-LT',
        breadcrumb: { '@id': `${canonicalUrl.value}#breadcrumb` },
        mainEntity: { '@id': `${canonicalUrl.value}#project` },
    },
])

const slides = computed(() => {
    const images = visualImages.value
    const availableImages = images.slice(1)
    const singleVisual = (image) => image
        ? { visualMode: 'single', image, images: [] }
        : { visualMode: 'abstract', image: null, images: [] }
    const abstractVisual = { visualMode: 'abstract', image: null, images: [] }
    const takeImage = () => availableImages.shift() || null
    const items = [{
        type: 'overview', label: isEnglish.value ? 'Overview' : 'Apžvalga', eyebrow: props.project.industry || (isEnglish.value ? 'Digital project' : 'Skaitmeninis projektas'),
        title: props.project.title, text: props.project.subtitle || props.project.description, ...singleVisual(images[0]),
    }]

    if (props.project.challenge) {
        const visual = images.length >= 3 ? singleVisual(takeImage()) : abstractVisual
        items.push({ type: 'story', label: isEnglish.value ? 'Challenge' : 'Iššūkis', eyebrow: isEnglish.value ? '01 · Challenge' : '01 · Iššūkis', title: isEnglish.value ? 'The problem we needed to solve' : 'Problema, kurią reikėjo išspręsti', text: props.project.challenge, ...visual })
    }
    if (props.project.solution) {
        const visual = images.length >= 2 ? singleVisual(takeImage()) : abstractVisual
        items.push({ type: 'story', label: isEnglish.value ? 'Solution' : 'Sprendimas', eyebrow: isEnglish.value ? '02 · Solution' : '02 · Sprendimas', title: isEnglish.value ? 'The direction that turned an idea into a product' : 'Kryptis, pavertusi idėją produktu', text: props.project.solution, ...visual })
    }
    if (props.project.result) {
        let visual = abstractVisual
        if (images.length === 2) {
            visual = { visualMode: 'duo', image: null, images: images.slice(0, 2) }
            availableImages.length = 0
        } else if (availableImages.length) {
            visual = singleVisual(takeImage())
        }
        items.push({ type: 'result', label: isEnglish.value ? 'Results' : 'Rezultatas', eyebrow: isEnglish.value ? '03 · Results' : '03 · Rezultatas', title: isEnglish.value ? 'The impact after launch' : 'Pokytis po projekto paleidimo', text: props.project.result, ...visual })
    }

    availableImages.forEach((image, index) => items.push({
        type: 'visual', label: `${isEnglish.value ? 'Frame' : 'Kadras'} ${String(index + 1).padStart(2, '0')}`, eyebrow: isEnglish.value ? 'Project gallery' : 'Projekto galerija',
        title: `${props.project.title} · ${isEnglish.value ? 'details' : 'detalės'}`, text: isEnglish.value ? 'The product’s visual direction and responsive experience across devices.' : 'Produkto vizualinė kryptis ir responsyvi patirtis skirtinguose ekranuose.', ...singleVisual(image),
    }))

    return items
})

const currentSlide = computed(() => slides.value[activeStep.value] || slides.value[0])
const activeImage = computed(() => currentSlide.value?.image || null)
const ambientImages = computed(() => {
    if (visualImages.value.length < 3 || currentSlide.value?.visualMode !== 'single') return []
    return visualImages.value.filter((image) => image !== activeImage.value).slice(0, 2)
})
const sceneStyle = computed(() => ({
    '--project-accent': props.project.accent_color || '#22D3EE',
    '--pointer-x': `${pointerX.value * 13}px`, '--pointer-y': `${pointerY.value * 9}px`,
    '--space-x': `${pointerX.value * -4}px`, '--space-y': `${pointerY.value * -3}px`,
    '--world-yaw': `${pointerX.value * 2.6 + (activeStep.value % 5 - 2) * 1.15}deg`,
    '--world-pitch': `${pointerY.value * -1.8 + (activeStep.value % 3 - 1) * .7}deg`,
    '--world-depth': `${activeStep.value * -11}px`,
    '--stage-turn': `${-3 + activeStep.value * -.7}deg`, '--stage-depth': `${activeStep.value * -7}px`,
}))

const setStep = (index) => { activeStep.value = Math.min(slides.value.length - 1, Math.max(0, index)) }
const nextStep = () => setStep(activeStep.value === slides.value.length - 1 ? 0 : activeStep.value + 1)
const previousStep = () => setStep(activeStep.value === 0 ? slides.value.length - 1 : activeStep.value - 1)

const openStory = () => {
    if (!storyDialog.value || storyDialog.value.open) return
    storyDialog.value.showModal()
    storyOpen.value = true
    storyTitle.value?.focus({ preventScroll: true })
}
const closeStory = () => storyDialog.value?.close()
const finishClosingStory = () => {
    storyOpen.value = false
    if (storyTrigger.value?.isConnected) storyTrigger.value.focus({ preventScroll: true })
}

const handleWheel = (event) => {
    if (wheelLocked || Math.abs(event.deltaY) < 18) return
    wheelLocked = true
    event.deltaY > 0 ? nextStep() : previousStep()
    wheelTimer = window.setTimeout(() => { wheelLocked = false }, 650)
}
const handleKey = (event) => {
    if (storyOpen.value || event.defaultPrevented) return
    if (['ArrowRight', 'ArrowDown', 'PageDown'].includes(event.key)) nextStep()
    if (['ArrowLeft', 'ArrowUp', 'PageUp'].includes(event.key)) previousStep()
}
const startTouch = (event) => { touchStart.value = { x: event.touches[0].clientX, y: event.touches[0].clientY } }
const endTouch = (event) => {
    const dx = event.changedTouches[0].clientX - touchStart.value.x
    const dy = event.changedTouches[0].clientY - touchStart.value.y
    if (Math.max(Math.abs(dx), Math.abs(dy)) < 45) return
    Math.abs(dx) > Math.abs(dy) ? (dx < 0 ? nextStep() : previousStep()) : (dy < 0 ? nextStep() : previousStep())
}
const movePointer = (event) => {
    if (window.matchMedia('(pointer: coarse)').matches) return
    const rect = event.currentTarget.getBoundingClientRect()
    pointerX.value = ((event.clientX - rect.left) / rect.width - .5) * 2
    pointerY.value = ((event.clientY - rect.top) / rect.height - .5) * 2
}
const resetPointer = () => { pointerX.value = 0; pointerY.value = 0 }

onMounted(() => {
    document.documentElement.classList.add('project-immersive-active')
    window.addEventListener('keydown', handleKey)
})
onUnmounted(() => {
    document.documentElement.classList.remove('project-immersive-active')
    window.removeEventListener('keydown', handleKey)
    window.clearTimeout(wheelTimer)
})
</script>

<template>
    <SeoHead
        :title="`${project.title} — ${isEnglish ? 'project' : 'projektas'} | DevSprendimai`"
        :description="projectDescription"
        :image="projectSocialImage"
        :image-alt="`${project.title} ${isEnglish ? 'project preview' : 'projekto vizualizacija'}`"
        type="article"
        :structured-data="projectStructuredData"
    />

    <PublicLayout immersive>
        <article
            class="project-experience"
            :style="sceneStyle"
            tabindex="0"
            @wheel.prevent="handleWheel"
            @touchstart.passive="startTouch"
            @touchend.passive="endTouch"
            @pointermove="movePointer"
            @pointerleave="resetPointer"
        >
            <ProjectScene :active-step="activeStep" :accent-color="project.accent_color || '#22D3EE'" />
            <div :key="`scene-flash-${activeStep}`" class="scene-flash" aria-hidden="true"></div>
            <div class="space-world" aria-hidden="true">
                <div class="space-aura"></div><div class="grid-plane grid-floor"></div><div class="grid-plane grid-ceiling"></div>
                <div class="room-wall wall-left"></div><div class="room-wall wall-right"></div><div class="horizon-line"></div><div class="space-fog"></div>
                <div class="space-gate gate-one"></div><div class="space-gate gate-two"></div><div class="space-gate gate-three"></div>
                <div class="light-beam beam-left"></div><div class="light-beam beam-right"></div>
                <i class="particle particle-one"></i><i class="particle particle-two"></i><i class="particle particle-three"></i>
            </div>

            <header class="experience-topbar">
                <div class="project-breadcrumb"><Link :href="localize('/')" :prefetch="['hover','click']" cache-for="30s" view-transition>{{ isEnglish ? 'Home' : 'Pradžia' }}</Link><span>/</span><span>{{ project.title }}</span></div>
                <div class="experience-tools">
                    <button ref="storyTrigger" type="button" class="story-trigger" aria-haspopup="dialog" aria-controls="project-story-reader" @click="openStory">
                        {{ isEnglish ? 'Read full case study' : 'Visa projekto istorija' }} <span aria-hidden="true">↗</span>
                    </button>
                    <div class="space-status"><i></i><span>VIRTUAL PROJECT SPACE</span><b>{{ String(activeStep + 1).padStart(2, '0') }} / {{ String(slides.length).padStart(2, '0') }}</b></div>
                </div>
            </header>

            <section class="experience-stage">
                <Transition name="slide-copy" mode="out-in">
                    <section :key="activeStep" class="slide-copy" :class="`slide-${currentSlide.type}`">
                        <span>{{ currentSlide.eyebrow }}</span>
                        <h1>{{ currentSlide.title }}</h1>
                        <p>{{ currentSlide.text }}</p>

                        <div v-if="currentSlide.type === 'overview'" class="tag-list">
                            <i v-for="item in services.slice(0, 4)" :key="item">{{ item }}</i>
                        </div>
                        <div v-if="currentSlide.type === 'result' && metrics.length" class="mini-metrics">
                            <div v-for="metric in metrics" :key="metric.label"><strong>{{ metric.value }}</strong><span>{{ metric.label }}</span></div>
                        </div>
                        <div v-if="currentSlide.type === 'overview'" class="project-meta">
                            <span>{{ project.client || (isEnglish ? 'Project' : 'Projektas') }}</span><span>{{ project.year || 'Case study' }}</span><span>{{ project.duration || (isEnglish ? 'Custom scope' : 'Individualiai') }}</span>
                        </div>
                        <div v-if="currentSlide.type === 'overview'" class="tag-list technology-list">
                            <i v-for="item in technologies.slice(0, 4)" :key="item">{{ item }}</i>
                        </div>
                    </section>
                </Transition>

                <section class="visual-space" aria-live="polite">
                    <figure v-if="ambientImages[0]" class="ambient-screen ambient-left"><img :src="`/storage/${ambientImages[0]}`" alt=""></figure>
                    <figure v-if="ambientImages[1]" class="ambient-screen ambient-right"><img :src="`/storage/${ambientImages[1]}`" alt=""></figure>
                    <div class="live-screen">
                        <div class="screen-bar"><span>LIVE PROJECT</span><i></i><b>{{ String(activeStep + 1).padStart(2, '0') }}</b></div>
                        <Transition name="screen-swap" mode="out-in">
                            <div v-if="currentSlide.visualMode === 'duo'" :key="`duo-${activeStep}`" class="screen-duo">
                                <figure v-for="(image, index) in currentSlide.images" :key="image" :class="`duo-image duo-image-${index + 1}`">
                                    <img :src="`/storage/${image}`" :alt="`${project.title} — projekto vaizdas ${index + 1}`">
                                </figure>
                                <span>{{ isEnglish ? 'DESKTOP + MOBILE EXPERIENCE' : 'DARBASTALIO + MOBILIOJI PATIRTIS' }}</span>
                            </div>
                            <img v-else-if="activeImage" :key="activeImage" :src="`/storage/${activeImage}`" :alt="`${project.title} — ${currentSlide.label}`">
                            <div v-else :key="`abstract-${activeStep}`" class="screen-abstract">
                                <div class="abstract-orbit"><i></i><i></i><i></i><b></b></div>
                                <span>{{ isEnglish ? 'IMMERSIVE CASE STUDY' : 'INTERAKTYVI PROJEKTO ISTORIJA' }}</span>
                                <strong>{{ currentSlide.label }}</strong>
                                <small>{{ project.title }} · strategija · dizainas · rezultatas</small>
                            </div>
                        </Transition>
                        <div v-if="currentSlide.visualMode === 'single' && activeImage" class="screen-reflection"><img :src="`/storage/${activeImage}`" alt=""></div>
                        <div class="screen-glow"></div><i class="corner corner-a"></i><i class="corner corner-b"></i>
                    </div>
                </section>
            </section>

            <footer class="experience-controls">
                <div class="slide-tabs" aria-label="Projekto skyriai">
                    <button v-for="(slide, index) in slides" :key="`${slide.label}-${index}`" type="button" :class="{ active: activeStep === index }" @click="setStep(index)">
                        <i></i><span>{{ slide.label }}</span>
                    </button>
                </div>
                <div class="control-actions">
                    <a v-if="project.url" :href="project.url" target="_blank" rel="noopener noreferrer">{{ isEnglish ? 'Open project' : 'Atidaryti projektą' }} ↗</a>
                    <Link v-else-if="nextProject" :href="localize(`/projektai/${nextProject.slug}`)">{{ isEnglish ? 'Next project' : 'Kitas projektas' }} ↗</Link>
                    <div><button type="button" :aria-label="isEnglish ? 'Previous section' : 'Ankstesnis skyrius'" @click="previousStep">←</button><button type="button" :aria-label="isEnglish ? 'Next section' : 'Kitas skyrius'" @click="nextStep">→</button></div>
                </div>
            </footer>
        </article>

        <dialog
            id="project-story-reader"
            ref="storyDialog"
            class="story-reader"
            :style="{ '--project-accent': project.accent_color || '#22D3EE' }"
            aria-labelledby="project-story-title"
            @close="finishClosingStory"
        >
            <header class="story-reader-header">
                <span>{{ isEnglish ? 'Full case study' : 'Visa projekto istorija' }}</span>
                <button type="button" class="story-close" @click="closeStory">
                    {{ isEnglish ? 'Close' : 'Uždaryti' }} <span aria-hidden="true">×</span>
                </button>
            </header>
            <div class="story-reader-body">
                <h2 id="project-story-title" ref="storyTitle" tabindex="-1">{{ project.title }}</h2>
                <p v-if="project.subtitle" class="story-subtitle">{{ project.subtitle }}</p>

                <dl v-if="projectDetails.length" class="story-details">
                    <div v-for="detail in projectDetails" :key="detail.label">
                        <dt>{{ detail.label }}</dt>
                        <dd>{{ detail.value }}</dd>
                    </div>
                </dl>

                <section v-for="section in storySections" :key="section.key" class="story-section" :aria-labelledby="`story-${section.key}`">
                    <h3 :id="`story-${section.key}`">{{ section.title }}</h3>
                    <p>{{ section.text }}</p>
                </section>

                <section v-if="metrics.length" class="story-section" aria-labelledby="story-metrics">
                    <h3 id="story-metrics">{{ isEnglish ? 'Project outcomes' : 'Projekto rezultatai' }}</h3>
                    <ul class="story-metrics">
                        <li v-for="(metric, index) in metrics" :key="index">
                            <strong v-if="metric.value">{{ metric.value }}</strong>
                            <span v-if="metric.label">{{ metric.label }}</span>
                        </li>
                    </ul>
                </section>

                <section v-if="services.length" class="story-section" aria-labelledby="story-services">
                    <h3 id="story-services">{{ isEnglish ? 'Services' : 'Paslaugos' }}</h3>
                    <ul class="story-tags"><li v-for="(service, index) in services" :key="index">{{ service }}</li></ul>
                </section>
                <section v-if="technologies.length" class="story-section" aria-labelledby="story-technologies">
                    <h3 id="story-technologies">{{ isEnglish ? 'Technologies' : 'Technologijos' }}</h3>
                    <ul class="story-tags"><li v-for="(technology, index) in technologies" :key="index">{{ technology }}</li></ul>
                </section>

                <nav class="story-links" :aria-label="isEnglish ? 'Explore services and projects' : 'Paslaugos ir kiti projektai'">
                    <h3>{{ isEnglish ? 'Explore further' : 'Sužinokite daugiau' }}</h3>
                    <Link v-for="service in relatedServices" :key="service.path" :href="localize(service.path)" @click="closeStory">{{ service.label }} <span aria-hidden="true">↗</span></Link>
                    <Link :href="`${localize('/')}#projects`" @click="closeStory">{{ isEnglish ? 'All projects' : 'Visi projektai' }} <span aria-hidden="true">↗</span></Link>
                    <a v-if="project.url" :href="project.url" target="_blank" rel="noopener noreferrer">{{ isEnglish ? 'Visit the live project (new tab)' : 'Atidaryti projektą (naujame lange)' }} <span aria-hidden="true">↗</span></a>
                </nav>
            </div>
        </dialog>
    </PublicLayout>
</template>

<style scoped>
:global(html.project-immersive-active),:global(html.project-immersive-active body){height:100%;overflow:hidden;overscroll-behavior:none}
.project-experience{--project-accent:#22d3ee;position:relative;display:grid;width:100%;height:100svh;grid-template-rows:auto minmax(0,1fr) auto;overflow:hidden;padding:6.2rem max(1.2rem,calc((100vw - 1380px)/2)) 1rem;color:#eef7ff;background:#02060e;perspective:1000px;outline:none;isolation:isolate;touch-action:pan-x pan-y}
.scene-flash{position:absolute;z-index:0;inset:-15%;pointer-events:none;background:radial-gradient(ellipse at 62% 50%,color-mix(in srgb,var(--project-accent) 18%,transparent),transparent 27%),linear-gradient(108deg,transparent 37%,color-mix(in srgb,var(--project-accent) 10%,transparent) 49%,transparent 61%);mix-blend-mode:screen;animation:scene-flash .9s cubic-bezier(.16,1,.3,1) both}
.space-world{position:absolute;z-index:-1;inset:-2%;overflow:hidden;transform-style:preserve-3d;transform:translate3d(var(--space-x),var(--space-y),var(--world-depth)) rotateX(var(--world-pitch)) rotateY(var(--world-yaw)) scale(1.045);transition:transform .28s cubic-bezier(.2,.75,.2,1)}.space-aura{position:absolute;inset:0;background:radial-gradient(circle at 60% 48%,color-mix(in srgb,var(--project-accent) 15%,transparent),transparent 35%),radial-gradient(circle at 15% 80%,color-mix(in srgb,var(--project-accent) 7%,transparent),transparent 24%),#02060e;animation:aura-breathe 7s ease-in-out infinite}.grid-plane,.room-wall{position:absolute;background-image:linear-gradient(color-mix(in srgb,var(--project-accent) 14%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--project-accent) 14%,transparent) 1px,transparent 1px);background-size:62px 62px}.grid-plane{left:-25%;width:150%;height:78%;mask-image:linear-gradient(90deg,transparent,black 24%,black 76%,transparent);animation:grid-travel 12s linear infinite}.grid-floor{bottom:-31%;transform-origin:50% 0;transform:perspective(620px) rotateX(62deg)}.grid-ceiling{top:-32%;transform-origin:50% 100%;transform:perspective(620px) rotateX(-62deg);opacity:.38;animation-direction:reverse}.room-wall{top:-10%;bottom:-10%;width:62%;opacity:.2;mask-image:linear-gradient(transparent 2%,black 28%,black 72%,transparent 98%)}.wall-left{left:-44%;transform-origin:100% 50%;transform:perspective(700px) rotateY(68deg)}.wall-right{right:-44%;transform-origin:0 50%;transform:perspective(700px) rotateY(-68deg)}.horizon-line{position:absolute;left:7%;right:7%;top:50%;height:1px;background:linear-gradient(90deg,transparent,var(--project-accent),transparent);box-shadow:0 0 25px color-mix(in srgb,var(--project-accent) 70%,transparent);opacity:.23}.space-fog{position:absolute;left:-10%;right:-10%;bottom:-8%;height:42%;background:linear-gradient(transparent,rgba(3,9,17,.72) 62%,color-mix(in srgb,var(--project-accent) 7%,#02060e));filter:blur(12px);opacity:.75}.space-gate{position:absolute;left:50%;top:51%;width:80%;height:74%;border:1px solid color-mix(in srgb,var(--project-accent) 20%,transparent);border-radius:1.8rem;box-shadow:inset 0 0 55px rgba(0,0,0,.5),0 0 20px color-mix(in srgb,var(--project-accent) 4%,transparent);transition:transform .75s cubic-bezier(.2,.75,.2,1)}.gate-one{transform:translate(-50%,-50%) translateZ(calc(-90px + var(--world-depth)))}.gate-two{transform:translate(-50%,-50%) translateZ(calc(-340px + var(--world-depth))) scale(.82);opacity:.5}.gate-three{transform:translate(-50%,-50%) translateZ(calc(-610px + var(--world-depth))) scale(.62);opacity:.23}.light-beam{position:absolute;top:-10%;bottom:-10%;width:1px;background:linear-gradient(transparent,var(--project-accent),transparent);box-shadow:0 0 18px var(--project-accent);opacity:.28}.beam-left{left:9%;transform:rotate(8deg)}.beam-right{right:9%;transform:rotate(-8deg)}.particle{position:absolute;width:4px;height:4px;border-radius:50%;background:var(--project-accent);box-shadow:0 0 15px var(--project-accent);animation:float 5s ease-in-out infinite}.particle-one{left:8%;top:31%}.particle-two{right:12%;top:67%;animation-delay:-1.7s}.particle-three{left:47%;bottom:7%;animation-delay:-3.3s}
.experience-topbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding-bottom:.8rem;border-bottom:1px solid rgba(255,255,255,.07)}.project-breadcrumb{display:flex;min-width:0;gap:.6rem;color:#607087;font-size:.62rem;font-weight:750}.project-breadcrumb span:last-child{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.project-breadcrumb a:hover{color:var(--project-accent)}.space-status{display:flex;align-items:center;gap:.65rem;color:#536177;font-size:.5rem;font-weight:850;letter-spacing:.15em}.space-status i{width:5px;height:5px;border-radius:50%;background:var(--project-accent);box-shadow:0 0 10px var(--project-accent);animation:pulse 1.8s ease infinite}.space-status b{color:var(--project-accent)}
.experience-stage{display:grid;min-width:0;min-height:0;grid-template-columns:minmax(250px,.65fr) minmax(0,1.35fr);align-items:center;gap:clamp(1.5rem,4vw,4rem);padding:1.2rem 0}.slide-copy{position:relative;z-index:5;min-width:0;max-width:100%;max-height:100%;overflow-y:auto;padding:.4rem .25rem .4rem 0;scrollbar-width:thin;scrollbar-color:color-mix(in srgb,var(--project-accent) 25%,transparent) transparent}.slide-copy>span{color:var(--project-accent);font-size:.6rem;font-weight:850;letter-spacing:.18em;text-transform:uppercase}.slide-copy h1{max-width:100%;margin-top:.8rem;font-size:clamp(2.35rem,4.65vw,5rem);font-weight:850;letter-spacing:-.065em;line-height:.9;overflow-wrap:anywhere;text-wrap:balance}.slide-story h1,.slide-result h1{font-size:clamp(2.05rem,4vw,4.15rem);line-height:.94}.slide-copy p{max-width:580px;margin-top:1.05rem;color:#99a7b9;font-size:clamp(.76rem,1vw,.92rem);line-height:1.65;overflow-wrap:anywhere;white-space:pre-line}.tag-list{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:1rem}.tag-list i{padding:.46rem .62rem;border:1px solid rgba(255,255,255,.09);border-radius:999px;color:#a8b5c5;background:rgba(255,255,255,.025);font-size:.56rem;font-style:normal}.technology-list{margin-top:.45rem}.technology-list i{color:var(--project-accent);border-color:color-mix(in srgb,var(--project-accent) 18%,transparent)}.project-meta{display:flex;flex-wrap:wrap;gap:.6rem;margin-top:1rem;color:#64748b;font-size:.58rem}.project-meta span+span:before{content:'·';margin-right:.6rem}.mini-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.55rem;margin-top:1.1rem}.mini-metrics div{min-width:0;padding:.65rem;border:1px solid rgba(255,255,255,.075);border-radius:.65rem;background:rgba(3,8,15,.42)}.mini-metrics strong{display:block;color:var(--project-accent);font-size:1.25rem;letter-spacing:-.05em}.mini-metrics span{display:block;margin-top:.25rem;color:#718096;font-size:.48rem;line-height:1.35;overflow-wrap:anywhere}
.visual-space{position:relative;display:grid;min-height:0;height:min(62vh,650px);place-items:center;perspective:950px;transform-style:preserve-3d}.live-screen{position:relative;z-index:4;width:min(83%,820px);overflow:visible;border:1px solid color-mix(in srgb,var(--project-accent) 38%,rgba(255,255,255,.08));border-radius:1.2rem;background:#060b14;box-shadow:0 55px 130px rgba(0,0,0,.68),0 0 40px color-mix(in srgb,var(--project-accent) 11%,transparent);transform:translate3d(var(--pointer-x),var(--pointer-y),var(--stage-depth)) rotateY(var(--stage-turn));transition:transform .25s ease-out}.screen-bar{display:flex;align-items:center;gap:.55rem;height:2.5rem;padding:0 .85rem;border-bottom:1px solid rgba(255,255,255,.07);color:#637287;font-size:.45rem;font-weight:850;letter-spacing:.14em}.screen-bar i{width:5px;height:5px;border-radius:50%;background:var(--project-accent);box-shadow:0 0 10px var(--project-accent)}.screen-bar b{margin-left:auto;color:var(--project-accent)}.live-screen>img{display:block;width:100%;aspect-ratio:16/10;border-radius:0 0 1.15rem 1.15rem;object-fit:cover}.screen-duo{position:relative;display:grid;grid-template-columns:1.12fr .88fr;gap:clamp(.55rem,1.5vw,1.1rem);aspect-ratio:16/10;overflow:hidden;padding:clamp(1rem,3vw,2.4rem);background:radial-gradient(circle at 62% 42%,color-mix(in srgb,var(--project-accent) 13%,transparent),transparent 38%),linear-gradient(145deg,#07101a,#02050b);border-radius:0 0 1.15rem 1.15rem}.screen-duo::before,.screen-abstract::before{content:'';position:absolute;inset:0;background-image:linear-gradient(color-mix(in srgb,var(--project-accent) 8%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--project-accent) 8%,transparent) 1px,transparent 1px);background-size:34px 34px;mask-image:radial-gradient(circle at center,black,transparent 78%);pointer-events:none}.duo-image{position:relative;z-index:1;min-width:0;overflow:hidden;margin:0;border:1px solid color-mix(in srgb,var(--project-accent) 28%,transparent);border-radius:.75rem;background:#050a11;box-shadow:0 20px 45px rgba(0,0,0,.55);transform:perspective(700px) rotateY(5deg)}.duo-image-2{transform:translateY(9%) perspective(700px) rotateY(-6deg)}.duo-image img{width:100%;height:100%;object-fit:cover}.screen-duo>span{position:absolute;z-index:2;right:1rem;bottom:.55rem;color:var(--project-accent);font-size:.38rem;font-weight:850;letter-spacing:.16em}.screen-abstract{position:relative;display:grid;aspect-ratio:16/10;place-content:center;overflow:hidden;padding:1rem;text-align:center;background:radial-gradient(circle at center,color-mix(in srgb,var(--project-accent) 13%,transparent),transparent 34%),linear-gradient(145deg,#07101a,#02050b);border-radius:0 0 1.15rem 1.15rem}.screen-abstract>span,.screen-abstract>strong,.screen-abstract>small{position:relative;z-index:2}.screen-abstract>span{color:var(--project-accent);font-size:.5rem;font-weight:850;letter-spacing:.2em}.screen-abstract>strong{margin-top:.5rem;color:#f4f8fc;font-size:clamp(2rem,4.2vw,4rem);letter-spacing:-.06em;line-height:.9}.screen-abstract>small{margin-top:.65rem;color:#6d7d91;font-size:.55rem}.abstract-orbit{position:absolute;left:50%;top:50%;width:58%;aspect-ratio:1;transform:translate(-50%,-50%);opacity:.58}.abstract-orbit i{position:absolute;inset:0;border:1px solid color-mix(in srgb,var(--project-accent) 34%,transparent);border-radius:50%;animation:orbit-spin 13s linear infinite}.abstract-orbit i:nth-child(2){inset:13%;border-style:dashed;animation-direction:reverse;animation-duration:9s}.abstract-orbit i:nth-child(3){inset:28%;animation-duration:6s}.abstract-orbit b{position:absolute;left:50%;top:50%;width:7px;height:7px;border-radius:50%;background:var(--project-accent);box-shadow:0 0 25px 9px color-mix(in srgb,var(--project-accent) 38%,transparent);transform:translate(-50%,-50%)}.screen-glow{position:absolute;z-index:-1;left:12%;right:12%;bottom:-2.5rem;height:5rem;background:var(--project-accent);filter:blur(55px);opacity:.22}.corner{position:absolute;width:1.8rem;height:1.8rem;border-color:var(--project-accent);opacity:.62}.corner-a{left:-.45rem;top:-.45rem;border-left:1px solid;border-top:1px solid}.corner-b{right:-.45rem;bottom:-.45rem;border-right:1px solid;border-bottom:1px solid}.ambient-screen{position:absolute;z-index:1;overflow:hidden;width:29%;aspect-ratio:4/3;border:1px solid color-mix(in srgb,var(--project-accent) 22%,transparent);border-radius:.75rem;background:#07101a;box-shadow:0 30px 60px rgba(0,0,0,.62)}.ambient-screen img{width:100%;height:100%;object-fit:cover;opacity:.45}.ambient-left{left:0;top:17%;transform:translateZ(-170px) rotateY(24deg)}.ambient-right{right:0;bottom:14%;transform:translateZ(-150px) rotateY(-23deg)}
.visual-space::after{content:'';position:absolute;z-index:0;left:18%;right:12%;bottom:4%;height:14%;border-radius:50%;background:radial-gradient(ellipse,color-mix(in srgb,var(--project-accent) 13%,rgba(0,0,0,.45)),transparent 68%);filter:blur(14px);transform:rotateX(68deg)}.live-screen::before{content:'';position:absolute;z-index:-2;inset:6px -9px -10px 7px;border:1px solid rgba(255,255,255,.055);border-radius:1.25rem;background:linear-gradient(135deg,#111a27,#01040a 45%);box-shadow:12px 16px 28px rgba(0,0,0,.55);transform:translateZ(-18px)}.live-screen::after{content:'';position:absolute;z-index:6;inset:2.5rem 0 0;border-radius:0 0 1.15rem 1.15rem;background:linear-gradient(118deg,rgba(255,255,255,.12),transparent 16%,transparent 72%,color-mix(in srgb,var(--project-accent) 6%,transparent));pointer-events:none;mix-blend-mode:screen}.screen-reflection{position:absolute;z-index:-3;left:3%;right:3%;top:calc(100% + 12px);height:34%;overflow:hidden;opacity:.13;filter:blur(2px);transform:perspective(500px) rotateX(68deg) scaleY(-.72);transform-origin:50% 0;mask-image:linear-gradient(black,transparent 82%)}.screen-reflection img{width:100%;height:100%;object-fit:cover}.ambient-screen::after{content:'';position:absolute;inset:0;background:linear-gradient(120deg,rgba(255,255,255,.1),transparent 20%);pointer-events:none}
.experience-controls{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:1rem;padding-top:.75rem;border-top:1px solid rgba(255,255,255,.075)}.slide-tabs{display:flex;min-width:0;gap:.3rem;overflow-x:auto;scrollbar-width:none}.slide-tabs::-webkit-scrollbar{display:none}.slide-tabs button{display:flex;flex:1;min-width:4.5rem;align-items:center;gap:.45rem;padding:.45rem;color:#536177;font-size:.5rem;font-weight:800;white-space:nowrap}.slide-tabs button i{width:100%;max-width:2.8rem;height:2px;background:rgba(255,255,255,.09);transition:background .25s,box-shadow .25s}.slide-tabs button.active{color:#d8e5ee}.slide-tabs button.active i{background:var(--project-accent);box-shadow:0 0 10px var(--project-accent)}.control-actions{display:flex;align-items:center;gap:1rem}.control-actions>a{color:var(--project-accent);font-size:.58rem;font-weight:800}.control-actions>div{display:flex;gap:.35rem}.control-actions button{display:grid;width:2.25rem;height:2.25rem;place-items:center;border:1px solid rgba(255,255,255,.1);border-radius:50%;color:#b9c5d2;background:rgba(255,255,255,.025);font-size:.8rem}.control-actions button:hover{color:#061016;border-color:var(--project-accent);background:var(--project-accent)}
.slide-copy-enter-active,.slide-copy-leave-active,.screen-swap-enter-active,.screen-swap-leave-active{transition:opacity .3s ease,transform .35s ease}.slide-copy-enter-from{opacity:0;transform:translateY(18px)}.slide-copy-leave-to{opacity:0;transform:translateY(-14px)}.screen-swap-enter-from{opacity:0;transform:scale(1.025)}.screen-swap-leave-to{opacity:0;transform:scale(.985)}
.project-experience :deep(.webgl-scene){z-index:-2}.space-world{opacity:.54;mix-blend-mode:screen}.space-aura{background:radial-gradient(circle at 60% 48%,color-mix(in srgb,var(--project-accent) 9%,transparent),transparent 38%),radial-gradient(circle at 15% 80%,color-mix(in srgb,var(--project-accent) 4%,transparent),transparent 27%)}
@keyframes float{0%,100%{opacity:.25;transform:translateY(0)}50%{opacity:1;transform:translateY(-22px)}}@keyframes pulse{0%,100%{opacity:.35}50%{opacity:1}}@keyframes grid-travel{to{background-position:0 62px,62px 0}}@keyframes aura-breathe{0%,100%{opacity:.78;transform:scale(1)}50%{opacity:1;transform:scale(1.06)}}
@keyframes scene-flash{0%{opacity:0;transform:scale(.72) translateX(7%)}22%{opacity:1}100%{opacity:0;transform:scale(1.22) translateX(-3%)}}
@keyframes orbit-spin{to{transform:rotate(360deg)}}

@media(max-width:900px){.project-experience{padding-inline:1rem}.experience-stage{grid-template-columns:.75fr 1.25fr;gap:1.2rem}.ambient-screen{display:none}.live-screen{width:92%}.slide-copy h1{font-size:clamp(2.5rem,7vw,4.5rem)}}
@media(max-width:640px){.project-experience{padding:5.15rem .75rem max(.65rem,env(safe-area-inset-bottom));grid-template-rows:auto minmax(0,1fr) auto}.space-world{inset:-5%}.space-status span{display:none}.project-breadcrumb{max-width:63%}.experience-topbar{padding-bottom:.5rem}.experience-stage{grid-template-columns:1fr;grid-template-rows:minmax(0,.94fr) minmax(135px,1.06fr);gap:.45rem;padding:.5rem 0}.slide-copy{min-height:0;overflow:hidden;padding:0}.slide-copy>span{font-size:.45rem}.slide-copy h1{margin-top:.3rem;font-size:clamp(1.65rem,8vw,2.35rem);line-height:.94;letter-spacing:-.052em;text-wrap:pretty}.slide-overview h1{font-size:clamp(2rem,10vw,2.8rem);line-height:.9}.slide-story h1,.slide-result h1{font-size:clamp(1.45rem,7vw,2rem);line-height:.96}.slide-copy p{display:-webkit-box;overflow:hidden;margin-top:.45rem;font-size:.64rem;line-height:1.45;-webkit-box-orient:vertical;-webkit-line-clamp:3}.slide-overview p{-webkit-line-clamp:2}.tag-list{max-height:2.4rem;overflow:hidden;margin-top:.45rem}.tag-list i{padding:.27rem .38rem;font-size:.43rem}.technology-list{display:none}.project-meta{margin-top:.42rem;font-size:.44rem}.mini-metrics{gap:.25rem;margin-top:.45rem}.mini-metrics div{padding:.38rem}.mini-metrics strong{font-size:.88rem}.mini-metrics span{font-size:.36rem;line-height:1.2}.visual-space{height:100%;min-height:0}.live-screen{width:min(94%,520px);border-radius:.8rem;transform:translate3d(0,0,var(--stage-depth)) rotateY(-2deg)}.screen-bar{height:1.6rem;font-size:.33rem}.live-screen>img{max-height:28vh;border-radius:0 0 .75rem .75rem}.experience-controls{grid-template-columns:minmax(0,1fr) auto;padding-top:.4rem}.slide-tabs button{min-width:2.6rem;padding:.25rem}.slide-tabs button span{display:none}.slide-tabs button i{max-width:none}.control-actions>a{display:none}.control-actions{gap:.35rem}.control-actions button{width:1.9rem;height:1.9rem}.grid-plane{height:68%;background-size:38px 38px}.grid-floor{bottom:-19%}.grid-ceiling{top:-24%}.space-gate{width:92%;height:68%;border-radius:1rem}}
@media(max-width:380px){.project-experience{padding-inline:.6rem}.slide-copy h1{font-size:1.5rem}.slide-overview h1{font-size:2rem}.slide-story h1,.slide-result h1{font-size:1.35rem}.slide-copy p{font-size:.6rem}.mini-metrics span{display:none}}
@media(max-height:720px) and (max-width:640px){.project-experience{padding-top:4.7rem}.experience-stage{grid-template-rows:minmax(0,.9fr) minmax(125px,1.1fr)}.slide-copy h1{font-size:1.45rem}.slide-overview h1{font-size:1.9rem}.slide-story h1,.slide-result h1{font-size:1.28rem}.slide-copy p{-webkit-line-clamp:2}.tag-list{display:none}.project-meta{display:none}.live-screen>img{max-height:25vh}}
@media(max-height:600px) and (max-width:640px){.slide-copy p{display:none}.mini-metrics{display:none}.live-screen>img{max-height:23vh}}
@media(max-width:640px){.project-experience{padding:4.72rem max(.85rem,env(safe-area-inset-left)) max(.72rem,env(safe-area-inset-bottom)) max(.85rem,env(safe-area-inset-right));grid-template-rows:2.25rem minmax(0,1fr) 2.45rem}.experience-topbar{min-width:0;padding-bottom:.42rem}.project-breadcrumb{max-width:72%;font-size:.58rem}.space-status{gap:.45rem;font-size:.48rem}.experience-stage{grid-template-rows:auto minmax(0,1fr);align-content:stretch;gap:.62rem;padding:.62rem 0 .25rem}.slide-copy{align-self:start;overflow:hidden;padding:0 .08rem}.slide-copy>span{font-size:.5rem}.slide-copy h1{margin-top:.36rem}.slide-overview h1{font-size:clamp(2.15rem,10.8vw,3rem)}.slide-story h1,.slide-result h1{font-size:clamp(1.55rem,7.4vw,2.15rem)}.slide-copy p{margin-top:.52rem;font-size:.68rem;line-height:1.5}.tag-list{gap:.32rem;margin-top:.55rem}.tag-list i{padding:.3rem .44rem;font-size:.47rem}.project-meta{margin-top:.48rem;font-size:.47rem}.visual-space{height:100%;min-height:0;place-items:center;padding:0}.live-screen{width:100%;max-width:520px;border-radius:.9rem;transform:translate3d(0,0,var(--stage-depth)) rotateY(-1.25deg)}.screen-bar{height:1.75rem;padding-inline:.65rem;font-size:.36rem}.live-screen>img{width:100%;height:min(38svh,calc((100vw - 1.7rem) * .76));max-height:100%;aspect-ratio:auto;border-radius:0 0 .85rem .85rem;object-fit:cover}.screen-duo,.screen-abstract{width:100%;height:min(38svh,calc((100vw - 1.7rem) * .76));max-height:100%;aspect-ratio:auto;border-radius:0 0 .85rem .85rem}.screen-duo{gap:.45rem;padding:.65rem}.screen-duo>span{right:.55rem;bottom:.35rem;font-size:.28rem}.screen-abstract>strong{font-size:clamp(1.7rem,9vw,2.7rem)}.screen-abstract>small{font-size:.4rem}.screen-reflection{display:none}.experience-controls{min-width:0;padding-top:.42rem}.slide-tabs{gap:.22rem}.slide-tabs button{min-width:2.25rem;padding:.3rem .16rem}.control-actions button{width:2.15rem;height:2.15rem}.visual-space::after{bottom:-5%;height:18%}.room-wall{opacity:.13}.space-fog{height:34%;opacity:.55}.scene-flash{opacity:.62}}
@media(max-height:720px) and (max-width:640px){.project-experience{padding-top:4.45rem;grid-template-rows:2rem minmax(0,1fr) 2.25rem}.experience-stage{gap:.3rem;padding:.3rem 0 .15rem}.slide-copy h1{margin-top:.2rem}.slide-copy p{margin-top:.3rem;-webkit-line-clamp:2}.tag-list,.project-meta{display:none}.live-screen>img,.screen-duo,.screen-abstract{height:min(28svh,calc((100vw - 1.7rem) * .62))}.screen-bar{height:1.5rem}}
@media(max-height:600px) and (max-width:640px){.project-experience{padding-top:4.25rem;grid-template-rows:1.8rem minmax(0,1fr) 2.1rem}.experience-stage{gap:.18rem;padding:.18rem 0}.slide-copy>span,.slide-copy p,.mini-metrics{display:none}.slide-copy h1{font-size:1.35rem}.slide-overview h1{font-size:1.75rem}.live-screen>img,.screen-duo,.screen-abstract{height:min(25svh,calc((100vw - 1.7rem) * .55))}.screen-bar{height:1.35rem}}
.experience-tools{display:flex;flex-shrink:0;align-items:center;gap:1rem}
.story-trigger{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;min-height:44px;padding:.55rem .8rem;border:1px solid color-mix(in srgb,var(--project-accent) 35%,#263645);border-radius:.7rem;color:#d2f6ff;background:#08131e;font-size:.7rem;font-weight:750;line-height:1.3;white-space:nowrap}
.story-trigger:hover{background:#102333;border-color:var(--project-accent)}
.story-trigger:focus-visible,.story-reader :focus-visible{outline:2px solid var(--project-accent);outline-offset:3px}
.story-reader{position:fixed;inset:0;width:min(48rem,calc(100vw - 2rem));max-width:calc(100vw - 2rem);max-height:calc(100dvh - 2rem);margin:auto;padding:0;overflow:hidden;border:1px solid color-mix(in srgb,var(--project-accent) 35%,#273747);border-radius:1.2rem;color:#eef7ff;background:#08111d;box-shadow:0 24px 100px #0009}
.story-reader[open]{display:flex;flex-direction:column}
.story-reader::backdrop{background:rgba(0,5,12,.88)}
.story-reader-header{display:flex;flex-shrink:0;align-items:center;justify-content:space-between;gap:1rem;padding:.75rem 1.5rem;border-bottom:1px solid #263545;background:#0b1724}
.story-reader-header>span{color:#a8c9d9;font-size:.74rem;font-weight:750;letter-spacing:.04em}
.story-close{display:inline-flex;flex-shrink:0;align-items:center;justify-content:center;gap:.65rem;min-height:44px;padding:.5rem .75rem;border:1px solid #3d5668;border-radius:.65rem;color:#e2f4ff;background:#122535;font-size:.8rem;font-weight:700}
.story-close span{font-size:1.3rem;line-height:1}
.story-close:hover{background:#203a4b}
.story-reader-body{min-height:0;padding:1.75rem 2rem 2rem;overflow-y:auto;overscroll-behavior:contain;touch-action:pan-y;scrollbar-width:thin;scrollbar-color:#456779 #08111d;overflow-wrap:anywhere}
.story-reader h2{margin:0;font-size:clamp(1.6rem,5vw,2.5rem);font-weight:850;letter-spacing:-.04em;line-height:1.15}
.story-reader h3{margin:0 0 .75rem;font-size:1.05rem;font-weight:800;line-height:1.4;color:#eef7ff}
.story-reader p{margin:0;color:#bccad9;font-size:.95rem;line-height:1.8;white-space:pre-line}
.story-reader .story-subtitle{margin-top:.9rem;color:#d3e5f2;font-size:1.05rem}
.story-details{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem;margin:1.5rem 0 0;padding:1.25rem;border:1px solid #253747;border-radius:.8rem;background:#0d1b28}
.story-details dt{color:#95aec1;font-size:.72rem;font-weight:700}
.story-details dd{margin:.3rem 0 0;color:#e1f0fb;font-size:.88rem;line-height:1.5}
.story-section{margin-top:1.75rem}
.story-tags{display:flex;flex-wrap:wrap;gap:.5rem;margin:0;padding:0;list-style:none}
.story-tags li{max-width:100%;padding:.5rem .75rem;border:1px solid #294557;border-radius:.6rem;color:#c7ddec;background:#0d1e2b;font-size:.83rem;line-height:1.5}
.story-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem;margin:0;padding:0;list-style:none}
.story-metrics li{padding:1rem;border:1px solid #294557;border-radius:.8rem;background:#0d1e2b}
.story-metrics strong{display:block;color:var(--project-accent);font-size:1.5rem;line-height:1.3}
.story-metrics span{display:block;margin-top:.35rem;color:#c7ddec;font-size:.82rem;line-height:1.5}
.story-links{display:flex;flex-direction:column;align-items:flex-start;gap:.4rem;margin-top:2rem;padding-top:1.5rem;border-top:1px solid #263545}
.story-links h3{margin-bottom:.3rem}
.story-links a{display:inline-flex;align-items:center;gap:.7rem;min-height:44px;color:#8ae7f5;font-size:.9rem;line-height:1.5;text-decoration:underline;text-underline-offset:4px}
.story-links a:hover{color:#e3fbff}
@media(max-width:900px){.experience-tools{gap:.6rem}.experience-tools .space-status>span{display:none}}
@media(max-width:640px){
    .project-experience{grid-template-rows:3.25rem minmax(0,1fr) 2.45rem}
    .experience-topbar{gap:.5rem}
    .project-breadcrumb{flex:1;min-width:0;max-width:none;gap:.35rem}
    .experience-tools .space-status{display:none}
    .story-trigger{padding:.45rem .6rem;font-size:.65rem}
    .story-reader{width:calc(100vw - 1.5rem);max-width:calc(100vw - 1.5rem);max-height:calc(100dvh - max(1.5rem,env(safe-area-inset-top) + env(safe-area-inset-bottom)));border-radius:.85rem}
    .story-reader-header{gap:.5rem;padding:.55rem .85rem}
    .story-reader-header>span{font-size:.65rem}
    .story-close{padding:.45rem .6rem;font-size:.75rem}
    .story-reader-body{padding:1.25rem 1rem 1.5rem}
    .story-reader p{font-size:.9rem}
    .story-details{gap:.85rem;padding:1rem}
    .story-metrics{grid-template-columns:1fr}
    .story-metrics li{padding:.8rem 1rem}
}
@media(prefers-reduced-motion:reduce){.space-world,.live-screen,.slide-copy-enter-active,.slide-copy-leave-active,.screen-swap-enter-active,.screen-swap-leave-active{transition:none}.particle,.space-status i,.grid-plane,.space-aura,.scene-flash,.abstract-orbit i{animation:none}}
</style>
