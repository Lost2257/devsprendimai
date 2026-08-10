<template>

<section class="py-28">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

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

            text-sm
            font-semibold
            "
        >
            Kodėl verta rinktis mus?
        </span>

        <h2
            class="
            mt-8

            text-5xl

            font-black
            "
        >
            El. parduotuvė,
            <span class="text-cyan-400">
                orientuota į pardavimus.
            </span>
        </h2>

        <p
            class="
            mt-8

            max-w-3xl

            mx-auto

            text-xl

            text-slate-400

            leading-9
            "
        >
            Kuriame ne tik gražias parduotuves – kuriame sistemas,
            kurios didina konversijas, automatizuoja procesus ir leidžia
            auginti verslą be papildomo galvos skausmo.
        </p>

    </div>

    <div
        class="
        grid

        md:grid-cols-2
        xl:grid-cols-4

        gap-8
        "
    >

        <!-- 1 -->

        <div class="benefit-card">

            <div class="icon">
                💳
            </div>

            <h3>
                Mokėjimų integracijos
            </h3>

            <p>

                Integruojame Paysera, Montonio,
                Stripe, PayPal ir kitus mokėjimų
                sprendimus.

            </p>

        </div>

        <!-- 2 -->

        <div class="benefit-card">

            <div class="icon">
                📦
            </div>

            <h3>
                Pristatymo sistemos
            </h3>

            <p>

                Omniva, DPD, LP Express,
                Venipak ir kitos kurjerių
                sistemos vienoje vietoje.

            </p>

        </div>

        <!-- 3 -->

        <div class="benefit-card">

            <div class="icon">
                🚀
            </div>

            <h3>
                Greitis ir SEO
            </h3>

            <p>

                Greitai veikiančios el.
                parduotuvės, optimizuotos
                paieškos sistemoms.

            </p>

        </div>

        <!-- 4 -->

        <div class="benefit-card">

            <div class="icon">
                📈
            </div>

            <h3>
                Pardavimų augimas
            </h3>

            <p>

                Analytics, Pixel,
                konversijų sekimas,
                akcijos ir kuponai.

            </p>

        </div>

    </div>

</div>

</section>

</template>

<style scoped>

.benefit-card{

    padding:40px;

    border-radius:28px;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    transition:.35s;

    backdrop-filter:blur(16px);

}

.benefit-card:hover{

    transform:translateY(-8px);

    border-color:rgba(34,211,238,.35);

    box-shadow:0 25px 60px rgba(34,211,238,.12);

}

.icon{

    width:72px;
    height:72px;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:34px;

    border-radius:22px;

    background:rgba(34,211,238,.08);

    margin-bottom:28px;

}

h3{

    font-size:26px;

    font-weight:800;

    margin-bottom:18px;

}

p{

    color:#94a3b8;

    line-height:1.9;

}

</style>