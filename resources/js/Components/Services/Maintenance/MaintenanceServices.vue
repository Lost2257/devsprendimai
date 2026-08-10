<template>

<section class="py-32">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">

            Kas įeina?

        </span>

        <h2 class="section-title">

            Visapusiška
            <span>svetainės priežiūra.</span>

        </h2>

        <p class="section-text">

            Pasirūpiname ne tik atnaujinimais – stebime svetainės veikimą,
            optimizuojame greitį, sprendžiame technines problemas ir
            užtikriname stabilų darbą.

        </p>

    </div>

    <div class="grid lg:grid-cols-3 gap-8">

        <div class="service-card">

            <div class="icon">🔄</div>

            <h3>Atnaujinimai</h3>

            <p>

                WordPress, Laravel, moduliai, įskiepiai,
                bibliotekos bei saugumo pataisos.

            </p>

        </div>

        <div class="service-card">

            <div class="icon">🛡️</div>

            <h3>Saugumo priežiūra</h3>

            <p>

                Kenkėjiškų programų prevencija,
                prisijungimų apsauga,
                SSL sertifikatų priežiūra.

            </p>

        </div>

        <div class="service-card">

            <div class="icon">💾</div>

            <h3>Atsarginės kopijos</h3>

            <p>

                Automatinės kopijos,
                greitas atkūrimas
                bei saugus duomenų saugojimas.

            </p>

        </div>

        <div class="service-card">

            <div class="icon">⚡</div>

            <h3>Našumo optimizavimas</h3>

            <p>

                Spartiname svetainę,
                optimizuojame duomenų bazę,
                talpyklą ir vaizdus.

            </p>

        </div>

        <div class="service-card">

            <div class="icon">📈</div>

            <h3>Stebėjimas</h3>

            <p>

                Nuolat stebime svetainės veikimą,
                serverio būklę ir prieinamumą.

            </p>

        </div>

        <div class="service-card">

            <div class="icon">🛠️</div>

            <h3>Techninė pagalba</h3>

            <p>

                Greitai šaliname klaidas,
                konsultuojame ir atliekame
                reikalingus pakeitimus.

            </p>

        </div>

    </div>

</div>

</section>

</template>

<style scoped>

.section-label{

display:inline-flex;
padding:10px 18px;
border-radius:999px;
background:rgba(34,211,238,.08);
border:1px solid rgba(34,211,238,.2);
color:#22d3ee;
font-weight:600;

}

.section-title{

margin-top:30px;
font-size:56px;
font-weight:900;

}

.section-title span{

color:#22d3ee;

}

.section-text{

max-width:760px;
margin:30px auto 0;
font-size:20px;
line-height:1.9;
color:#94a3b8;

}

.service-card{

padding:38px;

border-radius:28px;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

transition:.35s;

}

.service-card:hover{

transform:translateY(-8px);

border-color:rgba(34,211,238,.35);

box-shadow:0 20px 45px rgba(34,211,238,.12);

}

.icon{

width:72px;
height:72px;

display:flex;
align-items:center;
justify-content:center;

border-radius:20px;

background:rgba(34,211,238,.08);

font-size:34px;

margin-bottom:26px;

}

.service-card h3{

font-size:28px;

font-weight:800;

margin-bottom:16px;

}

.service-card p{

line-height:1.9;

color:#94a3b8;

}

@media(max-width:768px){

.section-title{

font-size:42px;

}

}

</style>