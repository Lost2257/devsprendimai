<script setup>
import { useReveal } from '@/composables/useReveal'

useReveal('.process-item')
</script>

<template>

<section class="py-32">

    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-24">

            <span class="section-number">
                Procesas
            </span>

            <h2 class="section-title">

                Skaidrus procesas

                <span>
                    nuo idėjos iki paleidimo.
                </span>

            </h2>

            <p class="section-text">

                Visą kūrimo eigą planuojame etapais,
                todėl visada žinosite, kas vyksta ir kada
                projektas bus paruoštas.

            </p>

        </div>

        <div class="timeline">

            <!-- 01 -->

            <div class="process-item">

                <div class="number">
                    01
                </div>

                <div class="content">

                    <h3>
                        Susipažįstame
                    </h3>

                    <p>

                        Aptariame jūsų verslą, tikslus,
                        auditoriją ir projekto viziją.
                        Suprantame, ko reikia rezultatui.

                    </p>

                </div>

            </div>

            <!-- 02 -->

            <div class="process-item">

                <div class="number">
                    02
                </div>

                <div class="content">

                    <h3>
                        Dizainas
                    </h3>

                    <p>

                        Sukuriame modernų dizainą,
                        orientuotą į patogią vartotojo
                        patirtį ir didesnę konversiją.

                    </p>

                </div>

            </div>

            <!-- 03 -->

            <div class="process-item">

                <div class="number">
                    03
                </div>

                <div class="content">

                    <h3>
                        Programavimas
                    </h3>

                    <p>

                        Įgyvendiname funkcionalumą,
                        optimizuojame greitį,
                        SEO ir pasirūpiname saugumu.

                    </p>

                </div>

            </div>

            <!-- 04 -->

            <div class="process-item last">

                <div class="number">
                    04
                </div>

                <div class="content">

                    <h3>
                        Paleidimas
                    </h3>

                    <p>

                        Svetainė perkeliama į serverį,
                        prijungiami domenai,
                        analitika ir suteikiama pagalba.

                    </p>

                </div>

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

    line-height:1.15;

}

.section-title span{

    color:#22D3EE;

}

.section-text{

    max-width:760px;

    margin:28px auto 0;

    color:#94A3B8;

    font-size:20px;

    line-height:1.8;

}

.timeline{

    position:relative;

    margin-top:80px;

}

.timeline::before{

    content:"";

    position:absolute;

    left:48px;

    top:0;

    bottom:0;

    width:2px;

    background:
        linear-gradient(
            #22D3EE,
            rgba(34,211,238,.1)
        );

}

.process-item{

    display:grid;

    grid-template-columns:100px 1fr;

    gap:50px;

    position:relative;

    padding-bottom:70px;

}

.number{

    width:96px;
    height:96px;

    border-radius:999px;

    display:flex;

    align-items:center;
    justify-content:center;

    background:#111827;

    border:1px solid rgba(34,211,238,.3);

    color:#22D3EE;

    font-size:30px;

    font-weight:800;

    z-index:2;

}

.content{

    padding:38px;

    border-radius:28px;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    transition:.35s;

}

.content:hover{

    transform:translateY(-6px);

    border-color:rgba(34,211,238,.3);

    box-shadow:
        0 30px 70px rgba(0,0,0,.35);

}

.content h3{

    font-size:34px;

    font-weight:800;

}

.content p{

    margin-top:20px;

    color:#94A3B8;

    line-height:1.9;

    font-size:18px;

}

.last{

    padding-bottom:0;

}

@media(max-width:768px){

.timeline::before{

    left:24px;

}

.process-item{

    grid-template-columns:50px 1fr;

    gap:24px;

}

.number{

    width:48px;
    height:48px;

    font-size:18px;

}

.content{

    padding:26px;

}

.section-title{

    font-size:40px;

}

.content h3{

    font-size:26px;

}

.section-text{

    font-size:18px;

}

}

</style>