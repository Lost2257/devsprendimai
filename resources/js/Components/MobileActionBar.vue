<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'

const page = usePage()
const { isEnglish } = useLocale()
const pastHero = ref(false)
const contactVisible = ref(false)
let observer
const blockedSections = new Set()

const currentPath = computed(() => page.url.split('?')[0])
const contactHref = computed(() => currentPath.value === '/' ? '#contact' : `${currentPath.value}#contact`)
const visible = computed(() => pastHero.value && !contactVisible.value)

const onScroll = () => {
    pastHero.value = window.scrollY > Math.min(window.innerHeight * .55, 460)
}

const setupBlockedSections = async () => {
    observer?.disconnect()
    blockedSections.clear()
    contactVisible.value = false
    await nextTick()

    const sections = [document.querySelector('#contact'), document.querySelector('.site-footer')].filter(Boolean)
    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) blockedSections.add(entry.target)
            else blockedSections.delete(entry.target)
        })
        contactVisible.value = blockedSections.size > 0
    }, { threshold: .04 })
    sections.forEach((section) => observer.observe(section))
}

onMounted(() => {
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })

    setupBlockedSections()
})

watch(() => page.url, () => setupBlockedSections())

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll)
    observer?.disconnect()
})
</script>

<template>
    <Transition name="action-bar">
        <div v-if="visible" class="mobile-action-bar">
            <a href="tel:+37060606227" class="call-action" :aria-label="isEnglish ? 'Call DevSprendimai' : 'Skambinti DevSprendimai'">
                <span aria-hidden="true">●</span> {{ isEnglish ? 'Call' : 'Skambinti' }}
            </a>
            <a :href="contactHref" class="project-action">
                {{ isEnglish ? 'Discuss a project' : 'Aptarti projektą' }} <span aria-hidden="true">↗</span>
            </a>
        </div>
    </Transition>
</template>

<style scoped>
.mobile-action-bar { position: fixed; z-index: 45; left: .75rem; right: .75rem; bottom: max(.75rem, env(safe-area-inset-bottom)); display: none; grid-template-columns: .72fr 1.28fr; gap: .45rem; padding: .42rem; border: 1px solid rgba(255,255,255,.11); border-radius: 1.1rem; background: rgba(8,13,23,.9); box-shadow: 0 18px 55px rgba(0,0,0,.46); backdrop-filter: blur(20px); }
.mobile-action-bar a { display: flex; align-items: center; justify-content: center; min-height: 3.25rem; border-radius: .78rem; font-size: .78rem; font-weight: 850; }
.call-action { gap: .45rem; color: #c6d1de; background: rgba(255,255,255,.055); }
.call-action span { color: #22d3ee; font-size: .5rem; box-shadow: 0 0 10px #22d3ee; }
.project-action { gap: .55rem; color: #061016; background: #22d3ee; box-shadow: 0 8px 25px rgba(34,211,238,.18); }
.action-bar-enter-active,
.action-bar-leave-active { transition: opacity .28s ease, transform .28s cubic-bezier(.2,.7,.2,1); }
.action-bar-enter-from,
.action-bar-leave-to { opacity: 0; transform: translateY(120%); }

@media (max-width: 767px) {
    .mobile-action-bar { display: grid; }
}

@media (prefers-reduced-motion: reduce) {
    .action-bar-enter-active,
    .action-bar-leave-active { transition: none; }
}
</style>
