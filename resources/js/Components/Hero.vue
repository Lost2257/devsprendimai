<script setup>
import { ref, onMounted } from 'vue'
import gsap from 'gsap'
import ScrollTrigger from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const hero = ref(null)

onMounted(() => {

    gsap.from('.hero-badge', {
        y: 30,
        opacity: 0,
        duration: .8
    })

    gsap.from('.hero-title', {
        y: 80,
        opacity: 0,
        duration: 1,
        delay: .2
    })

    gsap.from('.hero-text', {
        y: 50,
        opacity: 0,
        duration: .8,
        delay: .5
    })

    gsap.from('.hero-buttons', {
        y: 40,
        opacity: 0,
        duration: .8,
        delay: .8
    })

    gsap.to('.hero-bg-text', {
        y: -250,
        ease: 'none',
        scrollTrigger: {
            trigger: hero.value,
            start: 'top top',
            end: 'bottom top',
            scrub: true
        }
    })

    gsap.to('.hero-glow', {
        y: -150,
        ease: 'none',
        scrollTrigger: {
            trigger: hero.value,
            start: 'top top',
            end: 'bottom top',
            scrub: true
        }
    })

})
</script>

<template>
    <section
        ref="hero"
        class="
        relative
        min-h-[75vh] lg:min-h-screen
        flex
        items-center
        overflow-hidden
        px-6
        "
    >
        <!-- Glow -->
        <div class="hero-glow"></div>

        <div class="max-w-7xl mx-auto w-full relative z-10">

        <div
            class="
            inline-flex
            px-4
            py-2
            rounded-full

            border
            border-cyan-400/20

            bg-cyan-400/10

            text-cyan-300
            text-xs md:text-sm
            max-w-full

            hero-badge
            mb-10 lg:mb-0
            "
        >
            Interneto svetainės • El. parduotuvės • Automatizacija
        </div>

        <h1
            class="
            text-7xl
            sm:text-8xl
            md:text-8xl
            xl:text-[120px]

            font-black
            leading-[0.9]

            hero-title
            "
        >
            Padedu

            <span
                class="
                text-transparent
                bg-clip-text

                bg-gradient-to-r
                from-cyan-400
                via-blue-500
                to-indigo-500
                "
            >
                verslams
            </span>

            <br>

            augti internete
        </h1>

            <p
                class="
                mt-6
                text-base
                md:text-xl
                text-slate-400
                max-w-xl
                hero-text
                "
            >
            Nuo reprezentacinių svetainių iki WooCommerce
            parduotuvių ir verslo automatizavimo sprendimų.
            </p>

            <div
    class="
    hero-buttons
    flex
    flex-col
    sm:flex-row

    gap-3

    mt-6
    "
>
            <button
                class="
                w-full
                sm:w-auto
                justify-center
                px-6
                py-3
                sm:px-8
                sm:py-4

                rounded-full

                bg-cyan-400
                text-black

                font-bold

                hover:scale-105
                transition
                "
            >
                Aptarkime projektą
            </button>

            <button
                class="
                w-full
                sm:w-auto
                justify-center
                px-6
                py-3
                sm:px-8
                sm:py-4

                rounded-full

                border
                border-white/10

                hover:border-cyan-400

                transition
                "
            >
                Peržiūrėti darbus
            </button>
        </div>

        </div>

        <!-- Background Text -->
        <div class="hero-bg-text">
            DEVSPRENDIMAI
        </div>
    </section>
</template>

<style scoped>
.hero-glow {
    position: absolute;
    width: 500px;
    height: 500px;

    right: -250px;
    top: 50%;

    transform: translateY(-50%);

    border-radius: 50%;

    background: radial-gradient(
        circle,
        rgba(34,211,238,.25),
        rgba(79,70,229,.15),
        transparent 70%
    );

    filter: blur(80px);
}

@media (min-width: 1024px) {
    .hero-glow {
        width: 800px;
        height: 800px;
    }
}

.hero-bg-text {
    position: absolute;

    left: 50%;
    top: 50%;

    transform: translate(-50%, -50%);

    font-size: clamp(80px, 18vw, 300px);
    font-weight: 900;

    color: rgba(255,255,255,.025);

    white-space: nowrap;

    pointer-events: none;
    user-select: none;
}

@media (max-width: 1023px) {
    .hero-glow {
        width: 320px;
        height: 320px;
        right: -150px;
        top: 35%;
    }

    .hero-bg-text {
        left: 50%;
        top: 60%;
        transform: translate(-50%, -50%);
        font-size: clamp(60px, 16vw, 140px);
        max-width: 85vw;
        white-space: normal;
        text-align: center;
        line-height: 0.85;
    }
}

</style>