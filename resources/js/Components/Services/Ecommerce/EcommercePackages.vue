<template>

<section
    id="packages"
    class="py-32"
>

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">
            Sprendimų paketai
        </span>

        <h2 class="section-title">

            Pasirinkite
            <span>tinkamiausią sprendimą.</span>

        </h2>

        <p class="section-text">

            Kiekviena el. parduotuvė kuriama individualiai,
            tačiau šie paketai padeda suprasti, koks sprendimas
            geriausiai atitinka jūsų verslo poreikius.

        </p>

    </div>

    <div class="grid lg:grid-cols-3 gap-8">

        <!-- START -->

        <div class="package-card">

            <span class="package-tag">
                Start
            </span>

            <h3>
                Mažam verslui
            </h3>

            <p class="package-desc">
                Greita ir profesionali el. parduotuvė
                pradedančiam verslui.
            </p>

            <ul>

                <li>✓ WooCommerce</li>

                <li>✓ Iki 50 produktų</li>

                <li>✓ Mokėjimų integracija</li>

                <li>✓ Omniva / DPD</li>

                <li>✓ Kontaktų forma</li>

                <li>✓ SEO pagrindai</li>

                <li>✓ Mobili versija</li>

            </ul>

            <a
                href="#contact"
                class="package-btn"
            >
                Gauti pasiūlymą
            </a>

        </div>

        <!-- BUSINESS -->

        <div
            class="
            package-card
            featured
            "
        >

            <div class="popular">

                POPULIARIAUSIAS

            </div>

            <span class="package-tag">

                Business

            </span>

            <h3>

                Augančiam verslui

            </h3>

            <p class="package-desc">

                Daugiau funkcijų,
                daugiau automatizacijos
                ir geresni pardavimų rezultatai.

            </p>

            <ul>

                <li>✓ Neribotas produktų kiekis</li>

                <li>✓ Filtrai</li>

                <li>✓ Kuponai</li>

                <li>✓ Akcijos</li>

                <li>✓ Analytics</li>

                <li>✓ Facebook Pixel</li>

                <li>✓ SEO optimizacija</li>

                <li>✓ Greičio optimizacija</li>

                <li>✓ El. pašto automatizacija</li>

            </ul>

            <a
                href="#contact"
                class="package-btn featured-btn"
            >
                Gauti pasiūlymą
            </a>

        </div>

        <!-- ENTERPRISE -->

        <div class="package-card">

            <span class="package-tag">

                Enterprise

            </span>

            <h3>

                Individualūs projektai

            </h3>

            <p class="package-desc">

                Didelėms įmonėms,
                sudėtingiems projektams
                ir individualiems poreikiams.

            </p>

            <ul>

                <li>✓ Individualus dizainas</li>

                <li>✓ CRM integracijos</li>

                <li>✓ ERP integracijos</li>

                <li>✓ API sprendimai</li>

                <li>✓ Automatizacijos</li>

                <li>✓ Serverio optimizacija</li>

                <li>✓ Neribotas funkcionalumas</li>

                <li>✓ Prioritetinė pagalba</li>

            </ul>

            <a
                href="#contact"
                class="package-btn"
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

    color:#22d3ee;

    border:1px solid rgba(34,211,238,.2);

    font-weight:600;

}

.section-title{

    font-size:56px;

    font-weight:900;

    margin-top:32px;

}

.section-title span{

    color:#22d3ee;

}

.section-text{

    max-width:760px;

    margin:28px auto 0;

    color:#94a3b8;

    line-height:1.9;

    font-size:20px;

}

.package-card{

    position:relative;

    padding:42px;

    border-radius:30px;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    transition:.35s;

}

.package-card:hover{

    transform:translateY(-10px);

    border-color:rgba(34,211,238,.25);

}

.featured{

    border-color:#22d3ee;

    background:rgba(34,211,238,.05);

    box-shadow:0 0 60px rgba(34,211,238,.15);

}

.popular{

    position:absolute;

    top:-14px;

    left:50%;

    transform:translateX(-50%);

    background:#22d3ee;

    color:black;

    font-size:12px;

    font-weight:700;

    padding:8px 18px;

    border-radius:999px;

}

.package-tag{

    color:#22d3ee;

    font-weight:700;

    text-transform:uppercase;

    letter-spacing:1px;

}

.package-card h3{

    font-size:34px;

    font-weight:800;

    margin-top:18px;

}

.package-desc{

    color:#94a3b8;

    margin:22px 0 34px;

    line-height:1.8;

}

.package-card ul{

    display:flex;

    flex-direction:column;

    gap:16px;

    color:#e2e8f0;

}

.package-btn{

    display:flex;

    justify-content:center;

    align-items:center;

    margin-top:40px;

    padding:16px;

    border-radius:18px;

    border:1px solid rgba(255,255,255,.1);

    transition:.3s;

}

.package-btn:hover{

    border-color:#22d3ee;

}

.featured-btn{

    background:#22d3ee;

    color:black;

    border:none;

    font-weight:700;

}

.featured-btn:hover{

    transform:scale(1.03);

}

</style>