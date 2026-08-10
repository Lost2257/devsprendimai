<script setup>
import { useReveal } from '@/composables/useReveal'

useReveal('.feature-card')
</script>

<template>

<section class="py-32">

    <div class="max-w-7xl mx-auto px-6">

        <div class="features-wrapper">

            <div class="features-left">

                <span class="section-number">
                    Ką gausite
                </span>

                <h2 class="section-title">

                    Viskas ko reikia
                    <span>šiuolaikinei svetainei.</span>

                </h2>

                <p class="section-text">

                    Kuriame ne tik gražų dizainą.
                    Jūsų svetainė bus greita, saugi,
                    lengvai valdoma ir paruošta augimui.

                </p>

            </div>

            <div class="features-grid">

                <div class="feature-card">
                    <span>✓</span>
                    Individualus dizainas
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    Responsive dizainas
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    SEO optimizacija
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    Administravimo sistema
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    Kontaktų formos
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    Google Analytics
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    SSL sertifikatas
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    Greitas veikimas
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    Saugumo sprendimai
                </div>

                <div class="feature-card">
                    <span>✓</span>
                    Pagalba po paleidimo
                </div>

            </div>

        </div>

    </div>

</section>

</template>

<style scoped>

.features-wrapper{

    display:grid;

    grid-template-columns:420px 1fr;

    gap:80px;

    align-items:start;

}

.section-number{

    color:#22D3EE;

    text-transform:uppercase;

    letter-spacing:.25em;

    font-size:14px;

    font-weight:700;

}

.section-title{

    margin-top:22px;

    font-size:56px;

    font-weight:900;

    line-height:1.15;

}

.section-title span{

    color:#22D3EE;

}

.section-text{

    margin-top:30px;

    color:#94A3B8;

    line-height:1.9;

    font-size:20px;

}

.features-grid{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:22px;

}

.feature-card{

    display:flex;

    align-items:center;

    gap:18px;

    padding:24px 28px;

    border-radius:22px;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    font-size:18px;

    transition:.35s;

}

.feature-card span{

    width:42px;
    height:42px;

    display:flex;

    align-items:center;
    justify-content:center;

    border-radius:999px;

    background:rgba(34,211,238,.12);

    color:#22D3EE;

    font-weight:700;

    flex-shrink:0;

}

.feature-card:hover{

    transform:translateY(-5px);

    border-color:rgba(34,211,238,.3);

    box-shadow:
        0 25px 60px rgba(0,0,0,.35);

}

@media(max-width:1100px){

.features-wrapper{

    grid-template-columns:1fr;

    gap:60px;

}

}

@media(max-width:768px){

.section-title{

    font-size:40px;

}

.section-text{

    font-size:18px;

}

.features-grid{

    grid-template-columns:1fr;

}

.feature-card{

    font-size:17px;

}

}

</style>