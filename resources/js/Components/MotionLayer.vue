<script setup>
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const progress = ref(0)
const aura = ref(null)
const ripples = ref([])

let observer
let frame
let progressFrame
let rippleId = 0
const cleanup = []
const pointer = { targetX: 0, targetY: 0, x: 0, y: 0 }

const supportsFinePointer = () => window.matchMedia('(hover: hover) and (pointer: fine)').matches
const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches

const updateProgress = () => {
    const max = document.documentElement.scrollHeight - window.innerHeight
    progress.value = max > 0 ? Math.min(window.scrollY / max, 1) : 0
}

const onPointerMove = (event) => {
    pointer.targetX = event.clientX
    pointer.targetY = event.clientY
    startAura()
}

const animateAura = () => {
    frame = 0
    pointer.x += (pointer.targetX - pointer.x) * .13
    pointer.y += (pointer.targetY - pointer.y) * .13

    if (aura.value) {
        aura.value.style.transform = `translate3d(${pointer.x - 210}px, ${pointer.y - 210}px, 0)`
    }

    const moving = Math.abs(pointer.targetX - pointer.x) > .2 || Math.abs(pointer.targetY - pointer.y) > .2
    if (moving && !document.hidden) frame = requestAnimationFrame(animateAura)
}

const startAura = () => {
    if (!frame && !document.hidden) frame = requestAnimationFrame(animateAura)
}

const requestProgressUpdate = () => {
    if (progressFrame) return
    progressFrame = requestAnimationFrame(() => {
        progressFrame = 0
        updateProgress()
    })
}

const handleVisibility = () => {
    if (document.hidden) {
        cancelAnimationFrame(frame)
        frame = 0
    } else {
        startAura()
    }
}

const onPointerDown = (event) => {
    if (!supportsFinePointer()) return

    const id = ++rippleId
    ripples.value.push({ id, x: event.clientX, y: event.clientY })
    window.setTimeout(() => {
        ripples.value = ripples.value.filter((ripple) => ripple.id !== id)
    }, 720)
}

const setupReveal = () => {
    if (reducedMotion()) return

    const selectors = [
        '.services-heading',
        '#projects .mb-14',
        '#projects .mb-20',
        '#about .mb-20',
        '#process .mb-20',
        '.section-heading',
        '.center-heading',
        '.feature-copy',
        '.contact-copy',
        '.contact-form',
        '.footer-intro',
        '.footer-column',
        '.service-card',
        '.project-slider',
        '.empty-projects',
        '.about-card',
        '.step-card',
        '.benefit-card',
        '.feature-item',
        '.offer-card',
        '.process-card',
    ]

    const elements = [...document.querySelectorAll(selectors.join(','))]
    elements.forEach((element, index) => {
        element.classList.add('motion-reveal')
        element.style.setProperty('--motion-delay', `${Math.min(index % 4, 3) * 70}ms`)
    })

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return
            entry.target.classList.add('is-revealed')
            observer.unobserve(entry.target)
        })
    }, { threshold: .1, rootMargin: '0px 0px -7% 0px' })

    elements.forEach((element) => observer.observe(element))
}

const setupTilt = () => {
    if (!supportsFinePointer() || reducedMotion()) return

    document.querySelectorAll('.service-card, .benefit-card, .offer-card, .about-card').forEach((card) => {
        const move = (event) => {
            const rect = card.getBoundingClientRect()
            const x = (event.clientX - rect.left) / rect.width - .5
            const y = (event.clientY - rect.top) / rect.height - .5
            card.style.transform = `perspective(900px) rotateX(${y * -3.5}deg) rotateY(${x * 4.5}deg) translateY(-6px)`
            card.style.setProperty('--spot-x', `${(x + .5) * 100}%`)
            card.style.setProperty('--spot-y', `${(y + .5) * 100}%`)
        }
        const leave = () => {
            card.style.removeProperty('transform')
            card.style.removeProperty('--spot-x')
            card.style.removeProperty('--spot-y')
        }

        card.classList.add('motion-tilt')
        card.addEventListener('pointermove', move)
        card.addEventListener('pointerleave', leave)
        cleanup.push(() => {
            card.removeEventListener('pointermove', move)
            card.removeEventListener('pointerleave', leave)
        })
    })

    document.querySelectorAll('.primary-action, .button-primary, .header-cta, .contact-form button').forEach((button) => {
        const move = (event) => {
            const rect = button.getBoundingClientRect()
            const x = event.clientX - rect.left - rect.width / 2
            const y = event.clientY - rect.top - rect.height / 2
            button.style.transform = `translate3d(${x * .08}px, ${y * .11}px, 0)`
        }
        const leave = () => button.style.removeProperty('transform')

        button.addEventListener('pointermove', move)
        button.addEventListener('pointerleave', leave)
        cleanup.push(() => {
            button.removeEventListener('pointermove', move)
            button.removeEventListener('pointerleave', leave)
        })
    })
}

