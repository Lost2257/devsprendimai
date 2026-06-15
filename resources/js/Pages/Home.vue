<script setup>

import { ref, onMounted } from 'vue'

import PageLoader from '@/Components/PageLoader.vue'

const loading = ref(true)

onMounted(() => {
    setTimeout(() => {
        loading.value = false
    }, 2000)
})

import Header from '@/Components/Header.vue'
import Hero from '@/Components/Hero.vue'
import Services from '@/Components/Services.vue'
import Projects from '@/Components/Projects.vue'
import About from '@/Components/About.vue'
import Contact from '@/Components/Contact.vue'
import Footer from '@/Components/Footer.vue'
import MouseGlow from '@/Components/MouseGlow.vue'
import CursorGlow from '@/Components/CursorGlow.vue'
import Process from '@/Components/Process.vue'
</script>

<template>
    <PageLoader :loading="loading" />
    <div class="grid-bg"></div>

    <div class="min-h-screen bg-[#0B0F19] text-white">
        <!-- <MouseGlow /> -->
        <div class="noise"></div>
        <!-- <CursorGlow /> -->
        <Header />

        <div class="pt-24">
            <Hero />
            <Services />
            <Projects />
            <About />
            <Process />
            <Contact />
            <Footer />
        </div>
    </div>
</template>

<style>
.noise {
    position: fixed;
    inset: 0;

    background-image:
        radial-gradient(
            rgba(255,255,255,.03) 1px,
            transparent 1px
        );

    background-size: 4px 4px;

    pointer-events: none;

    z-index: 1;
}
</style>

