<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'

const page = usePage()
const scrolled = ref(false)
const mobileMenu = ref(false)
const { isEnglish, localize, languageUrl } = useLocale()

const links = computed(() => (isEnglish.value ? [
    ['Websites', '/interneto-svetaines'], ['E-commerce', '/el-parduotuves'], ['CRM', '/crm-sistemos'],
    ['Applications', '/aplikaciju-kurimas'], ['Automation', '/automatizacija'], ['Maintenance', '/svetainiu-prieziura'],
] : [
    ['Svetainės', '/interneto-svetaines'], ['El. parduotuvės', '/el-parduotuves'], ['CRM', '/crm-sistemos'],
    ['Aplikacijos', '/aplikaciju-kurimas'], ['Automatizacija', '/automatizacija'], ['Priežiūra', '/svetainiu-prieziura'],
]).map(([label, path]) => ({ label, href: localize(path) })))

const currentPath = computed(() => page.url.split('?')[0])
const contactHref = computed(() => currentPath.value === '/' ? '#contact' : `${currentPath.value}#contact`)
const isActive = (href) => currentPath.value === href
const closeMenu = () => { mobileMenu.value = false }
const onScroll = () => { scrolled.value = window.scrollY > 24 }
const onKeydown = (event) => {
    if (event.key === 'Escape') closeMenu()
}

watch(mobileMenu, (isOpen) => {
    document.documentElement.style.overflow = isOpen ? 'hidden' : ''
})

onMounted(() => {
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })
    window.addEventListener('keydown', onKeydown)
})

onUnmounted(() => {
    document.documentElement.style.overflow = ''
    window.removeEventListener('scroll', onScroll)
    window.removeEventListener('keydown', onKeydown)
})
</script>

<template>
    <header class="site-header" :class="{ scrolled }">
        <div class="header-wrap">
            <div class="header-bar">
                <Link :href="localize('/')" prefetch="hover" cache-for="30s" view-transition class="brand" :aria-label="isEnglish ? 'DevSprendimai home page' : 'DevSprendimai pradinis puslapis'" @click="closeMenu">
                    <img src="/images/logo.png" alt="DevSprendimai">
                </Link>

                <nav class="desktop-nav" :aria-label="isEnglish ? 'Main navigation' : 'Pagrindinė navigacija'">
                    <Link
                        v-for="link in links"
                        :key="link.href"
                        :href="link.href"
                        prefetch="hover"
                        cache-for="30s"
                        view-transition
                        :class="{ active: isActive(link.href) }"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <Link :href="languageUrl" class="language-switch" :aria-label="isEnglish ? 'Lietuvių kalba' : 'English language'">{{ isEnglish ? 'LT' : 'EN' }}</Link>
                <a :href="contactHref" class="header-cta">{{ isEnglish ? 'Discuss a project' : 'Aptarkime projektą' }} <span>↗</span></a>

                <button
                    class="menu-toggle"
                    type="button"
                    :aria-expanded="mobileMenu"
                    aria-controls="mobile-navigation"
                    :aria-label="mobileMenu ? (isEnglish ? 'Close menu' : 'Uždaryti meniu') : (isEnglish ? 'Open menu' : 'Atidaryti meniu')"
                    @click="mobileMenu = !mobileMenu"
                >
                    <span></span><span></span>
                </button>
            </div>
        </div>

        <Transition name="menu">
            <div v-if="mobileMenu" id="mobile-navigation" class="mobile-panel">
                <nav :aria-label="isEnglish ? 'Mobile navigation' : 'Mobilioji navigacija'">
                    <Link
                        v-for="(link, index) in links"
                        :key="link.href"
                        :href="link.href"
                        prefetch="click"
                        cache-for="30s"
                        view-transition
                        :class="{ active: isActive(link.href) }"
                        @click="closeMenu"
                    >
                        <span>0{{ index + 1 }}</span>{{ link.label }}<i>↗</i>
                    </Link>
                </nav>
                <div class="mobile-tools">
                    <Link :href="languageUrl" class="mobile-language" @click="closeMenu">{{ isEnglish ? 'LT · Lietuvių' : 'EN · English' }}</Link>
                    <a :href="contactHref" class="mobile-cta" @click="closeMenu">{{ isEnglish ? 'Discuss your project' : 'Aptarkime jūsų projektą' }} <span>→</span></a>
                </div>
                <div class="mobile-contact">
                    <a href="mailto:info@devsprendimai.lt">info@devsprendimai.lt</a>
                    <a href="tel:+37060606227">+370 606 06227</a>
                </div>
            </div>
        </Transition>
    </header>
</template>

