<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { useLocale } from '@/composables/useLocale'
import { openConsentSettings } from '@/lib/analytics'
import { legalPagesLt, legalPagesEn } from '@/data/legal'

const documents = { lt: legalPagesLt, en: legalPagesEn }

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['privacy', 'cookies', 'terms'].includes(value),
    },
})

const { isEnglish, localize } = useLocale()
const page = usePage()
const updated = computed(() => props.type === 'terms' ? '2026-08-10' : '2026-10-15')

const document = computed(() => documents[isEnglish.value ? 'en' : 'lt'][props.type])
const navItems = computed(() => [
    ['privacy', isEnglish.value ? 'Privacy' : 'Privatumas', '/privatumo-politika'],
    ['cookies', isEnglish.value ? 'Cookies' : 'Slapukai', '/slapuku-politika'],
    ['terms', isEnglish.value ? 'Terms' : 'Sąlygos', '/paslaugu-teikimo-salygos'],
])
</script>

<template>
    <SeoHead :title="`${document.title} | DevSprendimai`" :description="document.description" />

    <article class="legal-page">
        <div class="legal-horizon" aria-hidden="true"></div>
        <div class="site-container">
            <nav class="legal-nav" :aria-label="isEnglish ? 'Legal documents' : 'Teisiniai dokumentai'">
                <Link v-for="item in navItems" :key="item[0]" :href="localize(item[2])" :class="{ active: item[0] === type }">{{ item[1] }}</Link>
            </nav>

            <header class="legal-hero">
                <span>{{ document.eyebrow }}</span>
                <h1>{{ document.title }}</h1>
                <p>{{ document.intro }}</p>
                <button v-if="type === 'cookies' && (page.props.analytics?.measurementId || page.props.analytics?.pageViews)" type="button" class="legal-consent-button" @click="openConsentSettings">{{ isEnglish ? 'Change cookie settings' : 'Keisti slapukų nustatymus' }}</button>
                <div class="legal-meta">
                    <span>{{ isEnglish ? 'Effective' : 'Galioja nuo' }} · {{ updated }}</span>
                    <i></i>
                    <span>DS / LEGAL</span>
                </div>
                <div class="legal-highlights">
                    <div v-for="(highlight, index) in document.highlights" :key="highlight"><small>0{{ index + 1 }}</small>{{ highlight }}</div>
                </div>
            </header>

            <div class="legal-layout">
                <aside>
                    <span>{{ isEnglish ? 'Contents' : 'Turinys' }}</span>
                    <a v-for="(section, index) in document.sections" :key="section.title" :href="`#legal-${index + 1}`"><small>0{{ index + 1 }}</small>{{ section.title.replace(/^\d+\.\s*/, '') }}</a>
                </aside>

                <div class="legal-content">
                    <section v-for="(section, index) in document.sections" :id="`legal-${index + 1}`" :key="section.title">
                        <span class="section-index">/ {{ String(index + 1).padStart(2, '0') }}</span>
                        <h2>{{ section.title }}</h2>
                        <p v-for="paragraph in section.paragraphs" :key="paragraph">{{ paragraph }}</p>
                        <ul v-if="section.bullets">
                            <li v-for="bullet in section.bullets" :key="bullet">{{ bullet }}</li>
                        </ul>
                        <div v-if="section.table" class="legal-table-wrap">
                            <table>
                                <thead><tr><th v-for="cell in section.table[0]" :key="cell">{{ cell }}</th></tr></thead>
                                <tbody><tr v-for="row in section.table.slice(1)" :key="row[0]"><td v-for="cell in row" :key="cell">{{ cell }}</td></tr></tbody>
                            </table>
                        </div>
                        <div v-if="section.links" class="legal-links">
                            <a v-for="link in section.links" :key="link.href" :href="link.href" target="_blank" rel="noopener noreferrer">{{ link.label }} <i>↗</i></a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </article>
</template>

