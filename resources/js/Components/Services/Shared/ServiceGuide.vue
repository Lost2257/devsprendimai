<script setup>
import { Link } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'

defineProps({ guide: { type: Object, required: true } })
const { isEnglish, localize } = useLocale()
</script>

<template>
    <section class="service-guide" aria-labelledby="guide-title">
        <div class="site-container">
            <div class="guide-heading">
                <span>{{ isEnglish ? 'Before we start' : 'Prieš pradedant' }}</span>
                <h2 id="guide-title">{{ guide.title }}</h2>
                <p>{{ guide.intro }}</p>
            </div>
            <div class="guide-grid">
                <article v-for="item in guide.considerations" :key="item.title">
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.text }}</p>
                </article>
            </div>
            <div id="service-questions" class="guide-faq">
                <h2>{{ isEnglish ? 'Questions before choosing' : 'Klausimai prieš pasirenkant' }}</h2>
                <details v-for="item in guide.faq" :key="item.q">
                    <summary>{{ item.q }}</summary>
                    <p>{{ item.a }}</p>
                </details>
            </div>
            <nav class="guide-links" :aria-label="isEnglish ? 'Related services and work' : 'Susijusios paslaugos ir darbai'">
                <Link v-for="item in guide.related" :key="item.slug" :href="localize('/' + item.slug)">{{ item.label }} <span aria-hidden="true">↗</span></Link>
                <Link :href="localize('/') + '#projects'">{{ isEnglish ? 'View completed projects' : 'Peržiūrėti atliktus projektus' }} <span aria-hidden="true">↗</span></Link>
            </nav>
        </div>
    </section>
</template>

<style scoped>
.service-guide{padding-block:clamp(4rem,8vw,7rem);border-block:1px solid rgba(255,255,255,.07);background:linear-gradient(180deg,rgba(34,211,238,.025),transparent)}
.guide-heading{max-width:56rem}.guide-heading>span{color:#67e8f9;font-size:.7rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase}
.guide-heading h2,.guide-faq h2{margin-top:1rem;font-size:clamp(1.85rem,3.7vw,3.25rem);font-weight:800;letter-spacing:-.045em;line-height:1.15;overflow-wrap:break-word}
.guide-heading>p{margin-top:1.2rem;color:#a5b4c8;line-height:1.8}
.guide-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin-top:2rem}
.guide-grid article{padding:1.5rem;border:1px solid rgba(255,255,255,.09);border-radius:1rem;background:rgba(15,23,42,.55)}
.guide-grid h3{font-size:1.12rem;font-weight:750;line-height:1.4}.guide-grid p{margin-top:.8rem;color:#a5b4c8;font-size:.94rem;line-height:1.8}
.guide-faq{max-width:60rem;margin-top:3.5rem;scroll-margin-top:7rem}.guide-faq h2{font-size:clamp(1.65rem,3vw,2.5rem);margin-bottom:1.5rem}
.guide-faq details{border-bottom:1px solid rgba(255,255,255,.12)}.guide-faq summary{padding:1.2rem .25rem;min-height:48px;font-weight:700;line-height:1.5;cursor:pointer;color:#e2e8f0}.guide-faq summary::marker{color:#22d3ee}
.guide-faq details[open] summary{color:#67e8f9}.guide-faq details p{padding:0 .25rem 1.4rem;color:#a5b4c8;line-height:1.8;font-size:.95rem}
.guide-links{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:2rem}.guide-links a{display:inline-flex;align-items:center;gap:.75rem;min-height:44px;padding:.75rem 1rem;color:#b9f6fc;font-size:.85rem;border:1px solid rgba(34,211,238,.25);border-radius:.75rem;line-height:1.4}.guide-links a:hover{background:rgba(34,211,238,.07)}
.guide-faq summary:focus-visible,.guide-links a:focus-visible{outline:2px solid #67e8f9;outline-offset:4px}
@media(max-width:800px){.guide-grid{grid-template-columns:1fr}.guide-grid article{padding:1.25rem}.guide-links{display:grid;grid-template-columns:1fr}.guide-links a{justify-content:space-between}.guide-heading>p{font-size:.95rem}}
</style>
