<script setup>
import { useReveal } from '@/composables/useReveal'

useReveal('.about-card')
</script>

<template>
    <section
        id="about"
        class="relative py-24 overflow-hidden"
    >
        <div class="contact-glow"></div>
        <div class="max-w-7xl mx-auto px-6">

            <div class="mb-20 text-center">
                <span
                    class="text-cyan-400 uppercase tracking-[0.3em] font-semibold mb-4 "
                >
                    03
                </span>

                <h2
                    class="text-4xl md:text-6xl font-bold mt-4"
                >
                    Kodėl verta 
                    <span class="text-cyan-400">
                        dirbti su manimi
                    </span>
                </h2>
                <p
                    class="mt-6 text-slate-400 max-w-2xl mx-auto"
                >
                    Siekiu ne tik sukurti svetainę ar sistemą, bet ir pasiūlyti sprendimą,
                     kuris padėtų jūsų verslui augti ilgalaikėje perspektyvoje.
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 lg:gap-8">

                <div class="about-card">
                    <div class="icon">✓</div>
                    <h3>Individualus požiūris</h3>
                    <p>
                        Sprendimai kuriami pagal jūsų verslo tikslus,
                        o ne pagal iš anksto paruoštą šabloną.
                    </p>
                </div>

                <div class="about-card">
                    <div class="icon">✓</div>
                    <h3>Greitas įgyvendinimas</h3>
                    <p>
                    Aiškus procesas ir konkretūs terminai leidžia greičiau pasiekti rezultatą.
                    </p>
                </div>

                <div class="about-card">
                    <div class="icon">✓</div>
                    <h3>Patikimas bendravimas</h3>
                    <p>
                        Viso projekto metu žinosite darbų eigą
                        ir galėsite lengvai susisiekti kilus klausimams.
                    </p>
                </div>

                <div class="about-card">
                    <div class="icon">✓</div>
                    <h3>Pagalba po paleidimo</h3>
                    <p>
                        Pasibaigus projektui neliekate vieni – suteikiu pagalbą,
                        priežiūrą ir tolimesnius patobulinimus.
                    </p>
                </div>

            </div>

        </div>
    </section>
</template>

<style scoped>
.about-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);

    border-radius: 24px;

    padding: 28px;

    border-color: rgba(34,211,238,.4);

    box-shadow:
        0 0 40px rgba(34,211,238,.12);
}

.icon {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: rgba(34,211,238,.15);

    color: #22D3EE;
    font-size: 22px;
    font-weight: bold;

    margin-bottom: 20px;
}

.about-card h3 {
    font-size: 24px;
    font-weight: 700;

    margin-bottom: 12px;
}

.about-card p {
    color: #94A3B8;
    line-height: 1.7;
}
@media (max-width: 767px) {
    .about-card {
        padding: 22px;
        border-radius: 20px;
    }

    .about-card:hover {
        transform: none;
        box-shadow: none;
    }

    .icon {
        width: 42px;
        height: 42px;
        margin-bottom: 16px;
    }

    .about-card h3 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .about-card p {
        line-height: 1.65;
    }
}</style>