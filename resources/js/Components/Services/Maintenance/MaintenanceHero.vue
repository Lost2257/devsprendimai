<template>

<section
    class="
    relative
    overflow-hidden

    pt-36
    pb-24
    "
>

<div class="max-w-7xl mx-auto px-6">

<div
    class="
    grid
    lg:grid-cols-2
    gap-20
    items-center
    "
>

<!-- LEFT -->

<div>

    <span
        class="
        inline-flex

        px-4
        py-2

        rounded-full

        bg-cyan-400/10
        border
        border-cyan-400/20

        text-cyan-400
        font-semibold
        "
    >
        Svetainių priežiūra
    </span>

    <h1
        class="
        mt-8

        text-5xl
        md:text-7xl

        font-black

        leading-tight
        "
    >

        Rūpinamės

        <span class="block text-cyan-400">

            jūsų svetaine.

        </span>

    </h1>

    <p
        class="
        mt-8

        text-xl

        leading-9

        text-slate-400

        max-w-xl
        "
    >

        Nuolat prižiūrime jūsų interneto svetainę –
        diegiame atnaujinimus, darome atsargines kopijas,
        stebime veikimą ir greitai sprendžiame techninius
        nesklandumus.

    </p>

    <div
        class="
        flex
        flex-wrap
        gap-4
        mt-10
        "
    >

        <a
            href="#contact"
            class="
            px-8
            py-4

            rounded-2xl

            bg-cyan-400
            text-black

            font-bold

            hover:scale-105

            transition
            "
        >

            Gauti pasiūlymą

        </a>

        <a
            href="#plans"
            class="
            px-8
            py-4

            rounded-2xl

            border
            border-white/10

            hover:border-cyan-400/30

            transition
            "
        >

            Priežiūros planai

        </a>

    </div>

    <div
        class="
        grid

        grid-cols-2
        md:grid-cols-3

        gap-4

        mt-14
        "
    >

        <div class="feature">

            🛡️ Saugumas

        </div>

        <div class="feature">

            💾 Backup

        </div>

        <div class="feature">

            ⚡ Greitis

        </div>

        <div class="feature">

            🔄 Atnaujinimai

        </div>

        <div class="feature">

            📈 Monitoringas

        </div>

        <div class="feature">

            💬 Pagalba

        </div>

    </div>

</div>

<!-- RIGHT -->

<div class="relative flex justify-center">

    <div class="maintenance-card">

        <div class="status">

            <span class="dot"></span>

            Svetainė veikia

        </div>

        <div class="check">

            ✓ SSL sertifikatas aktyvus

        </div>

        <div class="check">

            ✓ Backup atliktas šiandien

        </div>

        <div class="check">

            ✓ WordPress atnaujintas

        </div>

        <div class="check">

            ✓ Įskiepiai atnaujinti

        </div>

        <div class="check">

            ✓ 99.9% veikimo laikas

        </div>

        <div class="check">

            ✓ Saugumo patikra atlikta

        </div>

    </div>

</div>

</div>

</div>

</section>

</template>

<style scoped>

.feature{

display:flex;
justify-content:center;
align-items:center;

padding:14px;

border-radius:18px;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

font-weight:600;

color:#cbd5e1;

transition:.3s;

}

.feature:hover{

transform:translateY(-4px);

border-color:rgba(34,211,238,.35);

}

.maintenance-card{

width:100%;
max-width:480px;

padding:40px;

border-radius:30px;

background:rgba(255,255,255,.04);

border:1px solid rgba(34,211,238,.15);

backdrop-filter:blur(20px);

box-shadow:0 30px 80px rgba(34,211,238,.12);

}

.status{

display:flex;

align-items:center;

gap:12px;

margin-bottom:30px;

font-size:22px;

font-weight:700;

}

.dot{

width:14px;
height:14px;

border-radius:999px;

background:#22d3ee;

box-shadow:0 0 18px #22d3ee;

}

.check{

padding:18px 22px;

margin-top:16px;

border-radius:18px;

background:rgba(34,211,238,.06);

border:1px solid rgba(34,211,238,.12);

color:#cbd5e1;

font-size:17px;

}

</style>