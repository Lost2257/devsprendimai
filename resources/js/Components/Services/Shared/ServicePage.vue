<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import WebsiteCTA from '@/Components/Services/Shared/WebsiteCTA.vue'
import HeroScene from '@/Components/HeroScene.vue'
import { useLocale } from '@/composables/useLocale'
import { englishServices } from '@/data/services.en'
import { sendProjectBrief } from '@/lib/projectPlanner'
import { pricedService } from '@/data/pricing'
import { siteContent } from '@/data/siteContent'
import { serviceGuides } from '@/data/serviceGuides'
import ServiceGuide from '@/Components/Services/Shared/ServiceGuide.vue'

const page = usePage()
const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
})
const { isEnglish, localize } = useLocale()
const config = computed(() => {
    const source = isEnglish.value ? (englishServices[props.config.title] || props.config) : props.config
    return pricedService(source, props.config.title, isEnglish.value)
})
const copy = computed(() => siteContent[isEnglish.value ? 'en' : 'lt'])
const serviceSlug = computed(() => (page.url || '').split(/[?#]/)[0].replace(/^\/en(?=\/|$)/, '').replace(/^\/+|\/+$/g, ''))
const guide = computed(() => serviceGuides[isEnglish.value ? 'en' : 'lt'][serviceSlug.value])
const baseUrl = computed(() => (page.props.appUrl || '').replace(/\/$/, ''))
const cleanPath = computed(() => (page.url || '/').split('?')[0])
const canonicalUrl = computed(() => `${baseUrl.value}${cleanPath.value}`)
const socialImage = computed(() => `${page.props.appUrl || ''}/og.png`)
const serviceTitle = computed(() => `${config.value.title} | DevSprendimai`)
const serviceStructuredData = computed(() => [
    {
        '@type': 'BreadcrumbList',
        '@id': `${canonicalUrl.value}#breadcrumb`,
        itemListElement: [
            { '@type': 'ListItem', position: 1, name: isEnglish.value ? 'Home' : 'Pradžia', item: `${baseUrl.value}${isEnglish.value ? '/en' : '/'}` },
            { '@type': 'ListItem', position: 2, name: config.value.title, item: canonicalUrl.value },
        ],
    },
    {
        '@type': 'Service',
        '@id': `${canonicalUrl.value}#service`,
        name: config.value.title,
        serviceType: config.value.eyebrow,
        description: config.value.seoDescription,
        url: canonicalUrl.value,
        areaServed: { '@type': 'Country', name: 'Lithuania' },
        provider: { '@id': `${baseUrl.value}/#organization` },
    },
    {
        '@type': 'WebPage',
        '@id': `${canonicalUrl.value}#webpage`,
        url: canonicalUrl.value,
        name: serviceTitle.value,
        description: config.value.seoDescription,
        inLanguage: isEnglish.value ? 'en' : 'lt-LT',
        breadcrumb: { '@id': `${canonicalUrl.value}#breadcrumb` },
        mainEntity: { '@id': `${canonicalUrl.value}#service` },
    },
])
const serviceVisual = ref(null)
const discussOffer = (offer) => sendProjectBrief({
    service: config.value.service,
    message: isEnglish.value
        ? `I am interested in “${offer.title}”. Please help me confirm a suitable scope and final price.`
        : `Domina „${offer.title}“. Norėčiau suderinti tinkamą apimtį ir galutinę kainą.`,
})

const moveVisual = (event) => {
    const visual = serviceVisual.value
    if (!visual) return
    const rect = visual.getBoundingClientRect()
    const x = (event.clientX - rect.left) / rect.width - .5
    const y = (event.clientY - rect.top) / rect.height - .5
    visual.style.setProperty('--visual-x', `${y * -3}deg`)
    visual.style.setProperty('--visual-y', `${x * 4}deg`)
    visual.style.setProperty('--visual-light-x', `${(x + .5) * 100}%`)
    visual.style.setProperty('--visual-light-y', `${(y + .5) * 100}%`)
}

const resetVisual = () => {
    serviceVisual.value?.style.setProperty('--visual-x', '0deg')
    serviceVisual.value?.style.setProperty('--visual-y', '0deg')
}

onMounted(() => {
    const enable = window.matchMedia('(hover: hover) and (pointer: fine)').matches
        && !window.matchMedia('(prefers-reduced-motion: reduce)').matches
    if (!enable) return
    serviceVisual.value?.addEventListener('pointermove', moveVisual, { passive: true })
    serviceVisual.value?.addEventListener('pointerleave', resetVisual)
})

onUnmounted(() => {
    serviceVisual.value?.removeEventListener('pointermove', moveVisual)
    serviceVisual.value?.removeEventListener('pointerleave', resetVisual)
})

</script>

<template>
    <SeoHead
        :title="serviceTitle"
        :description="config.seoDescription"
        :image="socialImage"
        :image-alt="config.title"
        :structured-data="serviceStructuredData"
    />

    <section class="service-hero">
        <HeroScene accent-color="#22D3EE" :variant="config.service" />
        <div class="service-orb service-orb-one"></div>
        <div class="service-orb service-orb-two"></div>

        <div class="site-container service-hero-grid">
            <div class="service-copy">
                <Link :href="localize('/')" :prefetch="['hover', 'click']" cache-for="30s" view-transition class="back-link" :aria-label="isEnglish ? 'Back to home page' : 'Grįžti į pradžios puslapį'">
                    <span aria-hidden="true">←</span> {{ isEnglish ? 'Home' : 'Pradžia' }}
                </Link>

                <div class="eyebrow">{{ config.eyebrow }}</div>

                <h1>
                    {{ config.headline }}
                    <span>{{ config.highlight }}</span>
                </h1>

                <p class="service-lead">{{ config.description }}</p>

                <div class="service-actions">
                    <a href="#contact" class="button-primary">
                        {{ isEnglish ? 'Discuss a project' : 'Aptarkime projektą' }} <span aria-hidden="true">↗</span>
                    </a>
                    <a href="#service-pricing" class="button-secondary">{{ isEnglish ? 'View prices' : 'Peržiūrėti kainas' }}</a>
                </div>

                <div class="proof-row" aria-label="Pagrindiniai privalumai">
                    <span v-for="proof in config.proofs" :key="proof">
                        <i aria-hidden="true"></i>{{ proof }}
                    </span>
                </div>
            </div>

            <div ref="serviceVisual" class="service-visual" aria-hidden="true">
                <div class="visual-topbar">
                    <span>{{ config.visualLabel }}</span>
                    <div class="live-status"><i></i> {{ isEnglish ? 'Live' : 'Veikia' }}</div>
                </div>

                <div class="visual-heading">{{ config.visualTitle }}</div>

                <div class="flow">
                    <div v-for="(proof, index) in config.proofs" :key="proof" class="flow-row">
                        <span class="flow-number">0{{ index + 1 }}</span>
                        <div class="flow-line"><i :style="{ width: `${88 - index * 13}%` }"></i></div>
                        <span class="flow-check">✓</span>
                    </div>
                </div>

                <div class="metric-grid">
                    <div v-for="metric in config.metrics" :key="metric.label" class="metric-card">
                        <strong>{{ metric.value }}</strong>
                        <span>{{ metric.label }}</span>
                    </div>
                </div>

                <div class="visual-shine"></div>
            </div>
        </div>
    </section>

    <section id="sprendimas" class="section-block">
        <div class="site-container">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">{{ isEnglish ? 'Why it works' : 'Kodėl tai veikia' }}</span>
                    <h2>{{ config.benefitsTitle }}</h2>
                </div>
                <p>{{ config.benefitsLead }}</p>
            </div>

            <div class="section-swipe-hint"><span>{{ isEnglish ? 'Swipe cards' : 'Perbraukite korteles' }}</span><i>→</i></div>
            <div class="benefit-grid mobile-snap">
                <article v-for="benefit in config.benefits" :key="benefit.number" class="benefit-card">
                    <span class="card-number">{{ benefit.number }}</span>
                    <h3>{{ benefit.title }}</h3>
                    <p>{{ benefit.description }}</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section-block section-tinted">
        <div class="site-container feature-grid">
            <div class="feature-copy">
                <span class="section-kicker">{{ isEnglish ? 'Service scope' : 'Paslaugos apimtis' }}</span>
                <h2>{{ config.featureTitle }}</h2>
                <p>{{ config.featureLead }}</p>
                <a href="#contact" class="text-link">{{ isEnglish ? 'Get a tailored proposal' : 'Gauti individualų pasiūlymą' }} <span>→</span></a>
            </div>

            <div class="feature-list">
                <div v-for="(feature, index) in config.features" :key="feature" class="feature-item">
                    <span>{{ String(index + 1).padStart(2, '0') }}</span>
                    <strong>{{ feature }}</strong>
                    <i aria-hidden="true">↗</i>
                </div>
            </div>
        </div>
    </section>

    <section id="service-pricing" class="section-block">
        <div class="site-container">
            <div class="center-heading">
                <span class="section-kicker">{{ isEnglish ? 'Solution options' : 'Sprendimo kryptys' }}</span>
                <h2>{{ config.pricingTitle }}</h2>
                <p>{{ isEnglish ? 'Every solution is adapted to your goals, priorities and current stage of growth.' : 'Galutinis sprendimas visada pritaikomas pagal jūsų tikslus, prioritetus ir dabartinį verslo etapą.' }}</p>
            </div>

            <div class="section-swipe-hint"><span>{{ isEnglish ? 'Swipe options' : 'Perbraukite variantus' }}</span><i>→</i></div>
            <div class="offer-grid mobile-snap">
                <article
                    v-for="offer in config.offers"
                    :key="offer.title"
                    class="offer-card"
                    :class="{ featured: offer.featured }"
                >
                    <span v-if="offer.tag" class="offer-tag">{{ offer.tag }}</span>
                    <div v-if="offer.price" class="offer-price"><small>{{ isEnglish ? 'from' : 'nuo' }}</small><strong>{{ offer.price }}</strong></div>
                    <h3>{{ offer.title }}</h3>
                    <p>{{ offer.description }}</p>
                    <ul>
                        <li v-for="point in offer.points" :key="point"><span>✓</span>{{ point }}</li>
                    </ul>
                    <a href="#contact" @click.prevent="discussOffer(offer)">{{ isEnglish ? 'Discuss this option' : 'Aptarti šį variantą' }} <span>→</span></a>
                </article>
            </div>
            <p class="offer-note">{{ copy.pricingNote }}</p>
        </div>
    </section>

    <section class="section-block process-section">
        <div class="site-container">
            <div class="center-heading">
                <span class="section-kicker">{{ isEnglish ? 'Clear process' : 'Aiškus procesas' }}</span>
                <h2>{{ isEnglish ? 'From conversation to outcome' : 'Nuo pokalbio iki rezultato' }}</h2>
            </div>

            <div class="process-grid">
                <article v-for="(step, index) in config.process" :key="step.title" class="process-card">
                    <span>{{ String(index + 1).padStart(2, '0') }}</span>
                    <h3>{{ step.title }}</h3>
                    <p>{{ step.text }}</p>
                </article>
            </div>
        </div>
    </section>

    <ServiceGuide v-if="guide" :guide="guide" />
    <WebsiteCTA :service="config.service" />
</template>

<style scoped>
#service-pricing { scroll-margin-top: 6rem; }
.offer-note { max-width: 78ch; margin: 1.4rem auto 0; color: #94a3b8; font-size: .85rem; line-height: 1.7; text-align: center; }
.service-hero {
    position: relative;
    overflow: hidden;
    padding: 8.5rem 0 7rem;
}

.service-hero::before {
    content: '';
    position: absolute;
    z-index: 1;
    inset: 0;
    background: linear-gradient(90deg, rgba(11, 15, 25, .5), transparent 52%), linear-gradient(180deg, rgba(11, 15, 25, .04), transparent 70%, #0b0f19 100%);
    pointer-events: none;
}

.service-orb {
    position: absolute;
    z-index: 1;
    border-radius: 999px;
    filter: blur(100px);
    pointer-events: none;
}

.service-orb-one {
    width: 34rem;
    height: 34rem;
    right: -10rem;
    top: 5rem;
    background: rgba(34, 211, 238, .14);
    animation: orb-one 10s ease-in-out infinite;
}

.service-orb-two {
    width: 26rem;
    height: 26rem;
    left: 25%;
    bottom: -16rem;
    background: rgba(79, 70, 229, .12);
    animation: orb-two 12s -4s ease-in-out infinite;
}

.service-hero-grid {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: minmax(0, 1.12fr) minmax(360px, .88fr);
    align-items: center;
    gap: clamp(3rem, 7vw, 7rem);
}

.service-hero-grid > * { min-width: 0; }

.back-link {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    margin-bottom: 2rem;
    color: #7f8ca3;
    font-size: .82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .15em;
    transition: color .2s ease;
}

.back-link:hover { color: #67e8f9; }

.eyebrow,
.section-kicker {
    color: #67e8f9;
    font-size: .76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .24em;
}

.service-copy h1 {
    max-width: 800px;
    overflow-wrap: anywhere;
    margin-top: 1.4rem;
    font-size: clamp(3rem, 6.2vw, 6.6rem);
    font-weight: 900;
    letter-spacing: -.065em;
    line-height: .94;
}

.service-copy h1 span {
    display: block;
    color: transparent;
    background: linear-gradient(100deg, #22d3ee, #60a5fa 50%, #818cf8);
    background-clip: text;
    -webkit-background-clip: text;
}

.service-lead {
    max-width: 650px;
    margin-top: 1.8rem;
    color: #9aa8bd;
    font-size: clamp(1.05rem, 1.5vw, 1.22rem);
    line-height: 1.75;
}

.service-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .8rem;
    margin-top: 2.25rem;
}

.button-primary,
.button-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 3.45rem;
    padding: 0 1.5rem;
    border-radius: 999px;
    font-weight: 800;
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}

.button-primary {
    gap: .65rem;
    color: #071018;
    background: #22d3ee;
    box-shadow: 0 12px 35px rgba(34, 211, 238, .22);
}

.button-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 42px rgba(34, 211, 238, .34); }

.button-secondary {
    color: #e2e8f0;
    border: 1px solid rgba(255, 255, 255, .12);
    background: rgba(255, 255, 255, .035);
}

.button-secondary:hover { transform: translateY(-2px); border-color: rgba(103, 232, 249, .5); }

.proof-row {
    display: flex;
    flex-wrap: wrap;
    gap: .8rem 1.35rem;
    margin-top: 2rem;
    color: #8190a7;
    font-size: .84rem;
    font-weight: 600;
}

.proof-row span { display: flex; align-items: center; gap: .5rem; }
.proof-row i { width: 5px; height: 5px; border-radius: 50%; background: #22d3ee; box-shadow: 0 0 12px #22d3ee; }

.service-visual {
    position: relative;
    min-height: 500px;
    padding: 1.7rem;
    overflow: hidden;
    border: 1px solid rgba(103, 232, 249, .17);
    border-radius: 2rem;
    background: linear-gradient(145deg, rgba(17, 26, 43, .82), rgba(10, 16, 28, .68));
    box-shadow: 0 52px 125px rgba(0, 0, 0, .55), 0 0 48px rgba(34, 211, 238, .07), inset 0 1px rgba(255, 255, 255, .06);
    transform: perspective(1200px) rotateY(calc(-4deg + var(--visual-y, 0deg))) rotateX(calc(2deg + var(--visual-x, 0deg))) translateZ(22px);
    transform-style: preserve-3d;
    backdrop-filter: blur(12px);
    transition: transform .28s ease-out, border-color .3s ease;
    animation: visual-in .9s .12s cubic-bezier(.2,.7,.2,1) backwards;
}

.service-visual:hover { border-color: rgba(103,232,249,.24); }

.service-visual::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: .28;
    background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 32px 32px;
    mask-image: linear-gradient(to bottom, black, transparent 90%);
}

.service-visual::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    opacity: .65;
    background: radial-gradient(340px circle at var(--visual-light-x, 70%) var(--visual-light-y, 25%), rgba(103,232,249,.1), transparent 65%);
    pointer-events: none;
}

.visual-topbar,
.visual-heading,
.flow,
.metric-grid { position: relative; z-index: 2; }

.visual-topbar { display: flex; align-items: center; justify-content: space-between; color: #718198; font-size: .75rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
.live-status { display: flex; align-items: center; gap: .45rem; color: #67e8f9; }
.live-status i { width: 7px; height: 7px; border-radius: 50%; background: #22d3ee; box-shadow: 0 0 12px #22d3ee; }
.visual-heading { max-width: 350px; margin-top: 3rem; font-size: clamp(1.8rem, 3vw, 2.7rem); font-weight: 850; letter-spacing: -.04em; line-height: 1.08; }
.flow { display: grid; gap: .85rem; margin-top: 2.6rem; }
.flow-row { display: grid; grid-template-columns: 2rem 1fr 1.8rem; align-items: center; gap: .8rem; padding: .9rem; border: 1px solid rgba(255,255,255,.07); border-radius: .9rem; background: rgba(255,255,255,.025); animation: flow-row-in .65s cubic-bezier(.2,.7,.2,1) both; }
.flow-row:nth-child(1) { animation-delay: .38s; }
.flow-row:nth-child(2) { animation-delay: .5s; }
.flow-row:nth-child(3) { animation-delay: .62s; }
.flow-number { color: #64748b; font-size: .7rem; font-weight: 800; }
.flow-line { height: 4px; overflow: hidden; border-radius: 9px; background: rgba(255,255,255,.07); }
.flow-line i { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #22d3ee, #6366f1); box-shadow: 0 0 12px rgba(34,211,238,.35); transform-origin: left; animation: flow-fill 1.1s .65s cubic-bezier(.2,.7,.2,1) both; }
.flow-check { display: grid; place-items: center; width: 1.55rem; height: 1.55rem; border-radius: 50%; color: #67e8f9; background: rgba(34,211,238,.12); font-size: .72rem; }
.metric-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; margin-top: .9rem; }
.metric-card { display: flex; flex-direction: column; padding: 1.1rem; border: 1px solid rgba(255,255,255,.07); border-radius: 1rem; background: rgba(4,10,20,.55); }
.metric-card strong { color: #f8fafc; font-size: 1.35rem; letter-spacing: -.03em; }
.metric-card span { margin-top: .25rem; color: #718198; font-size: .72rem; }
.visual-shine { position: absolute; width: 15rem; height: 15rem; right: -6rem; bottom: -6rem; border-radius: 50%; background: rgba(34,211,238,.17); filter: blur(70px); animation: shine-drift 7s ease-in-out infinite; }

.section-block { position: relative; padding: clamp(5rem, 9vw, 8.5rem) 0; }
.section-tinted { border-block: 1px solid rgba(255,255,255,.06); background: rgba(255,255,255,.018); }
.section-heading { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(300px, .65fr); align-items: end; gap: 3rem; margin-bottom: 3.5rem; }
.section-heading h2,
.feature-copy h2,
.center-heading h2 { margin-top: 1rem; font-size: clamp(2.3rem, 4vw, 4.3rem); font-weight: 850; letter-spacing: -.055em; line-height: 1.02; }
.section-heading > p,
.feature-copy > p,
.center-heading > p { color: #8d9bb0; font-size: 1.05rem; line-height: 1.75; }
.benefit-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
.section-swipe-hint { display: none; }
.benefit-card { min-height: 285px; padding: 2rem; border: 1px solid rgba(255,255,255,.08); border-radius: 1.5rem; background: rgba(255,255,255,.025); transition: transform .25s ease, border-color .25s ease, background .25s ease; }
.benefit-card:hover { transform: translateY(-6px); border-color: rgba(34,211,238,.28); background: rgba(34,211,238,.035); }
.card-number { display: inline-block; margin-bottom: 4.7rem; color: #22d3ee; font-size: .75rem; font-weight: 800; letter-spacing: .18em; }
.benefit-card h3,
.offer-card h3,
.process-card h3 { font-size: 1.45rem; font-weight: 800; letter-spacing: -.025em; }
.benefit-card p,
.offer-card p,
.process-card p { margin-top: .8rem; color: #8593a8; line-height: 1.7; }

.feature-grid { display: grid; grid-template-columns: minmax(0, .8fr) minmax(420px, 1.1fr); align-items: start; gap: clamp(4rem, 9vw, 9rem); }
.feature-copy { position: sticky; top: 8rem; }
.feature-copy > p { margin-top: 1.5rem; }
.text-link { display: inline-flex; gap: .7rem; margin-top: 2rem; color: #67e8f9; font-weight: 800; }
.text-link span { transition: transform .2s ease; }
.text-link:hover span { transform: translateX(4px); }
.feature-list { border-top: 1px solid rgba(255,255,255,.1); }
.feature-item { display: grid; grid-template-columns: 2.5rem 1fr 1.5rem; align-items: center; gap: 1rem; min-height: 5.25rem; border-bottom: 1px solid rgba(255,255,255,.1); }
.feature-item > span { color: #5c6a7e; font-size: .7rem; font-weight: 800; }
.feature-item strong { font-size: clamp(1.05rem, 2vw, 1.35rem); }
.feature-item i { color: #22d3ee; font-style: normal; opacity: .45; transition: opacity .2s ease, transform .2s ease; }
.feature-item:hover i { opacity: 1; transform: translate(3px, -3px); }

.center-heading { max-width: 760px; margin: 0 auto 3.5rem; text-align: center; }
.center-heading > p { margin-top: 1.25rem; }
.offer-grid { display: grid; grid-template-columns: repeat(3, 1fr); align-items: stretch; gap: 1rem; }
.offer-card { position: relative; display: flex; flex-direction: column; min-height: 410px; padding: 2rem; border: 1px solid rgba(255,255,255,.09); border-radius: 1.5rem; background: linear-gradient(145deg, rgba(255,255,255,.035), rgba(255,255,255,.018)); }
.offer-card.featured { border-color: rgba(34,211,238,.36); box-shadow: 0 25px 70px rgba(34,211,238,.08); }
.offer-tag { align-self: flex-start; margin-bottom: 1.2rem; padding: .45rem .7rem; border: 1px solid rgba(34,211,238,.24); border-radius: 999px; color: #67e8f9; background: rgba(34,211,238,.08); font-size: .67rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
.offer-price { display:flex; align-items:baseline; gap:.5rem; margin-bottom:1rem; }
.offer-price small { color:#64748b; font-size:.64rem; font-weight:800; text-transform:uppercase; }
.offer-price strong { color:#f0fbff; font-size:2rem; font-weight:850; letter-spacing:-.05em; }
.offer-card ul { display: grid; gap: .75rem; margin-top: 2rem; color: #b3bfd0; }
.offer-card li { display: flex; align-items: center; gap: .7rem; }
.offer-card li span { color: #22d3ee; font-size: .8rem; }
.offer-card > a { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 2rem; color: #e5e7eb; font-weight: 800; }
.offer-card > a span { color: #22d3ee; }

.process-section { overflow: hidden; background: linear-gradient(180deg, rgba(34,211,238,.025), transparent); }
.process-grid { display: grid; grid-template-columns: repeat(4, 1fr); position: relative; }
.process-grid::before { content: ''; position: absolute; left: 5%; right: 5%; top: 1.35rem; height: 1px; background: linear-gradient(90deg, transparent, rgba(34,211,238,.5), transparent); }
.process-card { position: relative; padding: 0 1.25rem; }
.process-card > span { position: relative; z-index: 2; display: grid; place-items: center; width: 2.7rem; height: 2.7rem; margin-bottom: 2rem; border: 1px solid rgba(34,211,238,.35); border-radius: 50%; color: #67e8f9; background: #0b0f19; font-size: .68rem; font-weight: 800; box-shadow: 0 0 25px rgba(34,211,238,.1); }

@keyframes visual-in { from { opacity: 0; transform: perspective(1200px) rotateY(-4deg) rotateX(2deg) translateY(32px); } }
@keyframes flow-row-in { from { opacity: 0; transform: translateX(22px); } }
@keyframes flow-fill { from { transform: scaleX(0); } }
@keyframes shine-drift { 50% { transform: translate(-3rem,-2rem) scale(1.2); opacity: .65; } }
@keyframes orb-one { 50% { transform: translate(-3rem,2rem) scale(1.12); } }
@keyframes orb-two { 50% { transform: translate(4rem,-2rem) scale(.9); } }

@media (max-width: 1023px) {
    .service-hero { padding-top: 7rem; }
    .service-hero-grid { grid-template-columns: minmax(0, 1fr); }
    .service-visual { width: min(100%, 650px); min-height: 450px; transform: none; }
    .feature-grid { grid-template-columns: 1fr; }
    .feature-copy { position: static; }
    .benefit-grid,
    .offer-grid { grid-template-columns: 1fr; }
    .benefit-card { min-height: auto; }
    .card-number { margin-bottom: 3rem; }
    .offer-card { min-height: 360px; }
    .process-grid { grid-template-columns: 1fr 1fr; gap: 3rem 1rem; }
    .process-grid::before { display: none; }
}

@media (max-width: 767px) {
    .service-hero { padding: 5.5rem 0 3.8rem; }
    .service-hero-grid { gap: 2.8rem; }
    .back-link { margin-bottom: 1.4rem; font-size: .7rem; }
    .eyebrow { font-size: .68rem; letter-spacing: .18em; }
    .service-copy h1 { margin-top: 1rem; font-size: clamp(2.7rem, 13.1vw, 4.25rem); line-height: .94; }
    .service-lead { margin-top: 1.3rem; font-size: .98rem; line-height: 1.7; }
    .service-actions { flex-direction: column; }
    .button-primary,
    .button-secondary { width: 100%; min-height: 3.45rem; }
    .proof-row { display: flex; flex-wrap: nowrap; gap: .55rem; margin: 1.4rem 0 0; padding: 0 0 .35rem; overflow-x: auto; scroll-snap-type: x proximity; scrollbar-width: none; }
    .proof-row::-webkit-scrollbar { display: none; }
    .proof-row span { flex: none; min-height: 2.2rem; padding: 0 .75rem; border: 1px solid rgba(255,255,255,.075); border-radius: 999px; background: rgba(255,255,255,.025); scroll-snap-align: start; }
    .service-visual { min-height: 370px; padding: 1.05rem; border-radius: 1.3rem; transform: none; }
    .visual-topbar { font-size: .62rem; }
    .visual-heading { margin-top: 2rem; font-size: 2rem; }
    .flow { gap: .65rem; margin-top: 1.8rem; }
    .flow-row { grid-template-columns: 1.7rem 1fr 1.7rem; padding: .72rem; }
    .metric-card { padding: .85rem; }
    .metric-card strong { font-size: 1.12rem; }
    .metric-card span { font-size: .64rem; }
    .section-block { padding-block: 4.5rem; }
    .section-heading { grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1.5rem; }
    .section-heading h2,
    .feature-copy h2,
    .center-heading h2 { font-size: clamp(2.25rem, 10.8vw, 3.3rem); line-height: 1; }
    .section-heading > p,
    .feature-copy > p,
    .center-heading > p { font-size: .95rem; line-height: 1.7; }
    .section-swipe-hint { display: flex; align-items: center; justify-content: space-between; margin: 0 0 .75rem; color: #5d6b80; font-size: .64rem; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .section-swipe-hint i { color: #67e8f9; font-size: 1rem; font-style: normal; animation: swipe-indicator 1.8s ease-in-out infinite; }
    .benefit-grid,
    .offer-grid { display: flex; gap: .75rem; margin-inline: 0; padding: .2rem 0 1rem; overflow-x: auto; }
    .benefit-card,
    .offer-card { flex: 0 0 min(84vw, 370px); scroll-snap-align: center; }
    .benefit-card:first-child,
    .offer-card:first-child { scroll-snap-align: start; }
    .benefit-card { min-height: 280px; padding: 1.5rem; }
    .card-number { margin-bottom: 3.4rem; }
    .feature-grid { gap: 3rem; }
    .feature-list { min-width: 0; }
    .feature-item { grid-template-columns: 1.8rem minmax(0, 1fr) 1rem; min-height: 4.6rem; gap: .7rem; }
    .feature-item strong { font-size: 1rem; }
    .center-heading { margin: 0 0 1.75rem; text-align: left; }
    .offer-card { min-height: 370px; padding: 1.5rem; }
    .process-grid { grid-template-columns: 1fr; gap: 2.5rem; }
    .process-card { padding: 0 0 0 4rem; min-height: 7rem; }
    .process-card > span { position: absolute; left: 0; top: 0; margin: 0; }
    .process-card:not(:last-child)::after { content: ''; position: absolute; left: 1.34rem; top: 3.05rem; bottom: -2.15rem; width: 1px; background: linear-gradient(rgba(34,211,238,.35),rgba(34,211,238,.05)); }
}

@keyframes swipe-indicator { 50% { transform: translateX(7px); } }

@media (prefers-reduced-motion: reduce) {
    .service-visual,
    .service-orb,
    .flow-row,
    .flow-line i,
    .visual-shine { animation: none; transform: none; }
    .section-swipe-hint i { animation: none; }
}
</style>
