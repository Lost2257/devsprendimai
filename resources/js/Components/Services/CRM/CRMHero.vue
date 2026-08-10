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
        items-center

        px-4
        py-2

        rounded-full

        bg-cyan-400/10
        border
        border-cyan-400/20

        text-cyan-400
        text-sm
        font-semibold
        "
    >
        Individualios CRM sistemos
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

        CRM sistema,

        <span
            class="
            block
            text-cyan-400
            "
        >
            kuri dirba už jus.
        </span>

    </h1>

    <p
        class="
        mt-8

        text-xl

        text-slate-400

        leading-9

        max-w-xl
        "
    >

        Kuriame individualias CRM sistemas,
        kurios automatizuoja kasdienius procesus,
        leidžia valdyti klientus, projektus,
        užduotis ir visą verslą vienoje vietoje.

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
            href="#packages"
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
            Sprendimai
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
            👥 Klientai
        </div>

        <div class="feature">
            📋 Projektai
        </div>

        <div class="feature">
            📅 Užduotys
        </div>

        <div class="feature">
            🤖 Automatizacija
        </div>

        <div class="feature">
            📈 Ataskaitos
        </div>

        <div class="feature">
            🔗 API Integracijos
        </div>

    </div>

</div>

<!-- RIGHT -->

<div
    class="
    relative

    flex

    justify-center

    items-center
    "
>

    <img
        src="/images/crm-desktop.png"
        class="
        w-full
        max-w-3xl

        drop-shadow-[0_35px_80px_rgba(0,255,255,.15)]
        "
    >

    <img
        src="/images/crm-mobile.png"
        class="
        absolute

        -bottom-8
        right-0

        w-44

        rounded-[2rem]

        border
        border-white/10

        shadow-2xl

        animate-float
        "
    >

</div>

</div>

</div>

</section>

</template>

<style scoped>

.feature{

    display:flex;

    align-items:center;

    justify-content:center;

    padding:14px;

    border-radius:18px;

    border:1px solid rgba(255,255,255,.08);

    background:rgba(255,255,255,.03);

    color:#cbd5e1;

    font-weight:600;

    backdrop-filter:blur(10px);

}

@keyframes float{

    0%,100%{

        transform:translateY(0);

    }

    50%{

        transform:translateY(-12px);

    }

}

.animate-float{

    animation:float 5s ease-in-out infinite;

}

</style>