<style scoped>
.site-header { position: fixed; inset: 0 0 auto; z-index: 50; pointer-events: none; }
.header-wrap { width: min(100% - 2rem, 1320px); margin: 0 auto; padding-top: 1.15rem; transition: padding .25s ease; }
.scrolled .header-wrap { padding-top: .65rem; }
.header-bar { display: grid; grid-template-columns: auto 1fr auto auto; align-items: center; min-height: 4.7rem; padding: 0 .75rem 0 1.1rem; border: 1px solid rgba(255,255,255,.09); border-radius: 1.25rem; background: rgba(10,15,26,.72); box-shadow: 0 10px 50px rgba(0,0,0,.12); backdrop-filter: blur(20px); pointer-events: auto; transition: min-height .25s ease, border-color .25s ease, background .25s ease; }
.scrolled .header-bar { min-height: 4.1rem; border-color: rgba(34,211,238,.18); background: rgba(10,15,26,.9); box-shadow: 0 18px 55px rgba(0,0,0,.3); }
.brand { display: flex; align-items: center; }
.brand img { width: auto; height: 2.35rem; object-fit: contain; }
.desktop-nav { display: flex; align-items: center; justify-content: center; gap: clamp(.9rem, 1.5vw, 1.7rem); padding-inline: 1.5rem; }
.desktop-nav a { position: relative; padding: .8rem 0; color: #a8b3c4; font-size: .78rem; font-weight: 700; white-space: nowrap; transition: color .2s ease; }
.desktop-nav a::after { content: ''; position: absolute; left: 0; right: 100%; bottom: .35rem; height: 1px; background: #22d3ee; transition: right .2s ease; }
.desktop-nav a:hover,
.desktop-nav a.active { color: #f8fafc; }
.desktop-nav a:hover::after,
.desktop-nav a.active::after { right: 0; }
.header-cta { display: flex; align-items: center; gap: .6rem; min-height: 3.1rem; padding: 0 1.15rem; border-radius: 999px; color: #061016; background: #22d3ee; font-size: .78rem; font-weight: 850; white-space: nowrap; transition: transform .2s ease, box-shadow .2s ease; }
.header-cta:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(34,211,238,.28); }
.language-switch { display:grid; width:2.65rem; height:2.65rem; margin-right:.55rem; place-items:center; border:1px solid rgba(255,255,255,.12); border-radius:50%; color:#b8c5d5; font-size:.68rem; font-weight:900; letter-spacing:.08em; transition:.2s ease; }
.language-switch:hover { border-color:rgba(34,211,238,.5); color:#67e8f9; }
.menu-toggle { display: none; position: relative; width: 2.9rem; height: 2.9rem; border: 1px solid rgba(255,255,255,.1); border-radius: 50%; }
.menu-toggle span { position: absolute; left: .8rem; right: .8rem; height: 1px; background: white; transition: transform .2s ease, top .2s ease; }
.menu-toggle span:first-child { top: 1.15rem; }
.menu-toggle span:last-child { top: 1.65rem; }
.menu-toggle[aria-expanded="true"] span:first-child { top: 1.42rem; transform: rotate(45deg); }
.menu-toggle[aria-expanded="true"] span:last-child { top: 1.42rem; transform: rotate(-45deg); }
.mobile-panel { position: fixed; inset: 0; z-index: -1; display: flex; flex-direction: column; overflow-y: auto; padding: 7.3rem 1.5rem 2rem; background: rgba(7,11,19,.98); pointer-events: auto; }
.mobile-panel nav { display: grid; border-top: 1px solid rgba(255,255,255,.09); }
.mobile-panel nav a { display: grid; grid-template-columns: 2rem 1fr auto; align-items: center; min-height: 4.5rem; border-bottom: 1px solid rgba(255,255,255,.09); color: #dbe4f0; font-size: 1.35rem; font-weight: 800; }
.mobile-panel nav a > span { color: #526078; font-size: .62rem; letter-spacing: .1em; }
.mobile-panel nav a > i { color: #22d3ee; font-size: 1rem; font-style: normal; }
.mobile-panel nav a.active { color: #67e8f9; }
.mobile-cta { display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; padding: 1.15rem 1.25rem; border-radius: 1rem; color: #071018; background: #22d3ee; font-weight: 850; }
.mobile-tools { display:grid; gap:.75rem; margin-top:1.5rem; }
.mobile-tools .mobile-cta { margin-top:0; }
.mobile-language { display:flex; align-items:center; justify-content:center; min-height:3rem; border:1px solid rgba(34,211,238,.22); border-radius:1rem; color:#9aeaf6; font-size:.75rem; font-weight:850; letter-spacing:.08em; }
.mobile-contact { display: flex; flex-wrap: wrap; gap: .75rem 1.25rem; margin-top: auto; padding-top: 2rem; color: #8290a5; font-size: .85rem; }
.menu-enter-active,
.menu-leave-active { transition: opacity .25s ease, transform .25s ease; }
.menu-enter-from,
.menu-leave-to { opacity: 0; transform: translateY(-10px); }

@media (max-width: 1120px) {
    .desktop-nav,
    .header-cta,
    .language-switch { display: none; }
    .header-bar { grid-template-columns: auto 1fr auto; }
    .menu-toggle { display: block; grid-column: 3; pointer-events: auto; }
}

@media (max-width: 640px) {
    .header-wrap { width: calc(100% - 1.5rem); padding-top: .6rem; }
    .scrolled .header-wrap { padding-top: .35rem; }
    .header-bar { min-height: 4rem; padding-left: .8rem; border-radius: 1rem; }
    .brand img { height: 2rem; }
    .mobile-panel { padding: 5.9rem 1.125rem calc(1.25rem + env(safe-area-inset-bottom)); }
    .mobile-panel nav a { min-height: 4.25rem; font-size: 1.15rem; }
    .mobile-panel nav a:nth-child(1) { animation-delay: .03s; }
    .mobile-panel nav a:nth-child(2) { animation-delay: .06s; }
    .mobile-panel nav a:nth-child(3) { animation-delay: .09s; }
    .mobile-panel nav a:nth-child(4) { animation-delay: .12s; }
    .mobile-panel nav a:nth-child(5) { animation-delay: .15s; }
    .mobile-panel nav a:nth-child(6) { animation-delay: .18s; }
    .mobile-panel nav a { animation: mobile-link-in .38s cubic-bezier(.2,.7,.2,1) both; }
    .mobile-cta { min-height: 3.75rem; }
}

@media (max-height: 720px) and (max-width: 640px) {
    .mobile-panel nav a { min-height: 3.65rem; font-size: 1.05rem; }
    .mobile-contact { display: none; }
}

@keyframes mobile-link-in {
    from { opacity: 0; transform: translateX(-16px); }
}

@media (prefers-reduced-motion: reduce) {
    .mobile-panel nav a { animation: none; }
}
</style>
