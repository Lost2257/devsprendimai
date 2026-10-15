<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import HeroScene from '@/Components/HeroScene.vue'
import { useLocale } from '@/composables/useLocale'
import { siteContent } from '@/data/siteContent'

const hero = ref(null)
const { isEnglish } = useLocale()
const copy = computed(() => siteContent[isEnglish.value ? 'en' : 'lt'])
let finePointer = false

const resetMotion = () => {
    if (!hero.value) return
    hero.value.style.setProperty('--hero-x', '0px')
    hero.value.style.setProperty('--hero-y', '0px')
    hero.value.style.setProperty('--panel-x', '0deg')
    hero.value.style.setProperty('--panel-y', '0deg')
}

const moveHero = (event) => {
    if (!finePointer || !hero.value) return
    const rect = hero.value.getBoundingClientRect()
    const x = (event.clientX - rect.left) / rect.width - .5
    const y = (event.clientY - rect.top) / rect.height - .5
    hero.value.style.setProperty('--hero-x', `${x * 34}px`)
    hero.value.style.setProperty('--hero-y', `${y * 24}px`)
    hero.value.style.setProperty('--panel-x', `${y * -2.2}deg`)
    hero.value.style.setProperty('--panel-y', `${x * 3.2}deg`)
}

onMounted(() => {
    finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches
        && !window.matchMedia('(prefers-reduced-motion: reduce)').matches
    hero.value?.addEventListener('pointermove', moveHero, { passive: true })
    hero.value?.addEventListener('pointerleave', resetMotion)
})

onUnmounted(() => {
    hero.value?.removeEventListener('pointermove', moveHero)
    hero.value?.removeEventListener('pointerleave', resetMotion)
})
</script>

<template>
    <section ref="hero" class="home-hero">
        <HeroScene accent-color="#22D3EE" variant="home" />
        <div class="hero-glow" aria-hidden="true"></div>
        <div class="hero-word" aria-hidden="true">DEVSPRENDIMAI</div>
        <div class="hero-signals" aria-hidden="true"><i></i><i></i><i></i><i></i></div>

        <div class="site-container hero-layout">
            <div class="hero-copy">
                <div class="hero-badge"><i></i> {{ isEnglish ? 'Now accepting new projects' : 'Priimami nauji projektai' }}</div>

                <h1>
                    {{ copy.headline }}
                    <span>{{ copy.highlight }}</span>
                </h1>

                <p>{{ copy.description }}</p>

                <div class="hero-actions">
                    <a href="#contact" class="primary-action">{{ isEnglish ? 'Discuss a project' : 'Aptarkime projektą' }} <span>↗</span></a>
                    <a href="#projects" class="secondary-action">{{ isEnglish ? 'View our work' : 'Peržiūrėti darbus' }} <span>↓</span></a>
                </div>

                <div class="hero-proof">
                    <span><strong>01</strong> {{ isEnglish ? 'Clear strategy' : 'Aiški strategija' }}</span>
                    <span><strong>02</strong> {{ isEnglish ? 'Custom design' : 'Individualus dizainas' }}</span>
                    <span><strong>03</strong> {{ isEnglish ? 'Post-launch support' : 'Pagalba po starto' }}</span>
                </div>
            </div>

            <div class="hero-panel" aria-hidden="true">
                <div class="panel-top">
                    <div class="panel-dots"><i></i><i></i><i></i></div>
                    <span>devsprendimai.lt</span>
                    <b>{{ isEnglish ? 'Live' : 'Gyvai' }}</b>
                </div>
                <div class="panel-body">
                    <div class="panel-scan"></div>
                    <span class="panel-label">{{ isEnglish ? 'BUSINESS GROWTH SYSTEM' : 'VERSLO AUGIMO SISTEMA' }}</span>
                    <div class="panel-title">{{ isEnglish ? 'Strategy.' : 'Strategija.' }}<br><em>{{ isEnglish ? 'Design.' : 'Dizainas.' }}</em><br>{{ isEnglish ? 'Results.' : 'Rezultatas.' }}</div>
                    <div class="panel-lines"><i></i><i></i></div>
                    <div class="panel-button">{{ isEnglish ? 'Start a project' : 'Pradėti projektą' }} <span>↗</span></div>
                </div>
                <div class="floating-card card-one"><span>{{ isEnglish ? 'Enquiry' : 'Užklausa' }}</span><strong>{{ isEnglish ? '+1 new' : '+1 nauja' }}</strong></div>
                <div class="floating-card card-two"><span>{{ isEnglish ? 'Process' : 'Procesas' }}</span><strong>{{ isEnglish ? 'Automated ✓' : 'Automatizuotas ✓' }}</strong></div>
            </div>
        </div>

        <div class="hero-ticker" aria-hidden="true">
            <div class="hero-ticker-track">
                <span>{{ isEnglish ? 'WEBSITES ✦ E-COMMERCE ✦ APPLICATIONS ✦ CRM SYSTEMS ✦ AUTOMATION ✦ BUSINESS GROWTH ✦' : 'SVETAINĖS ✦ EL. PARDUOTUVĖS ✦ APLIKACIJOS ✦ CRM SISTEMOS ✦ AUTOMATIZACIJA ✦ VERSLO AUGIMAS ✦' }}</span>
                <span>{{ isEnglish ? 'WEBSITES ✦ E-COMMERCE ✦ APPLICATIONS ✦ CRM SYSTEMS ✦ AUTOMATION ✦ BUSINESS GROWTH ✦' : 'SVETAINĖS ✦ EL. PARDUOTUVĖS ✦ APLIKACIJOS ✦ CRM SISTEMOS ✦ AUTOMATIZACIJA ✦ VERSLO AUGIMAS ✦' }}</span>
            </div>
        </div>

    </section>
