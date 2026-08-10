<script setup>
import { computed, ref } from 'vue'
import { faqItems, faqItemsEn } from '@/data/faq'
import { useLocale } from '@/composables/useLocale'

const active = ref(0)
const { isEnglish } = useLocale()
const items = computed(() => isEnglish.value ? faqItemsEn : faqItems)

const toggle = (index) => { active.value = active.value === index ? -1 : index }
</script>

<template>
    <section id="duk" class="faq-section">
        <div class="faq-orbit" aria-hidden="true"><i></i><i></i><b>?</b></div>
        <div class="faq-intro">
            <span class="eyebrow">{{ isEnglish ? 'Before we begin' : 'Prieš pradedant' }}</span>
            <h2>{{ isEnglish ? 'Clear answers.' : 'Aiškūs atsakymai.' }}<br><em>{{ isEnglish ? 'No vague promises.' : 'Jokių miglotų pažadų.' }}</em></h2>
            <p>{{ isEnglish ? 'A good project begins with clarity. These are the questions we most often cover during the first conversation.' : 'Geras projektas prasideda nuo aiškumo. Štai klausimai, kuriuos dažniausiai aptariame pirmojo pokalbio metu.' }}</p>
            <a href="#contact">{{ isEnglish ? 'Discuss your idea' : 'Aptarti savo idėją' }} <i>↗</i></a>
        </div>
        <div class="faq-list">
            <article v-for="(item, index) in items" :key="item.q" :class="{ open: active === index }" class="faq-item">
                <button type="button" :aria-expanded="active === index" @click="toggle(index)">
                    <span>{{ String(index + 1).padStart(2, '0') }}</span><strong>{{ item.q }}</strong><i>{{ active === index ? '−' : '+' }}</i>
                </button>
                <div class="answer"><div><p>{{ item.a }}</p></div></div>
            </article>
        </div>
    </section>
</template>

<style scoped>
.faq-section{position:relative;display:grid;grid-template-columns:minmax(280px,.78fr) minmax(0,1.22fr);gap:clamp(3rem,8vw,8rem);overflow:hidden;padding:clamp(7rem,12vw,11rem) max(1.5rem,calc((100vw - 1240px)/2));border-top:1px solid rgba(255,255,255,.06)}.faq-intro{position:relative;z-index:1}.eyebrow{color:#22d3ee;font-size:.67rem;font-weight:850;letter-spacing:.2em;text-transform:uppercase}.faq-intro h2{margin-top:1.5rem;font-size:clamp(2.6rem,5.3vw,5.3rem);font-weight:850;letter-spacing:-.065em;line-height:.95}.faq-intro h2 em{color:#718096;font-style:normal}.faq-intro p{max-width:32rem;margin-top:1.8rem;color:#7e8da2;font-size:clamp(.82rem,1.1vw,.96rem);line-height:1.8}.faq-intro>a{display:inline-flex;align-items:center;gap:1.5rem;margin-top:2rem;padding:.8rem 1rem;border:1px solid rgba(34,211,238,.2);border-radius:.55rem;color:#b9f5fb;font-size:.7rem;font-weight:800}.faq-intro>a i{color:#22d3ee;font-style:normal}.faq-list{position:relative;z-index:1;border-top:1px solid rgba(255,255,255,.1)}.faq-item{border-bottom:1px solid rgba(255,255,255,.1)}.faq-item button{display:grid;width:100%;grid-template-columns:2.5rem 1fr auto;align-items:center;gap:1rem;padding:1.5rem 0;color:#edf5ff;text-align:left}.faq-item button>span{color:#4f5d70;font-size:.58rem;font-weight:850}.faq-item button>strong{font-size:clamp(.9rem,1.5vw,1.2rem);font-weight:750;letter-spacing:-.02em;transition:color .25s}.faq-item button>i{display:grid;width:2rem;height:2rem;place-items:center;border:1px solid rgba(255,255,255,.1);border-radius:50%;color:#22d3ee;font-size:1rem;font-style:normal;transition:transform .3s,border-color .3s}.faq-item.open button>strong,.faq-item button:hover>strong{color:#67e8f9}.faq-item.open button>i{border-color:rgba(34,211,238,.35);transform:rotate(180deg)}.answer{display:grid;grid-template-rows:0fr;transition:grid-template-rows .4s cubic-bezier(.2,.8,.2,1)}.answer>div{overflow:hidden}.answer p{max-width:42rem;padding:0 3.5rem 1.7rem;color:#7e8da2;font-size:.82rem;line-height:1.8}.faq-item.open .answer{grid-template-rows:1fr}.faq-orbit{position:absolute;left:-9rem;bottom:-11rem;width:28rem;height:28rem;border:1px solid rgba(34,211,238,.055);border-radius:50%}.faq-orbit i{position:absolute;inset:4rem;border:1px solid rgba(34,211,238,.04);border-radius:50%}.faq-orbit i:nth-child(2){inset:8rem}.faq-orbit b{position:absolute;right:3rem;top:5rem;color:rgba(34,211,238,.1);font-size:8rem}
@media(max-width:820px){.faq-section{grid-template-columns:1fr;gap:3.5rem;padding-inline:1.25rem}.faq-intro p{margin-top:1.2rem}.faq-list{width:100%}}@media(max-width:540px){.faq-section{padding-inline:1rem}.faq-item button{grid-template-columns:1.8rem 1fr auto;gap:.65rem;padding:1.25rem 0}.faq-item button>i{width:1.8rem;height:1.8rem}.answer p{padding-left:2.45rem;padding-right:.25rem}}
@media(prefers-reduced-motion:reduce){.answer,.faq-item button>i{transition:none}}
</style>
