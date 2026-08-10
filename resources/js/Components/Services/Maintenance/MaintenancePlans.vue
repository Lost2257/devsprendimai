<template>

<section
    id="plans"
    class="py-32"
>

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">

            Priežiūros planai

        </span>

        <h2 class="section-title">

            Pasirinkite
            <span>tinkamiausią planą.</span>

        </h2>

        <p class="section-text">

            Kiekvienas verslas yra skirtingas, todėl
            siūlome kelis priežiūros lygius.
            Jei reikia – paruošime individualų planą.

        </p>

    </div>

    <div class="grid lg:grid-cols-3 gap-8">

        <!-- STARTER -->

        <div class="plan-card">

            <h3>

                Starter

            </h3>

            <p class="subtitle">

                Nedidelėms svetainėms

            </p>

            <ul>

                <li>✓ Sistemos atnaujinimai</li>

                <li>✓ Įskiepių atnaujinimai</li>

                <li>✓ Atsarginės kopijos</li>

                <li>✓ Veikimo stebėjimas</li>

                <li>✓ El. pašto pagalba</li>

            </ul>

            <a
                href="#contact"
                class="plan-btn"
            >

                Gauti pasiūlymą

            </a>

        </div>

        <!-- BUSINESS -->

        <div class="plan-card featured">

            <div class="badge">

                Populiariausias

            </div>

            <h3>

                Business

            </h3>

            <p class="subtitle">

                Augančiam verslui

            </p>

            <ul>

                <li>✓ Viskas iš Starter</li>

                <li>✓ Greičio optimizavimas</li>

                <li>✓ Saugumo patikros</li>

                <li>✓ Smulkūs pakeitimai</li>

                <li>✓ Prioritetinė pagalba</li>

            </ul>

            <a
                href="#contact"
                class="plan-btn"
            >

                Gauti pasiūlymą

            </a>

        </div>

        <!-- PREMIUM -->

        <div class="plan-card">

            <h3>

                Premium

            </h3>

            <p class="subtitle">

                Maksimali priežiūra

            </p>

            <ul>

                <li>✓ Viskas iš Business</li>

                <li>✓ Serverio priežiūra</li>

                <li>✓ 24/7 monitoringas</li>

                <li>✓ Neriboti smulkūs darbai</li>

                <li>✓ Individualūs sprendimai</li>

            </ul>

            <a
                href="#contact"
                class="plan-btn"
            >

                Gauti pasiūlymą

            </a>

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

.plan-card{

position:relative;

padding:48px 40px;

border-radius:30px;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

transition:.35s;

}

.plan-card:hover{

transform:translateY(-10px);

border-color:rgba(34,211,238,.35);

box-shadow:0 25px 55px rgba(34,211,238,.12);

}

.featured{

border:2px solid rgba(34,211,238,.45);

box-shadow:0 30px 70px rgba(34,211,238,.15);

}

.badge{

position:absolute;

top:-15px;
left:50%;

transform:translateX(-50%);

padding:8px 18px;

border-radius:999px;

background:#22d3ee;

color:#000;

font-weight:700;

font-size:14px;

}

.plan-card h3{

font-size:34px;

font-weight:900;

margin-top:10px;

}

.subtitle{

margin:14px 0 34px;

color:#94a3b8;

}

.plan-card ul{

display:flex;

flex-direction:column;

gap:18px;

list-style:none;

padding:0;

margin:0;

}

.plan-card li{

color:#cbd5e1;

font-size:17px;

}

.plan-btn{

display:block;

margin-top:40px;

text-align:center;

padding:16px;

border-radius:18px;

background:#22d3ee;

color:#000;

font-weight:800;

transition:.3s;

text-decoration:none;

}

.plan-btn:hover{

transform:translateY(-3px);

box-shadow:0 15px 35px rgba(34,211,238,.35);

}

@media(max-width:768px){

.section-title{

font-size:42px;

}

}

</style>