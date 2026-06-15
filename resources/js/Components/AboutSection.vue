<script setup>
import { onMounted } from 'vue'
import gsap from 'gsap'
import ScrollTrigger from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

onMounted(() => {

    gsap.from('.about-title', {
        scrollTrigger: {
            trigger: '.about-section',
            start: 'top 80%',
        },
        y: 100,
        opacity: 0,
        duration: 1,
    })

    gsap.from('.about-text', {
        scrollTrigger: {
            trigger: '.about-section',
            start: 'top 70%',
        },
        y: 60,
        opacity: 0,
        duration: 1,
        delay: .2
    })

})
</script>

<template>
    <section
        class="
        about-section

        min-h-screen

        flex
        items-center

        px-6
        "
    >
        <div class="max-w-6xl mx-auto">

            <h2
                class="
                about-title

                text-6xl
                md:text-8xl

                font-black
                "
            >
                Nestatau svetainių.
            </h2>

            <h2
                class="
                about-title

                text-6xl
                md:text-8xl

                font-black

                text-cyan-400
                "
            >
                Kuriu sistemas.
            </h2>

            <p
                class="
                about-text

                mt-10

                text-2xl
                text-slate-400

                max-w-3xl
                "
            >
                CRM, rezervacijos,
                administravimo panelės,
                API integracijos ir
                automatizacijos.
            </p>

        </div>
    </section>
</template>