<script setup>
import { useReveal } from '@/composables/useReveal'

useReveal('.package-card')
</script>

<template>

<section class="py-28">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-20">

            <span class="section-number">
                Sprendimai
            </span>

            <h2 class="section-title">

                Sukursime sprendimą,

                <span>atitinkantį jūsų verslą.</span>

            </h2>

            <p class="section-text">

                Nuo reprezentacinių svetainių iki individualių sistemų
                ir automatizacijų.

            </p>

        </div>

        <div class="packages">

            <!-- CARD -->

            <div class="package-card">

                <span class="package-tag">
                    POPULIARIAUSIA
                </span>

                <h3>
                    Reprezentacinė svetainė
                </h3>

                <p>
                    Moderni interneto svetainė,
                    skirta pristatyti jūsų verslą
                    ir generuoti užklausas.
                </p>

                <ul>

                    <li>✓ Individualus dizainas</li>
                    <li>✓ Responsive</li>
                    <li>✓ SEO optimizacija</li>
                    <li>✓ Kontaktų forma</li>
                    <li>✓ Google Analytics</li>
                    <li>✓ Administravimo sistema</li>

                </ul>

                <a href="#contact">
                    Gauti pasiūlymą
                </a>

            </div>

            <!-- CARD -->

            <div class="package-card featured">

                <div class="featured-badge">

                    Rekomenduojama

                </div>

                <h3>
                    Verslo sistema
                </h3>

                <p>

                    Nestandartiniai projektai,
                    rezervacijos, CRM,
                    klientų zonos,
                    API integracijos.

                </p>

                <ul>

                    <li>✓ Individualus funkcionalumas</li>
                    <li>✓ CRM sistemos</li>
                    <li>✓ Automatizacijos</li>
                    <li>✓ Mokėjimų integracijos</li>
                    <li>✓ API integracijos</li>
                    <li>✓ Pilnas palaikymas</li>

                </ul>

                <a href="#contact">
                    Aptarkime projektą
                </a>

            </div>

            <!-- CARD -->

            <div class="package-card">

                <span class="package-tag">
                    AUGIMUI
                </span>

                <h3>
                    Svetainės priežiūra
                </h3>

                <p>

                    Prižiūrime jau veikiančias
                    svetaines ir nuolat jas
                    tobuliname.

                </p>

                <ul>

                    <li>✓ Atnaujinimai</li>
                    <li>✓ Atsarginės kopijos</li>
                    <li>✓ Saugumas</li>
                    <li>✓ Turinio pakeitimai</li>
                    <li>✓ Greičio optimizacija</li>
                    <li>✓ Pagalba</li>

                </ul>

                <a href="#contact">
                    Sužinoti daugiau
                </a>

            </div>

        </div>

    </div>

</section>

</template>

<style scoped>

.section-number{

    color:#22D3EE;

    letter-spacing:.25em;

    text-transform:uppercase;

    font-size:14px;

    font-weight:700;

}

.section-title{

    margin-top:20px;

    font-size:56px;

    font-weight:900;

}

.section-title span{

    color:#22D3EE;

}

.section-text{

    max-width:720px;

    margin:26px auto 0;

    color:#94A3B8;

    font-size:20px;

    line-height:1.8;

}

.packages{

    margin-top:80px;

    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:30px;

}

.package-card{

    position:relative;

    padding:42px;

    border-radius:32px;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    transition:.35s;

    overflow:hidden;

}

.package-card:hover{

    transform:translateY(-10px);

    border-color:rgba(34,211,238,.25);

    box-shadow:
        0 35px 80px rgba(0,0,0,.4);

}

.featured{

    border-color:#22D3EE;

    background:
    linear-gradient(
        180deg,
        rgba(34,211,238,.08),
        rgba(255,255,255,.03)
    );

}

.featured-badge{

    position:absolute;

    top:24px;
    right:24px;

    padding:8px 14px;

    border-radius:999px;

    background:#22D3EE;

    color:black;

    font-size:12px;

    font-weight:700;

}

.package-tag{

    color:#22D3EE;

    font-size:13px;

    letter-spacing:.18em;

}

.package-card h3{

    margin-top:18px;

    font-size:34px;

    font-weight:800;

}

.package-card p{

    margin-top:18px;

    color:#94A3B8;

    line-height:1.8;

}

.package-card ul{

    margin-top:30px;

    display:flex;

    flex-direction:column;

    gap:14px;

    color:#E2E8F0;

}

.package-card li{

    list-style:none;

}

.package-card a{

    display:inline-flex;

    margin-top:36px;

    padding:16px 28px;

    border-radius:16px;

    background:#22D3EE;

    color:black;

    font-weight:700;

    transition:.3s;

}

.package-card a:hover{

    transform:translateY(-2px);

}

@media(max-width:1100px){

.packages{

    grid-template-columns:1fr;

}

.section-title{

    font-size:42px;

}

}

</style>