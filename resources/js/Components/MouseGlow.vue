<script setup>
import { ref, onMounted } from 'vue'

const x = ref(0)
const y = ref(0)

onMounted(() => {
    window.addEventListener('mousemove', (e) => {
        x.value = e.clientX
        y.value = e.clientY
    })
})
</script>

<template>
    <div
        class="mouse-glow"
        :style="{
            left: `${x}px`,
            top: `${y}px`
        }"
    />
</template>

<style scoped>
.mouse-glow {
    position: fixed;

    width: 500px;
    height: 500px;

    transform: translate(-50%, -50%);

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(34,211,238,.08),
            transparent 70%
        );

    pointer-events: none;

    z-index: 0;
}
</style>