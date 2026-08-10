<template>

<section class="py-32">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">
            Kodėl automatizacija?
        </span>

        <h2 class="section-title">

            Leiskite sistemoms
            <span>dirbti už jus.</span>

        </h2>

        <p class="section-text">

            Automatizuojame pasikartojančius procesus,
            kad jūsų komanda galėtų skirti laiką svarbiausiems darbams,
            o ne rutinai.

        </p>

    </div>

    <div class="grid lg:grid-cols-2 gap-8">

        <div class="benefit-card">

            <div class="icon">⚡</div>

            <div>

                <h3>Taupomas laikas</h3>

                <p>

                    Pasikartojančios užduotys vykdomos automatiškai,
                    todėl darbuotojai gali susitelkti į svarbesnius darbus.

                </p>

            </div>

        </div>

        <div class="benefit-card">

            <div class="icon">❌</div>

            <div>

                <h3>Mažiau klaidų</h3>

                <p>

                    Automatizuoti procesai sumažina žmogiškųjų klaidų
                    tikimybę ir užtikrina tikslius duomenis.

                </p>

            </div>

        </div>

        <div class="benefit-card">

            <div class="icon">🔄</div>

            <div>

                <h3>Procesai be žmogaus įsikišimo</h3>

                <p>

                    Duomenys automatiškai keliauja tarp jūsų sistemų,
                    todėl nebereikia jų kopijuoti rankiniu būdu.

                </p>

            </div>

        </div>

        <div class="benefit-card">

            <div class="icon">📈</div>

            <div>

                <h3>Didesnis produktyvumas</h3>

                <p>

                    Greitesni procesai leidžia aptarnauti daugiau klientų
                    ir efektyviau auginti verslą.

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

box-shadow:0 20px 45px rgba(34,211,238,.12);

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

font-size:32px;

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

.section-title{

font-size:42px;

}

}

</style>