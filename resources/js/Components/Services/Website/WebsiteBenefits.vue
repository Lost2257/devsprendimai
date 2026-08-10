<script setup>
import { useReveal } from '@/composables/useReveal'

useReveal('.benefit-card')
</script>

<template>

<section class="py-28">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-20">

            <span class="section-number">
                Kodėl mes?
            </span>

            <h2 class="section-title">

                Svetainė nėra tik dizainas.

                <span>
                    Tai jūsų pardavimų įrankis.
                </span>

            </h2>

            <p class="section-text">

                Kiekvieną projektą kuriame orientuodamiesi į greitį,
                patogumą ir realų rezultatą verslui.

            </p>

        </div>

        <div class="benefits-grid">

            <div class="benefit-card">

                <div class="icon">⚡</div>

                <h3>Greitis</h3>

                <p>
                    Optimizuotas kodas ir greitas svetainės veikimas,
                    kad lankytojai nelauktų.
                </p>

            </div>

            <div class="benefit-card">

                <div class="icon">📈</div>

                <h3>SEO optimizacija</h3>

                <p>
                    Tvarkinga struktūra ir techninis SEO nuo pirmos dienos.
                </p>

            </div>

            <div class="benefit-card">

                <div class="icon">📱</div>

                <h3>Responsive</h3>

                <p>
                    Svetainė puikiai atrodo telefone,
                    planšetėje ir kompiuteryje.
                </p>

            </div>

            <div class="benefit-card">

                <div class="icon">🛠</div>

                <h3>Lengvas valdymas</h3>

                <p>
                    Administravimo sistema leidžia
                    patiems redaguoti turinį.
                </p>

            </div>

            <div class="benefit-card">

                <div class="icon">🔒</div>

                <h3>Saugumas</h3>

                <p>
                    SSL, atsarginės kopijos ir apsauga
                    nuo dažniausių grėsmių.
                </p>

            </div>

            <div class="benefit-card">

                <div class="icon">🚀</div>

                <h3>Paruošta augimui</h3>

                <p>
                    Svetainė lengvai plečiama,
                    kai jūsų verslas auga.
                </p>

            </div>

        </div>

    </div>

</section>

</template>

<style scoped>

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

    max-width:760px;

    margin:30px auto 0;

    color:#94A3B8;

    font-size:20px;

    line-height:1.8;

}

.benefits-grid{

    margin-top:80px;

    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:28px;

}

.benefit-card{

    position:relative;

    padding:42px;

    border-radius:30px;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    overflow:hidden;

    transition:.35s;

}

.benefit-card::before{

    content:"";

    position:absolute;

    inset:-1px;

    opacity:0;

    transition:.35s;

    background:
        radial-gradient(circle at top right,
        rgba(34,211,238,.18),
        transparent 60%);

}

.benefit-card:hover{

    transform:translateY(-8px);

    border-color:rgba(34,211,238,.25);

    box-shadow:
        0 25px 60px rgba(0,0,0,.35);

}

.benefit-card:hover::before{

    opacity:1;

}

.icon{

    width:72px;
    height:72px;

    display:flex;

    align-items:center;
    justify-content:center;

    border-radius:22px;

    background:rgba(34,211,238,.08);

    font-size:32px;

    margin-bottom:28px;

}

.benefit-card h3{

    font-size:28px;

    font-weight:800;

}

.benefit-card p{

    margin-top:18px;

    color:#94A3B8;

    line-height:1.8;

}

@media(max-width:1024px){

.benefits-grid{

    grid-template-columns:repeat(2,1fr);

}

}

@media(max-width:768px){

.section-title{

    font-size:40px;

}

.section-text{

    font-size:18px;

}

.benefits-grid{

    grid-template-columns:1fr;

}

.benefit-card{

    padding:32px;

}

}

</style>