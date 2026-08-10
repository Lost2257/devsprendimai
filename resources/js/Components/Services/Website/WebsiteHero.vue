<script setup>
import { Link } from '@inertiajs/vue3'
import { useReveal } from '@/composables/useReveal'

useReveal('.hero-item')
</script>

<template>

<section class="relative overflow-hidden py-20 lg:py-32 md:mt-5">

    <div class="hero-glow"></div>

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            <!-- LEFT -->

            <div class="hero-item">

                <span class="hero-badge">
                    Interneto svetainių kūrimas
                </span>

                <h1 class="hero-title">

                    Kuriame

                    <span>
                        interneto svetaines,
                    </span>

                    kurios parduoda.

                </h1>

                <p class="hero-text">

                    Modernios, greitos ir SEO optimizuotos svetainės,
                    kurios reprezentuoja jūsų verslą ir generuoja užklausas.

                </p>

                <div class="hero-buttons">

                    <a
                        href="#contact"
                        class="btn-primary"
                    >
                        Gauti pasiūlymą
                    </a>

                </div>

                <div class="hero-stats">

                    <div>

                        <h3>50+</h3>
                        <span>Projektų</span>

                    </div>

                    <div>

                        <h3>95%</h3>
                        <span>Grįžtančių klientų</span>

                    </div>

                    <div>

                        <h3>24/7</h3>
                        <span>Pagalba</span>

                    </div>

                </div>

            </div>

            <!-- RIGHT -->

            <div class="hero-item">

                <div class="browser">

                    <div class="browser-top">

                        <div class="dots">

                            <span></span>
                            <span></span>
                            <span></span>

                        </div>

                        <div class="url">
                            devsprendimai.lt
                        </div>

                    </div>

                    <img
                        src="/images/devsprendimai.png"
                        class="browser-image"
                    >

                    <img
                        src="/images/devsprendimai.png"
                        class="phone"
                    >

                </div>

            </div>

        </div>

    </div>

</section>

</template>

<style scoped>

.hero-glow{

    position:absolute;

    width:900px;
    height:900px;

    left:50%;
    top:-300px;

    transform:translateX(-50%);

    border-radius:999px;

    background:rgba(34,211,238,.08);

    filter:blur(180px);

    pointer-events:none;

}

.hero-badge{

    display:inline-flex;

    padding:10px 18px;

    border-radius:999px;

    background:rgba(34,211,238,.08);

    border:1px solid rgba(34,211,238,.25);

    color:#22D3EE;

    font-size:13px;

    text-transform:uppercase;

    letter-spacing:.18em;

}

.hero-title{

    margin-top:30px;

    font-size:68px;

    line-height:1.05;

    font-weight:900;

}

.hero-title span{

    color:#22D3EE;

}

.hero-text{

    margin-top:30px;

    max-width:560px;

    color:#94A3B8;

    font-size:18px;

    line-height:1.8;

}

.hero-buttons{

    display:flex;

    flex-wrap:wrap;

    gap:16px;

    margin-top:40px;

}

.btn-primary{

    padding:16px 34px;

    border-radius:16px;

    background:#22D3EE;

    color:black;

    font-weight:700;

}

.btn-secondary{

    padding:16px 34px;

    border-radius:16px;

    border:1px solid rgba(255,255,255,.12);

}

.hero-stats{

    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:24px;

    margin-top:60px;

}

.hero-stats h3{

    color:#22D3EE;

    font-size:40px;

    font-weight:900;

}

.hero-stats span{

    color:#64748B;

}

.browser{

    position:relative;

    background:#111827;

    border:1px solid rgba(255,255,255,.08);

    border-radius:26px;

    overflow:hidden;

    transition:.45s;

    box-shadow:0 50px 100px rgba(0,0,0,.45);

}

.browser:hover{

    transform:
        perspective(1600px)
        rotateY(-6deg)
        rotateX(2deg);

}

.browser-top{

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:18px 24px;

    border-bottom:1px solid rgba(255,255,255,.08);

}

.dots{

    display:flex;

    gap:8px;

}

.dots span{

    width:10px;
    height:10px;

    border-radius:999px;

    background:#334155;

}

.url{

    color:#94A3B8;

    font-size:14px;

}

.browser-image{

    display:block;

    width:100%;

}

.phone{

    position:absolute;

    width:180px;

    right:-25px;

    bottom:-25px;

    border-radius:34px;

    border:8px solid #0B0F19;

    box-shadow:0 25px 60px rgba(0,0,0,.5);

}

@media(max-width:1024px){

.hero-text{

font-size:20px;

}

.hero-title{

    font-size:52px;

}

.hero-stats{

    gap:30px;

}

.phone{

    width:120px;

}

.hero-buttons{

flex-direction:column;

}

.hero-buttons a{

width:100%;

text-align:center;

}

.hero-stats{

grid-template-columns:1fr;

gap:22px;

text-align:center;

}

}

@media(min-width:768px){

.phone{

display:none;

}

.hero-title{

font-size:56px;

}

}

@media(min-width:1280px){

.hero-title{

font-size:68px;

}

}

</style>