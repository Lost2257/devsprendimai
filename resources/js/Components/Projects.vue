<script setup>
import { useReveal } from '@/composables/useReveal'

useReveal('.project-card')
</script>

<template>
    <section
        id="projects"
        class="py-24 md:py-32"
    >
        <div class="max-w-7xl mx-auto px-6">

            <div class="mb-20 text-center">
                <span
                    class="text-cyan-400 uppercase tracking-[0.3em] font-semibold"
                >
                    02
                </span>

                <h2
                    class="text-4xl md:text-6xl font-bold mt-4"
                >
                    Naujausi
                <span class="text-cyan-400">
                    projektai
                </span>
                    
                </h2>
            </div>

            <div class="space-y-6 sm:space-y-8">

                <a
                    href="https://autoritmu.lt"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="project-card"
                >
                    <div>
                        <p class="project-type">
                            Renginių platforma,
                            Administravimo sistema,
                            Mokėjimų integracijos
                        </p>

                        <h3 class="project-title">
                            Autoritmu.lt
                        </h3>

                        <p class="project-desc">
                            Sukurta specializuota renginių platforma, leidžianti organizatoriams talpinti renginius,
                            o lankytojams greitai atrasti aktualius auto, moto ir dviračių renginius Lietuvoje.
                        </p>
                    </div>

                    <span class="project-arrow">
                        →
                    </span>
                </a>

                <a
                    href="https://e-akademija.lt"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="project-card"
                >
                    <div>
                        <p class="project-type">
                            Verslo mokymų ir bendruomenės platforma
                        </p>

                        <h3 class="project-title">
                            E-akademija.lt
                        </h3>

                        <p class="project-desc">
                            Verslo mokymų ir bendruomenės platforma su renginių registracijomis, narių zona ir turinio valdymo sistema.
                            Sukurtas funkcionalus ir lengvai administruojamas sprendimas verslo augimui.
                        </p>
                    </div>

                    <span class="project-arrow">
                        →
                    </span>
                </a>
            </div>

        </div>
    </section>
</template>

<style scoped>
.project-card {
    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 40px;

    border-radius: 28px;

    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.08);

    transition: .3s;
}

.project-card:hover {
    transform: translateY(-5px);

    border-color: rgba(34,211,238,.4);

    box-shadow:
        0 0 40px rgba(34,211,238,.12);
}

.project-type {
    color: #22D3EE;
    margin-bottom: 10px;
}

.project-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 12px;
}

.project-desc {
    color: #94A3B8;
    max-width: 700px;
}

.project-arrow {
    font-size: 42px;
    color: #22D3EE;
}
@media (max-width: 767px) {
    .project-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 18px;
        padding: 28px;
    }

    .project-card:hover {
        transform: none;
        box-shadow: none;
    }

    .project-type {
        margin-bottom: 8px;
    }

    .project-title {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .project-desc {
        max-width: none;
        line-height: 1.65;
    }

    .project-arrow {
        font-size: 32px;
        align-self: flex-end;
    }
}</style>