const resetPageMotion = () => {
    observer?.disconnect()
    observer = undefined
    cleanup.splice(0).forEach((dispose) => dispose())
}

const initializePageMotion = async () => {
    resetPageMotion()
    await nextTick()
    updateProgress()
    setupReveal()
    setupTilt()
}

onMounted(async () => {
    await nextTick()
    updateProgress()
    window.addEventListener('scroll', requestProgressUpdate, { passive: true })
    window.addEventListener('resize', requestProgressUpdate, { passive: true })
    document.addEventListener('visibilitychange', handleVisibility)

    await initializePageMotion()

    if (supportsFinePointer() && !reducedMotion()) {
        pointer.x = pointer.targetX = window.innerWidth * .7
        pointer.y = pointer.targetY = window.innerHeight * .35
        window.addEventListener('pointermove', onPointerMove, { passive: true })
        window.addEventListener('pointerdown', onPointerDown, { passive: true })
        startAura()
    }
})

watch(() => page.url, () => initializePageMotion())

onUnmounted(() => {
    resetPageMotion()
    cancelAnimationFrame(frame)
    cancelAnimationFrame(progressFrame)
    window.removeEventListener('scroll', requestProgressUpdate)
    window.removeEventListener('resize', requestProgressUpdate)
    document.removeEventListener('visibilitychange', handleVisibility)
    window.removeEventListener('pointermove', onPointerMove)
    window.removeEventListener('pointerdown', onPointerDown)
})
</script>

<template>
    <div class="motion-layer" aria-hidden="true">
        <div class="scroll-progress"><i :style="{ transform: `scaleX(${progress})` }"></i></div>
        <div ref="aura" class="pointer-aura"></div>
        <span
            v-for="ripple in ripples"
            :key="ripple.id"
            class="pointer-ripple"
            :style="{ left: `${ripple.x}px`, top: `${ripple.y}px` }"
        ></span>
    </div>
</template>

<style scoped>
.motion-layer { position: fixed; inset: 0; z-index: 60; overflow: hidden; pointer-events: none; }
.scroll-progress { position: absolute; inset: 0 0 auto; height: 2px; background: rgba(255,255,255,.025); }
.scroll-progress i { display: block; width: 100%; height: 100%; background: linear-gradient(90deg,#22d3ee,#60a5fa,#818cf8); box-shadow: 0 0 16px rgba(34,211,238,.75); transform-origin: left; will-change: transform; }
.pointer-aura { position: absolute; width: 420px; height: 420px; border-radius: 50%; opacity: .13; background: radial-gradient(circle,rgba(34,211,238,.38),rgba(96,165,250,.12) 35%,transparent 70%); filter: blur(22px); will-change: transform; }
.pointer-ripple { position: absolute; width: 18px; height: 18px; border: 1px solid rgba(103,232,249,.8); border-radius: 50%; transform: translate(-50%,-50%); animation: ripple .72s ease-out forwards; }
@keyframes ripple { to { width: 90px; height: 90px; opacity: 0; } }

@media (hover: none), (pointer: coarse), (prefers-reduced-motion: reduce) {
    .pointer-aura,
    .pointer-ripple { display: none; }
}
</style>
