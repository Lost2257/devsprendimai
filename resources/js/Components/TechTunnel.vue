<script setup>
import { onMounted, ref } from 'vue'
import gsap from 'gsap'
import ScrollTrigger from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const tunnel = ref(null)

onMounted(() => {

    const wrapper = tunnel.value
    const left = wrapper.querySelector('.left')
    const right = wrapper.querySelector('.right')
    const cards = gsap.utils.toArray('.card')

    // 🔥 1. PIN SCENE (kamera)
    ScrollTrigger.create({
        trigger: wrapper,
        start: "top top",
        end: "+=2500",
        scrub: true,
        pin: true
    })

    // 🔥 2. WALL DEPTH (tunelis)
    gsap.to(left, {
        x: -250,
        z: -600,
        rotateY: 20,
        ease: "none",
        scrollTrigger: {
            trigger: wrapper,
            start: "top top",
            end: "+=2500",
            scrub: true,
            pin: true
        }
    })

    gsap.to(right, {
        x: 250,
        z: -600,
        rotateY: -20,
        ease: "none",
        scrollTrigger: {
            trigger: wrapper,
            start: "top top",
            end: "+=2500",
            scrub: true,
            pin: true
        }
    })

    // 🔥 3. CARDS = CAMERA PASS THROUGH (ESMĖ)
cards.forEach((card, i) => {

    const depth = i * -600

    gsap.fromTo(card,
        {
            z: 800,
            scale: 0.5,
            opacity: 0
        },
        {
            z: depth,
            scale: 1,
            opacity: 1,
            ease: "none",
            scrollTrigger: {
                trigger: wrapper,
                start: "top top",
                end: "+=2500",
                scrub: true,
                pin: true
            }
        }
    )
})

    gsap.to(wrapper, {
    rotateX: 8,
    rotateY: -6,
    ease: "none",
    transformOrigin: "center center",
    scrollTrigger: {
        trigger: wrapper,
        start: "top top",
        end: "+=2500",
        scrub: true,
        pin: true
    }
})

})
</script>

<template>
    <section ref="tunnel" class="tunnel-wrapper">

        <div class="wall left"></div>
        <div class="wall right"></div>

        <div class="card card-1">
            CRM
        </div>

        <div class="card card-2">
            ERP
        </div>

        <div class="card card-3">
            API
        </div>

        <div class="card card-4">
            DASHBOARD
        </div>

        <div class="card card-5">
            AUTOMATION
        </div>

    </section>
</template>

<style scoped>

.tunnel {
    position: relative;
    min-height: 300vh; /* vietoj height */
    background: #0B0F19;
    overflow: visible;
}

.tunnel-wrapper {
    position: relative;

    height: 100vh;
    min-height: 250vh; /* 🔥 SVARBIAUSIA */

    perspective: 1600px;
    transform-style: preserve-3d;
}

.tunnel-wrapper::before {
    content: "";
    position: absolute;
    inset: 0;

    background: radial-gradient(
        circle at center,
        transparent 30%,
        rgba(11,15,25,0.4) 70%,
        rgba(11,15,25,0.9) 100%
    );

    pointer-events: none;
    z-index: 10;
}

.wall {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 45vw;

    opacity: 0.9;
    filter: blur(0.2px);

    transform-style: preserve-3d;
}

.left {
    left: -20vw;

    transform:
        rotateY(35deg)
        skewY(-10deg);

    background: linear-gradient(
        to right,
        rgba(255,255,255,.05),
        transparent
    );
}

.right {
    right: -20vw;

    transform:
        rotateY(-35deg)
        skewY(10deg);

    background: linear-gradient(
        to left,
        rgba(255,255,255,.05),
        transparent
    );
}

.card {
    position: absolute;

    padding: 40px 70px;
    border-radius: 24px;

    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);

    backdrop-filter: blur(25px);

    font-size: 42px;
    font-weight: 800;

    color: white;

    transform-style: preserve-3d;
    will-change: transform;
}

.card-1{
    top:15%;
    left:18%;
}

.card-2{
    top:35%;
    right:15%;
}

.card-3{
    top:55%;
    left:22%;
}

.card-4{
    top:75%;
    right:18%;
}

.card-5{
    top:90%;
    left:25%;
}

</style>