<style scoped>
.legal-consent-button { margin-top:1.2rem; min-height:44px; padding:.75rem 1rem; border:1px solid #285361; border-radius:.75rem; color:#8beaf7; background:#0b1421; }
.legal-consent-button:focus-visible { outline:2px solid #67e8f9; outline-offset:4px; }
.legal-page { position:relative; min-height:100vh; overflow:hidden; padding:8rem 0 7rem; }
.legal-horizon { position:absolute; inset:0 0 auto; height:44rem; opacity:.45; background:radial-gradient(circle at 75% 18%,rgba(34,211,238,.15),transparent 30%),linear-gradient(rgba(34,211,238,.055) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,.055) 1px,transparent 1px); background-size:auto,52px 52px,52px 52px; mask-image:linear-gradient(to bottom,black,transparent); pointer-events:none; }
.legal-nav { position:relative; z-index:2; display:flex; width:max-content; max-width:100%; padding:.35rem; overflow-x:auto; border:1px solid rgba(255,255,255,.09); border-radius:999px; background:rgba(8,14,24,.7); backdrop-filter:blur(16px); }
.legal-nav a { padding:.7rem 1rem; border-radius:999px; color:#718097; font-size:.68rem; font-weight:800; white-space:nowrap; transition:.2s ease; }
.legal-nav a:hover { color:#dbeafe; }
.legal-nav a.active { color:#041016; background:#22d3ee; }
.legal-hero { position:relative; z-index:1; max-width:920px; padding:clamp(3rem,8vw,7rem) 0 5rem; }
.legal-hero > span { color:#67e8f9; font-size:.7rem; font-weight:850; letter-spacing:.2em; text-transform:uppercase; }
.legal-hero h1 { margin-top:1rem; font-size:clamp(3.4rem,8vw,7.5rem); font-weight:850; letter-spacing:-.075em; line-height:.9; }
.legal-hero > p { max-width:760px; margin-top:1.8rem; color:#91a0b5; font-size:clamp(1rem,1.5vw,1.18rem); line-height:1.8; }
.legal-meta { display:flex; align-items:center; gap:.8rem; margin-top:1.5rem; color:#506077; font-size:.62rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
.legal-meta i { width:.3rem; height:.3rem; border-radius:50%; background:#22d3ee; box-shadow:0 0 12px #22d3ee; }
.legal-highlights { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin-top:2.8rem; }
.legal-highlights div { display:flex; align-items:center; gap:.7rem; min-height:4rem; padding:0 1rem; border:1px solid rgba(255,255,255,.08); border-radius:1rem; color:#aebacc; background:rgba(8,15,26,.62); font-size:.76rem; font-weight:750; }
.legal-highlights small { color:#22d3ee; font-size:.58rem; font-weight:900; }
.legal-layout { position:relative; display:grid; grid-template-columns:250px minmax(0,760px); align-items:start; justify-content:space-between; gap:clamp(3rem,8vw,8rem); }
.legal-layout aside { position:sticky; top:7rem; display:flex; flex-direction:column; border-top:1px solid rgba(255,255,255,.1); }
.legal-layout aside > span { padding:1.1rem 0; color:#526178; font-size:.62rem; font-weight:850; letter-spacing:.17em; text-transform:uppercase; }
.legal-layout aside a { display:grid; grid-template-columns:2rem 1fr; padding:.7rem 0; color:#718097; font-size:.72rem; line-height:1.35; transition:color .2s ease; }
.legal-layout aside a:hover { color:#67e8f9; }
.legal-layout aside small { color:#344258; font-size:.56rem; }
.legal-content section { position:relative; padding:0 0 3.5rem 3.2rem; scroll-margin-top:7rem; }
.legal-content section + section { padding-top:3.5rem; border-top:1px solid rgba(255,255,255,.08); }
.section-index { position:absolute; left:0; top:.35rem; color:#22d3ee; font-size:.58rem; font-weight:900; letter-spacing:.08em; }
.legal-content section + section .section-index { top:3.85rem; }
.legal-content h2 { margin-bottom:1.25rem; color:#edf7ff; font-size:clamp(1.45rem,2.5vw,2rem); font-weight:800; letter-spacing:-.035em; }
.legal-content p { color:#8998ad; line-height:1.85; }
.legal-content p + p { margin-top:1rem; }
.legal-content ul { display:grid; gap:.75rem; margin-top:1.25rem; }
.legal-content li { position:relative; padding-left:1.3rem; color:#8998ad; line-height:1.75; }
.legal-content li::before { content:''; position:absolute; left:0; top:.7rem; width:.35rem; height:.35rem; border:1px solid #22d3ee; transform:rotate(45deg); }
.legal-links { display:flex; flex-wrap:wrap; gap:.65rem; margin-top:1.4rem; }
.legal-links a { padding:.65rem .85rem; border:1px solid rgba(34,211,238,.18); border-radius:.7rem; color:#74ddea; background:rgba(34,211,238,.04); font-size:.7rem; font-weight:750; }
.legal-links i { font-style:normal; }
.legal-table-wrap { margin-top:1.25rem; overflow-x:auto; border:1px solid rgba(255,255,255,.08); border-radius:1rem; }
.legal-table-wrap table { width:100%; min-width:560px; border-collapse:collapse; }
.legal-table-wrap th,.legal-table-wrap td { padding:1rem; border-bottom:1px solid rgba(255,255,255,.07); color:#8998ad; font-size:.75rem; line-height:1.5; text-align:left; }
.legal-table-wrap th { color:#67e8f9; background:rgba(34,211,238,.035); font-size:.62rem; letter-spacing:.08em; text-transform:uppercase; }
.legal-table-wrap tr:last-child td { border-bottom:0; }
@media (max-width:900px) {
    .legal-layout { grid-template-columns:1fr; }
    .legal-layout aside { position:relative; top:auto; display:none; }
}
@media (max-width:640px) {
    .legal-page { padding:6rem 0 5rem; }
    .legal-nav { width:100%; }
    .legal-nav a { flex:1; text-align:center; }
    .legal-hero { padding:3.5rem 0 3rem; }
    .legal-hero h1 { font-size:clamp(3rem,15vw,4.3rem); overflow-wrap:anywhere; }
    .legal-highlights { grid-template-columns:1fr; }
    .legal-highlights div { min-height:3.5rem; }
    .legal-content section,.legal-content section + section { padding-left:0; padding-top:3rem; padding-bottom:3rem; }
    .legal-content section:first-child { padding-top:0; }
    .section-index,.legal-content section + section .section-index { position:relative; display:block; top:auto; margin-bottom:.6rem; }
}
</style>
