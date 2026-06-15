<script setup>
import { useReveal } from '@/composables/useReveal'

useReveal('.service-card')
</script>

<template>
    <section
        id="services"
        class="relative py-24 overflow-hidden"
    >
        <div class="contact-glow"></div>
        <div class="max-w-7xl mx-auto px-6">

            <div class="mb-20 text-center">
                <span
                    class="text-cyan-400 uppercase tracking-[0.3em] font-semibold"
                >
                    01
                </span>

                <h2
                    class="text-4xl md:text-6xl font-bold mt-4"
                >
                
                    Paslaugos
                </h2>
                <p
                    class="mt-6 text-slate-400 max-w-2xl mx-auto"
                >
                    Sprendimai, padedantys verslui augti internete
                </p>
            </div>

            <div
                class="grid md:grid-cols-2 xl:grid-cols-4 gap-5 md:gap-6"
            >
                <div class="service-card">
                    <h3>Interneto svetainės</h3>

                    <p>
                        Modernios, greitos ir mobiliesiems pritaikytos svetainės,
                        kurios reprezentuoja jūsų verslą ir padeda pritraukti naujų klientų.
                    </p>
                </div>

                <div class="service-card">
                    <h3>El. parduotuvės</h3>

                    <p>
                        WooCommerce ir individualūs e. komercijos sprendimai,
                        leidžiantys patogiai parduoti produktus bei valdyti užsakymus.
                    </p>
                </div>

                <div class="service-card">
                    <h3>Automatizacija</h3>

                    <p>
                        Procesų automatizavimas, API integracijos ir sprendimai,
                        kurie taupo laiką bei mažina rankinį darbą.
                    </p>
                </div>

                <div class="service-card">
                    <h3>Svetainių priežiūra</h3>

                    <p>
                        Nuolatiniai atnaujinimai, saugumo priežiūra,
                        atsarginės kopijos ir techninė pagalba jūsų svetainei.
                    </p>
                </div>
            </div>

        </div>
    </section>
</template>

<style scoped>
.service-card {
    background: rgba(255,255,255,.03);

    border: 1px solid rgba(255,255,255,.08);

    border-radius: 24px;

    padding: 32px;

    transition: .3s;
}

.service-card:hover {
    transform: translateY(-8px);

    border-color: rgba(34,211,238,.4);

    box-shadow:
        0 0 40px rgba(34,211,238,.12);
}

.service-card h3 {
    font-size: 24px;
    font-weight: 700;

    margin-bottom: 16px;
}

.service-card p {
    color: #94A3B8;
    line-height: 1.7;
}

@media (max-width: 767px) {
    .service-card {
        padding: 24px;
        border-radius: 20px;
    }

    .service-card:hover {
        transform: none;
        box-shadow: none;
    }

    .service-card h3 {
        font-size: 22px;
        margin-bottom: 12px;
    }

    .service-card p {
        line-height: 1.6;
    }
}
</style>