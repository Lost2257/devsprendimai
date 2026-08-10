<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import MotionLayer from '@/Components/MotionLayer.vue'
import MobileActionBar from '@/Components/MobileActionBar.vue'
import { useLocale } from '@/composables/useLocale'

defineProps({ immersive: { type: Boolean, default: false } })
const { isEnglish } = useLocale()
</script>

<template>
    <div class="site-shell" :class="{ 'site-shell-immersive': immersive }">
        <a href="#main-content" class="skip-link">{{ isEnglish ? 'Skip to content' : 'Pereiti prie turinio' }}</a>
        <div class="site-grid" aria-hidden="true"></div>
        <div class="site-noise" aria-hidden="true"></div>
        <MotionLayer />
        <Header />
        <MobileActionBar v-if="!immersive" />
        <main id="main-content">
            <slot />
        </main>
        <Footer v-if="!immersive" />
    </div>
</template>

<style scoped>
.site-shell-immersive { height: 100svh; overflow: hidden; }
.site-shell-immersive main { height: 100%; overflow: hidden; }
</style>
