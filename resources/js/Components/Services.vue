<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'

const { isEnglish, localize } = useLocale()
const services = computed(() => (isEnglish.value ? [
    { number: '01', title: 'Websites', text: 'Fast, distinctive and conversion-ready business websites.', href: '/interneto-svetaines', meta: 'Design · Development · SEO' },
    { number: '02', title: 'E-commerce', text: 'A smooth buying experience and efficient order management.', href: '/el-parduotuves', meta: 'WooCommerce · Integrations' },
    { number: '03', title: 'CRM systems', text: 'Customers, sales and team workflows in one place.', href: '/crm-sistemos', meta: 'Processes · Reports · API' },
    { number: '04', title: 'Application development', text: 'Web and mobile applications from MVP to a full platform.', href: '/aplikaciju-kurimas', meta: 'Web · PWA · Products' },
    { number: '05', title: 'Automation', text: 'Less repetitive work and manual data entry.', href: '/automatizacija', meta: 'Workflows · API · Data' },
    { number: '06', title: 'Website maintenance', text: 'Updates, security and support when you need it.', href: '/svetainiu-prieziura', meta: 'Monitoring · Support' },
] : [
    { number: '01', title: 'Interneto svetainės', text: 'Greitos, išskirtinės ir pardavimams paruoštos svetainės.', href: '/interneto-svetaines', meta: 'Dizainas · Kūrimas · SEO' },
    { number: '02', title: 'El. parduotuvės', text: 'Patogi pirkimo patirtis ir sklandus užsakymų valdymas.', href: '/el-parduotuves', meta: 'WooCommerce · Integracijos' },
    { number: '03', title: 'CRM sistemos', text: 'Klientai, pardavimai ir komandos darbai vienoje vietoje.', href: '/crm-sistemos', meta: 'Procesai · Ataskaitos · API' },
    { number: '04', title: 'Aplikacijų kūrimas', text: 'Web ir mobiliosios aplikacijos nuo MVP iki pilnos platformos.', href: '/aplikaciju-kurimas', meta: 'Web · PWA · Produktai' },
    { number: '05', title: 'Automatizacija', text: 'Mažiau pasikartojančių užduočių ir rankinio duomenų darbo.', href: '/automatizacija', meta: 'Srautai · API · Duomenys' },
    { number: '06', title: 'Svetainių priežiūra', text: 'Atnaujinimai, saugumas ir pagalba, kai jos reikia.', href: '/svetainiu-prieziura', meta: 'Stebėjimas · Pagalba' },
]).map((service) => ({ ...service, href: localize(service.href) })))
</script>

<template>
    <section id="services" class="services-section">
        <div class="site-container">
            <div class="services-heading">
                <div>
                    <span>01 · {{ isEnglish ? 'Services' : 'Paslaugos' }}</span>
                    <h2>{{ isEnglish ? 'One partner.' : 'Vienas partneris.' }}<br><em>{{ isEnglish ? 'The entire digital journey.' : 'Visas skaitmeninis kelias.' }}</em></h2>
                </div>
                <p>{{ isEnglish ? 'From the first idea to a working product and ongoing support. Clear, responsible and focused on business outcomes.' : 'Nuo pirmos idėjos iki veikiančio sprendimo ir jo priežiūros. Aiškiai, atsakingai ir su dėmesiu verslo rezultatui.' }}</p>
            </div>

            <div class="mobile-swipe-hint"><span>{{ isEnglish ? 'Swipe' : 'Perbraukite' }}</span><i>→</i></div>

            <div class="services-grid mobile-snap">
                <Link v-for="service in services" :key="service.href" :href="service.href" :prefetch="['hover', 'click']" cache-for="30s" view-transition class="service-card">
                    <div class="card-top"><span>{{ service.number }}</span><i>↗</i></div>
                    <div>
                        <h3>{{ service.title }}</h3>
                        <p>{{ service.text }}</p>
                    </div>
                    <small>{{ service.meta }}</small>
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
.services-section { position: relative; padding: clamp(5rem,9vw,8.5rem) 0; }
.services-heading { display: grid; grid-template-columns: minmax(0,1.25fr) minmax(300px,.6fr); align-items: end; gap: 3rem; margin-bottom: 3.5rem; }
.services-heading span { color: #67e8f9; font-size: .74rem; font-weight: 800; letter-spacing: .2em; text-transform: uppercase; }
.services-heading h2 { margin-top: 1.1rem; font-size: clamp(2.5rem,5vw,5.2rem); font-weight: 850; letter-spacing: -.06em; line-height: .98; }
.services-heading h2 em { color: #67e8f9; font-style: normal; }
.services-heading > p { color: #8997ac; line-height: 1.75; }
.services-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; }
.mobile-swipe-hint { display: none; }
.service-card { display: flex; flex-direction: column; min-height: 340px; padding: 1.7rem; overflow: hidden; border: 1px solid rgba(255,255,255,.08); border-radius: 1.5rem; background: linear-gradient(145deg,rgba(255,255,255,.032),rgba(255,255,255,.015)); transition: transform .25s ease,border-color .25s ease,background .25s ease,box-shadow .25s ease; }
.service-card:hover { transform: translateY(-7px); border-color: rgba(34,211,238,.3); background: rgba(34,211,238,.035); box-shadow: 0 25px 70px rgba(0,0,0,.2); }
.card-top { display: flex; align-items: center; justify-content: space-between; color: #526177; font-size: .7rem; font-weight: 800; letter-spacing: .14em; }
.card-top i { display: grid; place-items: center; width: 2.3rem; height: 2.3rem; border: 1px solid rgba(255,255,255,.1); border-radius: 50%; color: #67e8f9; font-style: normal; transition: color .2s ease,background .2s ease,transform .2s ease; }
.service-card:hover .card-top i { color: #071018; background: #22d3ee; transform: rotate(8deg); }
.service-card > div:nth-child(2) { margin-top: auto; }
.service-card h3 { font-size: clamp(1.4rem,2vw,1.8rem); font-weight: 800; letter-spacing: -.035em; }
.service-card p { margin-top: .8rem; color: #8795aa; line-height: 1.65; }
.service-card small { margin-top: 2rem; color: #55647a; font-size: .67rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; }

@media (max-width: 1023px) {
    .services-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 767px) {
    .services-section { padding-block: 4.5rem; }
    .services-heading { grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1.75rem; }
    .services-heading h2 { font-size: clamp(2.45rem,12vw,3.6rem); line-height: .96; }
    .services-heading > p { font-size: .95rem; }
    .mobile-swipe-hint { display: flex; align-items: center; justify-content: space-between; margin-bottom: .8rem; color: #5f6e83; font-size: .66rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
    .mobile-swipe-hint i { color: #67e8f9; font-size: 1rem; font-style: normal; animation: swipe-hint 1.8s ease-in-out infinite; }
    .services-grid { display: flex; gap: .75rem; margin-inline: 0; padding: .25rem 0 1.2rem; overflow-x: auto; }
    .service-card { flex: 0 0 min(82vw,340px); min-height: 300px; padding: 1.45rem; scroll-snap-align: center; }
    .service-card:first-child { scroll-snap-align: start; }
    .service-card h3 { font-size: 1.55rem; }
    .service-card p { font-size: .94rem; }
}

@keyframes swipe-hint { 50% { transform: translateX(7px); } }

@media (prefers-reduced-motion: reduce) {
    .mobile-swipe-hint i { animation: none; }
}
</style>
