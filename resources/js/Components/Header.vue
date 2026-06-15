<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const scrolled = ref(false)
const mobileMenu = ref(false)

const onScroll = () => {
    scrolled.value = window.scrollY > 50
}

onMounted(() => {
    window.addEventListener('scroll', onScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll)
})
</script>

<template>
    <header
        class="
        fixed
        top-0
        left-0
        right-0
        z-50
        "
    >
        <div
            class="max-w-7xl mx-auto px-6 transition-all duration-300"
            :class="[
                scrolled
                    ? 'py-2'
                    : 'py-5'
            ]"
        >
        <div
            class="
            flex
            items-center
            justify-between

            backdrop-blur-xl

            rounded-2xl

            px-6

            transition-all
            duration-300
            "
            :class="[
                scrolled
                    ? 'bg-white/10 border border-cyan-400/20 py-3 shadow-[0_0_30px_rgba(34,211,238,0.15)]'
                    : 'bg-white/5 border border-white/10 py-4'
            ]"
        >
                <!-- Logo -->
                <a href="/">
                    <img
                        src="/images/logo.png"
                        alt="DevSprendimai"
                        class="transition-all duration-300"
                        :class="[
                            scrolled
                                ? 'h-8'
                                : 'h-10'
                        ]"
                    />
                </a>

                <!-- Menu -->
                <nav
                    class="
                    hidden
                    md:flex
                    items-center
                    gap-10
                    "
                >
                     <!-- <a
                        href="#services"
                        class="text-slate-300 hover:text-cyan-400 transition font-medium text-sm tracking-wide">
                        Paslaugos
                    </a> -->
                </nav>

                <button
                    @click="mobileMenu = !mobileMenu"
                    class="
                    md:hidden

                    flex
                    flex-col
                    gap-1.5

                    p-2
                    "
                >
                    <span
                        class="w-6 h-0.5 bg-white"
                    ></span>

                    <span
                        class="w-6 h-0.5 bg-white"
                    ></span>

                    <span
                        class="w-6 h-0.5 bg-white"
                    ></span>
                </button>

                <!-- CTA -->
                <a
                    href="#contact"
                    class="
                    hidden
                    md:flex

                    items-center

                    px-6 py-3

                    rounded-full

                    bg-cyan-400
                    text-black

                    font-semibold

                    shadow-[0_0_25px_rgba(34,211,238,0.35)]

                    hover:scale-105
                    hover:shadow-[0_0_40px_rgba(34,211,238,0.55)]

                    transition
                    "
                >
                    Susisiekti
                </a>
            </div>
        </div>
        <transition
    enter-active-class="duration-300"
    leave-active-class="duration-300"
    enter-from-class="opacity-0 -translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 -translate-y-4"
>
    <div
        v-if="mobileMenu"
        class="
        md:hidden

        mt-3
        mx-6

        rounded-2xl

        bg-[#101624]/95
        backdrop-blur-xl

        border
        border-white/10

        p-6
        "
    >
        <nav class="flex flex-col gap-5">

            <!-- <a
                href="#services"
                @click="mobileMenu = false"
                class="text-slate-300"
            >
                Paslaugos
            </a>

            <a
                href="#projects"
                @click="mobileMenu = false"
                class="text-slate-300"
            >
                Projektai
            </a>

            <a
                href="#about"
                @click="mobileMenu = false"
                class="text-slate-300"
            >
                Apie
            </a>

            <a
                href="#contact"
                @click="mobileMenu = false"
                class="text-slate-300"
            >
                Kontaktai
            </a> -->

            <a
                href="#contact"
                class="
                mt-2

                py-3

                text-center

                rounded-full

                bg-cyan-400
                text-black

                font-semibold
                "
            >
                Susisiekti
            </a>

        </nav>
    </div>
</transition>
    </header>
</template>