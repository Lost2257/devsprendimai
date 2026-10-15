<script setup>
import { computed } from 'vue'
import { useLocale } from '@/composables/useLocale'
import { sendProjectBrief } from '@/lib/projectPlanner'
import { getHomePackages } from '@/data/pricing'
import { siteContent } from '@/data/siteContent'

const { isEnglish } = useLocale()
const packages = computed(() => getHomePackages(isEnglish.value))
const copy = computed(() => siteContent[isEnglish.value ? 'en' : 'lt'])

const discussPackage = (item) => sendProjectBrief({
    service: item.service,
    message: isEnglish.value
        ? `I am interested in “${item.name}” (published starting price: ${item.price}). Please help me confirm a suitable scope and final price.`
        : `Domina „${item.name}“ (skelbiama startinė kaina: nuo ${item.price}). Norėčiau suderinti tinkamą apimtį ir galutinę kainą.`,
})
</script>

<template>
    <section id="pricing" class="pricing-section">
        <div class="site-container">
            <div class="pricing-heading">
                <div>
                    <span>{{ isEnglish ? 'Transparent starting prices' : 'Aiškios startinės kainos' }}</span>
                    <h2>{{ copy.pricingTitle }}<br><em>{{ copy.pricingHighlight }}</em></h2>
                </div>
                <p>{{ copy.pricingIntro }}</p>
            </div>

            <div class="pricing-grid mobile-snap">
                <article v-for="item in packages" :key="item.name" class="pricing-card" :class="{ featured: item.featured }">
                    <span v-if="item.tag" class="pricing-tag">{{ item.tag }}</span>
                    <small>{{ isEnglish ? 'from' : 'nuo' }}</small>
                    <strong>{{ item.price }}</strong>
                    <h3>{{ item.name }}</h3>
                    <p>{{ item.text }}</p>
                    <ul><li v-for="point in item.points" :key="point"><i>✓</i>{{ point }}</li></ul>
                    <a href="#contact" @click.prevent="discussPackage(item)">{{ isEnglish ? 'Discuss this option' : 'Aptarti šį variantą' }} <span>→</span></a>
                </article>
            </div>
            <p class="pricing-note">{{ copy.pricingNote }}</p>
        </div>
    </section>
</template>

<style scoped>
.pricing-section{position:relative;padding:clamp(6rem,10vw,9rem) 0;border-top:1px solid rgba(255,255,255,.06);overflow:hidden}.pricing-section::before{content:'';position:absolute;inset:10% auto auto 50%;width:60rem;height:38rem;border-radius:50%;background:rgba(34,211,238,.075);filter:blur(120px);transform:translateX(-50%);pointer-events:none}.pricing-heading{position:relative;display:grid;grid-template-columns:1.1fr .65fr;align-items:end;gap:3rem}.pricing-heading>div>span{color:#67e8f9;font-size:.7rem;font-weight:850;letter-spacing:.18em;text-transform:uppercase}.pricing-heading h2{margin-top:1.2rem;font-size:clamp(2.8rem,5.4vw,5.6rem);font-weight:850;letter-spacing:-.065em;line-height:.94}.pricing-heading h2 em{color:#738198;font-style:normal}.pricing-heading>p{color:#8997aa;line-height:1.75}.pricing-grid{position:relative;display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-top:3.5rem}.pricing-card{position:relative;display:flex;flex-direction:column;min-height:31rem;padding:1.7rem;border:1px solid rgba(255,255,255,.09);border-radius:1.4rem;background:linear-gradient(145deg,rgba(18,27,44,.82),rgba(8,13,23,.78));box-shadow:0 25px 70px rgba(0,0,0,.22)}.pricing-card.featured{border-color:rgba(34,211,238,.42);box-shadow:0 30px 80px rgba(0,0,0,.3),0 0 45px rgba(34,211,238,.09)}.pricing-tag{position:absolute;right:1rem;top:1rem;padding:.35rem .55rem;border-radius:999px;color:#061016;background:#22d3ee;font-size:.58rem;font-weight:900;text-transform:uppercase}.pricing-card>small{color:#627086;font-size:.65rem;font-weight:800;text-transform:uppercase}.pricing-card>strong{margin-top:.25rem;color:#eef8ff;font-size:clamp(2.2rem,3vw,3rem);letter-spacing:-.06em}.pricing-card h3{margin-top:1.4rem;font-size:1.2rem}.pricing-card>p{margin-top:.8rem;min-height:4.7rem;color:#8391a5;font-size:.82rem;line-height:1.65}.pricing-card ul{display:grid;gap:.7rem;margin-top:1.35rem}.pricing-card li{display:flex;gap:.65rem;color:#aeb9c8;font-size:.76rem}.pricing-card li i{color:#22d3ee;font-style:normal}.pricing-card>a{display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:1.4rem;border-top:1px solid rgba(255,255,255,.08);color:#b9f6fc;font-size:.72rem;font-weight:850}.pricing-note{position:relative;margin-top:1.4rem;color:#5f6c80;font-size:.7rem;text-align:center}
@media(max-width:1050px){.pricing-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:700px){.pricing-section{padding-inline:0}.pricing-heading{grid-template-columns:1fr;gap:1.25rem}.pricing-grid{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;margin-inline:-1rem;padding:0 1rem 1rem;scrollbar-width:none}.pricing-card{flex:0 0 min(84vw,21rem);min-height:29rem;scroll-snap-align:center}.pricing-note{text-align:left}.pricing-heading h2{font-size:clamp(2.55rem,12vw,3.7rem)}}
</style>
