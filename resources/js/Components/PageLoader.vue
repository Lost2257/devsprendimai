<script setup>
defineProps({
    loading: Boolean
})
</script>

<template>
    <transition name="loader">
        <div
            v-if="loading"
            class="
            fixed
            inset-0
            z-[9999]

            flex
            flex-col
            items-center
            justify-center

            bg-[#0B0F19]
            "
        >
            <img
                src="/images/logo.png"
                alt="DevSprendimai"
                class="h-24 md:h-32"
            >

            <div
                class="
                mt-8

                w-48
                h-1

                rounded-full
                overflow-hidden

                bg-white/10
                "
            >
                <div class="loader-bar"></div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.loader-bar {
    height: 100%;
    width: 100%;

    background: linear-gradient(
        90deg,
        #22D3EE,
        #3B82F6
    );

    animation: loading 2s linear forwards;
}

@keyframes loading {
    from {
        width: 0%;
    }

    to {
        width: 100%;
    }
}

.loader-enter-active,
.loader-leave-active {
    transition: opacity .6s ease;
}

.loader-enter-from,
.loader-leave-to {
    opacity: 0;
}
</style>