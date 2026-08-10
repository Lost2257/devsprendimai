<template>

<section class="py-32">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">

            Kodėl CRM?

        </span>

        <h2 class="section-title">

            Daugiau tvarkos.
            <span>Mažiau rankinio darbo.</span>

        </h2>

        <p class="section-text">

            Individuali CRM sistema padeda automatizuoti kasdienius procesus,
            sumažina klaidų tikimybę ir leidžia komandai dirbti efektyviau.

        </p>

    </div>

    <div class="grid lg:grid-cols-2 gap-8">

        <div class="benefit-card">

            <div class="icon">⚡</div>

            <div>

                <h3>Taupomas laikas</h3>

                <p>

                    Automatizuokite pasikartojančias užduotis,
                    kad komanda galėtų susitelkti į svarbiausius darbus.

                </p>

            </div>

        </div>

        <div class="benefit-card">

            <div class="icon">👥</div>

            <div>

                <h3>Visa informacija vienoje vietoje</h3>

                <p>

                    Klientai, projektai, dokumentai ir komunikacija
                    pasiekiami vienoje sistemoje.

                </p>

            </div>

        </div>

        <div class="benefit-card">

            <div class="icon">📈</div>

            <div>

                <h3>Efektyvesni pardavimai</h3>

                <p>

                    Stebėkite visą pardavimo procesą,
                    analizuokite rezultatus ir priimkite geresnius sprendimus.

                </p>

            </div>

        </div>

        <div class="benefit-card">

            <div class="icon">🤖</div>

            <div>

                <h3>Automatizacija</h3>

                <p>

                    El. laiškai, priminimai, užduotys ir kiti procesai
                    vykdomi automatiškai.

                </p>

            </div>

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

.benefit-card{

display:flex;

gap:28px;

align-items:flex-start;

padding:36px;

border-radius:28px;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

transition:.35s;

}

.benefit-card:hover{

transform:translateY(-8px);

border-color:rgba(34,211,238,.35);

box-shadow:0 20px 50px rgba(34,211,238,.12);

}

.icon{

width:72px;

height:72px;

display:flex;

align-items:center;

justify-content:center;

flex-shrink:0;

border-radius:20px;

background:rgba(34,211,238,.08);

font-size:30px;

}

.benefit-card h3{

font-size:28px;

font-weight:800;

margin-bottom:12px;

}

.benefit-card p{

color:#94a3b8;

line-height:1.9;

}

@media(max-width:768px){

.benefit-card{

flex-direction:column;

}

}

</style>