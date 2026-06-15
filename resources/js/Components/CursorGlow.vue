<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const x = ref(0)
const y = ref(0)

const move = (e) => {
    x.value = e.clientX
    y.value = e.clientY
}

onMounted(() => {
    window.addEventListener('mousemove', move)
})

onUnmounted(() => {
    window.removeEventListener('mousemove', move)
})
</script>

<template>
    <div
        class="cursor-glow"
        :style="{
            left: x + 'px',
            top: y + 'px'
        }"
    />
</template>

<style scoped>
.cursor-glow {
    position: fixed;

    width: 500px;
    height: 500px;

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(34,211,238,.15),
            transparent 70%
        );

    transform: translate(-50%, -50%);

    pointer-events: none;

    z-index: 0;

    transition:
        left .15s linear,
        top .15s linear;
}
</style>