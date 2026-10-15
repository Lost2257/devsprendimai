<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { openConsentSettings } from '@/lib/analytics'
import { useLocale } from '@/composables/useLocale'

const year = new Date().getFullYear()
const page = usePage()

const { isEnglish, localize } = useLocale()
const services = computed(() => (isEnglish.value ? [
    ['Websites', '/interneto-svetaines'], ['E-commerce', '/el-parduotuves'], ['CRM systems', '/crm-sistemos'],
    ['Application development', '/aplikaciju-kurimas'], ['Automation', '/automatizacija'], ['Website maintenance', '/svetainiu-prieziura'],
] : [
    ['Interneto svetainės', '/interneto-svetaines'],
    ['El. parduotuvės', '/el-parduotuves'],
    ['CRM sistemos', '/crm-sistemos'],
    ['Aplikacijų kūrimas', '/aplikaciju-kurimas'],
    ['Automatizacija', '/automatizacija'],
    ['Svetainių priežiūra', '/svetainiu-prieziura'],
]).map(([label, path]) => [label, localize(path)]))
const legalLinks = computed(() => (isEnglish.value ? [
    ['Privacy Policy', '/privatumo-politika'],
    ['Cookie Policy', '/slapuku-politika'],
    ['Terms of Service', '/paslaugu-teikimo-salygos'],
] : [
    ['Privatumo politika', '/privatumo-politika'],
    ['Slapukų politika', '/slapuku-politika'],
    ['Paslaugų teikimo sąlygos', '/paslaugu-teikimo-salygos'],
]).map(([label, path]) => [label, localize(path)]))
const contactHref = computed(() => `${localize('/')}#contact`)
</script>

<template>
    <footer class="site-footer">
        <div class="site-container">
            <div class="footer-top">
                <div class="footer-intro">
                    <Link :href="localize('/')" :prefetch="['hover', 'click']" cache-for="30s" view-transition :aria-label="isEnglish ? 'DevSprendimai home' : 'DevSprendimai pradžia'"><img src="/images/logo.png" alt="DevSprendimai"></Link>
                    <p>{{ isEnglish ? 'Digital solutions that help businesses look professional, work efficiently and grow.' : 'Kuriu skaitmeninius sprendimus, kurie padeda verslui atrodyti profesionaliai, dirbti efektyviau ir augti.' }}</p>
                </div>

                <div class="footer-column">
                    <span>{{ isEnglish ? 'Services' : 'Paslaugos' }}</span>
                    <Link v-for="service in services" :key="service[1]" :href="service[1]" :prefetch="['hover', 'click']" cache-for="30s" view-transition>{{ service[0] }}</Link>
                </div>

                <div class="footer-column">
                    <span>{{ isEnglish ? 'Contact' : 'Susisiekime' }}</span>
                    <a href="mailto:info@devsprendimai.lt">info@devsprendimai.lt</a>
                    <a href="tel:+37060606227">+370 606 06227</a>
                    <a :href="contactHref">{{ isEnglish ? 'Project enquiry' : 'Projekto užklausa' }} ↗</a>
                </div>

                <div class="footer-column">
                    <span>{{ isEnglish ? 'Legal' : 'Teisinė informacija' }}</span>
                    <Link v-for="item in legalLinks" :key="item[1]" :href="item[1]" :prefetch="['hover', 'click']" cache-for="30s" view-transition>{{ item[0] }}</Link>
                    <button v-if="page.props.analytics?.measurementId" type="button" class="cookie-settings" @click="openConsentSettings">{{ isEnglish ? 'Cookie settings' : 'Slapukų nustatymai' }}</button>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    <p>© {{ year }} DevSprendimai. {{ isEnglish ? 'All rights reserved.' : 'Visos teisės saugomos.' }}</p>
                    <small>Aivaras Razvodovskis · {{ isEnglish ? 'Individual activity certificate' : 'Individualios veiklos pažyma' }} Nr. 1400067 · Pramonės pr. 28, Kaunas</small>
                </div>
                <p>{{ isEnglish ? 'Strategy · Design · Development' : 'Strategija · Dizainas · Programavimas' }}</p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.site-footer { position: relative; z-index: 2; padding: 5.5rem 0 2rem; border-top: 1px solid rgba(255,255,255,.07); background: rgba(6,10,18,.55); }
.footer-top { display: grid; grid-template-columns: minmax(280px, 1.45fr) repeat(3,1fr); gap: clamp(2rem, 5vw, 5rem); padding-bottom: 4.5rem; }
.footer-intro img { width: auto; height: 2.7rem; }
.footer-intro p { max-width: 440px; margin-top: 1.5rem; color: #7e8da3; line-height: 1.75; }
.footer-column { display: flex; flex-direction: column; align-items: flex-start; gap: .85rem; }
.footer-column > span { margin-bottom: .45rem; color: #55647a; font-size: .7rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
.footer-column a { color: #aeb9c9; transition: color .2s ease, transform .2s ease; }
.footer-column a:hover { color: #67e8f9; transform: translateX(3px); }
.cookie-settings { padding:.5rem 0; min-height:44px; color:#aeb9c9; text-align:left; }
.cookie-settings:hover { color:#67e8f9; }
.cookie-settings:focus-visible { outline:2px solid #67e8f9; outline-offset:4px; }
.footer-bottom { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,.07); color: #56657a; font-size: .75rem; }
.footer-bottom > div { display:grid; gap:.35rem; }
.footer-bottom small { color:#46556b; font-size:.66rem; line-height:1.55; }

@media (max-width: 1023px) {
    .footer-top { grid-template-columns: 1.2fr 1fr 1fr; }
    .footer-column:last-child { grid-column: 2 / -1; }
}

@media (max-width: 767px) {
    .site-footer { padding: 4rem 0 calc(1.5rem + env(safe-area-inset-bottom)); }
    .footer-top { grid-template-columns: 1fr; gap: 2.7rem; }
    .footer-column:last-child { grid-column: auto; }
    .footer-bottom { flex-direction: column; align-items: flex-start; }
}
</style>
