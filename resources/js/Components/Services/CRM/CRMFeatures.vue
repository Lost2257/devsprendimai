<template>

<section class="py-32">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">
            Integracijos
        </span>

        <h2 class="section-title">

            CRM sistema
            <span>prisitaiko prie jūsų verslo.</span>

        </h2>

        <p class="section-text">

            Integruojame CRM su populiariausiomis sistemomis,
            mokėjimų platformomis, el. paštu, apskaitos programomis
            ir individualiais API sprendimais.

        </p>

    </div>

    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">

        <div class="integration-card">
            <div class="logo">📧</div>
            <h3>Gmail</h3>
        </div>

        <div class="integration-card">
            <div class="logo">📨</div>
            <h3>Outlook</h3>
        </div>

        <div class="integration-card">
            <div class="logo">📅</div>
            <h3>Google Calendar</h3>
        </div>

        <div class="integration-card">
            <div class="logo">💳</div>
            <h3>Stripe</h3>
        </div>

        <div class="integration-card">
            <div class="logo">💶</div>
            <h3>Paysera</h3>
        </div>

        <div class="integration-card">
            <div class="logo">🛒</div>
            <h3>WooCommerce</h3>
        </div>

        <div class="integration-card">
            <div class="logo">🛍</div>
            <h3>Shopify</h3>
        </div>

        <div class="integration-card">
            <div class="logo">🤖</div>
            <h3>OpenAI</h3>
        </div>

        <div class="integration-card">
            <div class="logo">⚙️</div>
            <h3>REST API</h3>
        </div>

        <div class="integration-card">
            <div class="logo">🔗</div>
            <h3>Webhooks</h3>
        </div>

        <div class="integration-card">
            <div class="logo">⚡</div>
            <h3>Make</h3>
        </div>

        <div class="integration-card">
            <div class="logo">🚀</div>
            <h3>Zapier</h3>
        </div>

    </div>

    <div class="custom-box">

        <h3>

            Naudojate kitą sistemą?

        </h3>

        <p>

            Galime sukurti individualią integraciją su jūsų naudojama
            sistema ar API. CRM prisitaiko prie jūsų verslo, o ne atvirkščiai.

        </p>

        <a
            href="#contact"
            class="cta-btn"
        >

            Aptarti integraciją

        </a>

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

.integration-card{

padding:34px;

border-radius:24px;

text-align:center;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

transition:.35s;

}

.integration-card:hover{

transform:translateY(-8px);

border-color:rgba(34,211,238,.35);

box-shadow:0 20px 45px rgba(34,211,238,.15);

}

.logo{

width:80px;

height:80px;

margin:auto;

margin-bottom:20px;

display:flex;

align-items:center;

justify-content:center;

border-radius:22px;

background:rgba(34,211,238,.08);

font-size:36px;

}

.integration-card h3{

font-size:22px;

font-weight:700;

}

.custom-box{

margin-top:90px;

padding:60px;

border-radius:32px;

text-align:center;

background:linear-gradient(
135deg,
rgba(34,211,238,.08),
rgba(255,255,255,.02)
);

border:1px solid rgba(34,211,238,.15);

}

.custom-box h3{

font-size:42px;

font-weight:900;

margin-bottom:20px;

}

.custom-box p{

max-width:760px;

margin:auto;

color:#94a3b8;

font-size:20px;

line-height:1.9;

}

.cta-btn{

display:inline-flex;

margin-top:34px;

padding:18px 40px;

border-radius:18px;

background:#22d3ee;

color:#000;

font-weight:700;

transition:.3s;

}

.cta-btn:hover{

transform:translateY(-4px);

box-shadow:0 15px 35px rgba(34,211,238,.35);

}

</style>