</template>

<style scoped>
.home-hero { position: relative; display: flex; align-items: center; min-height: 100svh; overflow: hidden; padding: 8.5rem 0 6.5rem; }
.hero-glow { position: absolute; z-index: 1; width: 52rem; height: 52rem; right: -19rem; top: 5rem; border-radius: 50%; background: radial-gradient(circle, rgba(34,211,238,.18), rgba(79,70,229,.09) 43%, transparent 70%); filter: blur(35px); transform: translate3d(var(--hero-x,0),var(--hero-y,0),0); transition: transform .55s cubic-bezier(.2,.7,.2,1); }
.hero-word { position: absolute; left: 50%; bottom: -1.5vw; color: rgba(255,255,255,.018); font-size: clamp(5rem, 15vw, 15rem); font-weight: 900; letter-spacing: -.08em; line-height: .8; white-space: nowrap; transform: translateX(-50%); user-select: none; }
.hero-signals { position: absolute; inset: 0; pointer-events: none; }
.hero-signals i { position: absolute; width: 5px; height: 5px; border-radius: 50%; background: #67e8f9; box-shadow: 0 0 18px #22d3ee; animation: signal-float 7s ease-in-out infinite; }
.hero-signals i:nth-child(1) { left: 7%; top: 28%; animation-delay: -1s; }
.hero-signals i:nth-child(2) { left: 48%; top: 19%; animation-delay: -3.4s; }
.hero-signals i:nth-child(3) { right: 7%; top: 58%; animation-delay: -5s; }
.hero-signals i:nth-child(4) { left: 38%; bottom: 13%; animation-delay: -2.1s; }
.hero-layout { position: relative; z-index: 2; display: grid; grid-template-columns: minmax(0,1.12fr) minmax(360px,.7fr); align-items: center; gap: clamp(3rem,6vw,6rem); }
.hero-copy { min-width: 0; animation: rise .8s cubic-bezier(.2,.7,.2,1) both; }
.hero-badge { display: inline-flex; align-items: center; gap: .65rem; padding: .55rem .85rem; border: 1px solid rgba(34,211,238,.18); border-radius: 999px; color: #9aeaf6; background: rgba(34,211,238,.07); font-size: .72rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
.hero-badge i { width: 7px; height: 7px; border-radius: 50%; background: #22d3ee; box-shadow: 0 0 14px #22d3ee; animation: pulse 2s ease-in-out infinite; }
.hero-copy h1 { max-width: 950px; margin-top: 1.6rem; overflow-wrap: anywhere; font-size: clamp(3.5rem,7.3vw,7.8rem); font-weight: 850; letter-spacing: -.07em; line-height: .88; }
.hero-copy h1 span { display: block; color: transparent; background: linear-gradient(100deg,#22d3ee,#60a5fa 38%,#818cf8 55%,#22d3ee 78%); background-size: 220% 100%; background-clip: text; -webkit-background-clip: text; animation: gradient-flow 7s ease-in-out infinite; }
.hero-copy > p { max-width: 690px; margin-top: 1.8rem; color: #94a3b8; font-size: clamp(1.05rem,1.45vw,1.22rem); line-height: 1.75; }
.hero-actions { display: flex; flex-wrap: wrap; gap: .8rem; margin-top: 2.2rem; }
.hero-actions a { display: inline-flex; align-items: center; justify-content: center; gap: .7rem; min-height: 3.6rem; padding: 0 1.55rem; border-radius: 999px; font-weight: 850; transition: transform .2s ease,border-color .2s ease,box-shadow .2s ease; }
.primary-action { color: #061016; background: #22d3ee; box-shadow: 0 14px 36px rgba(34,211,238,.2); }
.primary-action:hover { transform: translateY(-2px); box-shadow: 0 18px 44px rgba(34,211,238,.33); }
.secondary-action { border: 1px solid rgba(255,255,255,.12); color: #e2e8f0; background: rgba(255,255,255,.03); }
.secondary-action:hover { transform: translateY(-2px); border-color: rgba(34,211,238,.45); }
.hero-proof { display: flex; flex-wrap: wrap; gap: 1rem 1.6rem; margin-top: 2.4rem; color: #78869a; font-size: .78rem; font-weight: 700; }
.hero-proof span { display: flex; align-items: center; gap: .55rem; }
.hero-proof strong { color: #22d3ee; font-size: .64rem; letter-spacing: .1em; }
.hero-panel { position: relative; width: 100%; min-height: 510px; overflow: visible; border: 1px solid rgba(103,232,249,.18); border-radius: 1.8rem; background: linear-gradient(145deg,rgba(18,29,47,.84),rgba(8,13,23,.8)); box-shadow: 0 55px 130px rgba(0,0,0,.58),0 0 55px rgba(34,211,238,.08); transform: perspective(1100px) rotateY(calc(-7deg + var(--panel-y,0deg))) rotateX(calc(2deg + var(--panel-x,0deg))) translateZ(24px); transform-style:preserve-3d; transition: transform .25s ease-out; animation: panel-in 1s .15s cubic-bezier(.2,.7,.2,1) backwards; backdrop-filter:blur(12px); }
.hero-panel::after { content: ''; position: absolute; inset: 0; border-radius: inherit; background: linear-gradient(120deg,rgba(255,255,255,.06),transparent 28%); pointer-events: none; }
.panel-top { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; min-height: 3.6rem; padding: 0 1.2rem; border-bottom: 1px solid rgba(255,255,255,.07); color: #55657b; font-size: .65rem; }
.panel-top b { justify-self: end; color: #67e8f9; font-weight: 800; }
.panel-dots { display: flex; gap: .35rem; }
.panel-dots i { width: 7px; height: 7px; border-radius: 50%; background: #334155; }
.panel-body { position: relative; min-height: 450px; padding: 4rem 2.3rem 2rem; overflow: hidden; }
.panel-body::before { content: ''; position: absolute; inset: 0; opacity: .35; background-image: linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px); background-size: 30px 30px; mask-image: linear-gradient(black,transparent); }
.panel-scan { position: absolute; z-index: 1; left: 0; right: 0; top: -20%; height: 30%; opacity: .35; background: linear-gradient(transparent,rgba(34,211,238,.13),transparent); animation: panel-scan 5.5s ease-in-out infinite; }
.panel-label,.panel-title,.panel-lines,.panel-button { position: relative; z-index: 2; }
.panel-label { color: #67e8f9; font-size: .65rem; font-weight: 800; letter-spacing: .16em; }
.panel-title { margin-top: 1.2rem; font-size: clamp(2.4rem,4vw,4rem); font-weight: 850; letter-spacing: -.06em; line-height: .93; }
.panel-title em { color: #22d3ee; font-style: normal; }
.panel-lines { display: grid; gap: .5rem; margin-top: 1.7rem; }
.panel-lines i { width: 74%; height: 4px; border-radius: 9px; background: linear-gradient(90deg,#26344a,#22d3ee,#26344a); background-size: 220% 100%; animation: line-pulse 3.8s linear infinite; }
.panel-lines i:last-child { width: 52%; animation-delay: -.9s; }
.panel-button { display: flex; justify-content: space-between; width: 12rem; margin-top: 2rem; padding: .75rem 1rem; border-radius: 999px; color: #071018; background: #22d3ee; font-size: .72rem; font-weight: 850; }
.floating-card { position: absolute; z-index: 4; display: flex; flex-direction: column; gap: .2rem; min-width: 9rem; padding: .85rem 1rem; border: 1px solid rgba(255,255,255,.1); border-radius: .9rem; background: rgba(12,19,32,.92); box-shadow: 0 18px 45px rgba(0,0,0,.3); backdrop-filter: blur(15px); }
.floating-card span { color: #607087; font-size: .62rem; text-transform: uppercase; letter-spacing: .1em; }
.floating-card strong { color: #dce7f4; font-size: .8rem; }
.card-one { right: -2.5rem; top: 7rem; animation: float-card 4.8s ease-in-out infinite; }
.card-two { left: -3.2rem; bottom: 2.5rem; animation: float-card 5.4s -2.3s ease-in-out infinite reverse; }
.card-two strong { color: #67e8f9; }
.hero-ticker { position: absolute; z-index: 3; left: 0; right: 0; bottom: 0; overflow: hidden; border-block: 1px solid rgba(255,255,255,.06); background: rgba(7,12,21,.66); backdrop-filter: blur(12px); transform: rotate(-.7deg) scale(1.02); }
.hero-ticker-track { display: flex; width: max-content; padding: .8rem 0; animation: ticker 24s linear infinite; }
.hero-ticker-track span { flex: none; color: #526177; font-size: .66rem; font-weight: 800; letter-spacing: .2em; white-space: nowrap; }
.hero-ticker-track i { margin: 0 1.25rem; color: #22d3ee; font-style: normal; }
@keyframes rise { from { opacity: 0; transform: translateY(28px); } }
@keyframes panel-in { from { opacity: 0; transform: perspective(1100px) rotateY(-7deg) rotateX(2deg) translateY(35px); } }
@keyframes pulse { 50% { opacity: .45; box-shadow: 0 0 5px #22d3ee; } }
@keyframes gradient-flow { 50% { background-position: 100% 50%; } }
@keyframes panel-scan { 0%,15% { transform: translateY(-100%); } 65%,100% { transform: translateY(500%); } }
@keyframes line-pulse { to { background-position: -220% 0; } }
@keyframes float-card { 50% { transform: translateY(-9px); } }
@keyframes signal-float { 50% { transform: translate3d(8px,-18px,0) scale(1.5); opacity: .35; } }
@keyframes ticker { to { transform: translateX(-50%); } }

@media (max-width: 1100px) {
    .hero-layout { grid-template-columns: minmax(0,1fr); }
    .hero-panel { width: min(100%,650px); min-height: 470px; transform: none; }
    .card-one { right: -1rem; }
    .card-two { left: -1rem; }
}

@media (max-width: 767px) {
    .home-hero { min-height: auto; padding: 6.5rem 0 5.2rem; }
    .hero-layout { gap: 2.6rem; }
    .hero-badge { padding: .48rem .68rem; font-size: .62rem; letter-spacing: .06em; }
    .hero-copy h1 { margin-top: 1.3rem; font-size: clamp(2.45rem,12.5vw,4.7rem); line-height: .97; }
    .hero-copy > p { margin-top: 1.35rem; font-size: .98rem; line-height: 1.68; }
    .hero-actions { flex-direction: column; }
    .hero-actions a { width: 100%; min-height: 3.45rem; }
    .hero-proof { display: flex; flex-wrap: nowrap; gap: .55rem; margin: 1.55rem 0 0; padding: 0 0 .4rem; overflow-x: auto; scroll-snap-type: x proximity; scrollbar-width: none; }
    .hero-proof::-webkit-scrollbar { display: none; }
    .hero-proof span { flex: none; min-height: 2.25rem; padding: 0 .8rem; border: 1px solid rgba(255,255,255,.075); border-radius: 999px; background: rgba(255,255,255,.025); scroll-snap-align: start; }
    .hero-panel { min-height: 360px; border-radius: 1.3rem; transform: none; }
    .panel-top { min-height: 3.15rem; padding-inline: .9rem; }
    .panel-body { min-height: 305px; padding: 2.45rem 1.25rem 1.25rem; }
    .panel-title { font-size: clamp(2.55rem,12vw,3.4rem); }
    .panel-lines { margin-top: 1.25rem; }
    .panel-button { margin-top: 1.45rem; }
    .floating-card { display: none; }
    .hero-ticker { transform: none; }
    .hero-ticker-track { animation-duration: 18s; }
    .hero-signals i:nth-child(1),
    .hero-signals i:nth-child(4) { display: none; }
}

@media (prefers-reduced-motion: reduce) {
    .hero-copy,
    .hero-panel,
    .hero-badge i,
    .hero-copy h1 span,
    .hero-signals i,
    .panel-scan,
    .panel-lines i,
    .floating-card,
    .hero-ticker-track { animation: none; }
    .hero-panel { transform: none; }
}
</style>
