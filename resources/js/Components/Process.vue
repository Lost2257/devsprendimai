<script setup>
import { computed } from 'vue'
import { useLocale } from '@/composables/useLocale'
const { isEnglish } = useLocale()
const steps = computed(() => isEnglish.value ? [
    ['Conversation', 'We discuss your idea, needs and business goals.'],
    ['Plan', 'I prepare the scope, timeline and delivery plan.'],
    ['Delivery', 'I build, test and refine every agreed feature.'],
    ['Launch', 'The project goes live and remains supported when needed.'],
] : [
    ['Pokalbis', 'Aptariame jūsų idėją, poreikius ir tikslus.'],
    ['Planas', 'Paruošiu pasiūlymą, terminus ir darbų eigą.'],
    ['Įgyvendinimas', 'Kuriu, testuoju ir derinu visas funkcijas.'],
    ['Paleidimas', 'Projektas paleidžiamas ir prireikus prižiūrimas.'],
])
</script>

<template>
    <section
        id="process"
        class="py-24 relative overflow-hidden"
    >
        <div class="site-container">

            <!-- Header -->
            <div class="mb-20 text-center">
                <span
                    class="text-cyan-400 uppercase tracking-[0.3em] font-semibold"
                >
                    04
                </span>

                <h2
                    class="text-4xl md:text-6xl font-bold mt-4"
                >
                    {{ isEnglish ? 'How we' : 'Kaip vyksta' }}
                    <span class="text-cyan-400">
                        {{ isEnglish ? 'work together' : 'bendradarbiavimas' }}
                    </span>
                </h2>

                <p
                    class="mt-6 text-slate-400 max-w-2xl mx-auto"
                >
                    {{ isEnglish ? 'A clear process from the first conversation to a successful launch.' : 'Aiškus procesas nuo pirmo pokalbio iki sėkmingai paleisto projekto.' }}
                </p>
            </div>

            <!-- Steps -->
            <div class="process-wrapper">

                <div class="process-line"></div>

                <div
                    class="
                    grid
                    md:grid-cols-2
                    xl:grid-cols-4
                    gap-6
                    relative
                    z-10
                    "
                >

                <div v-for="(step, index) in steps" :key="step[0]" class="step-card">
                    <span class="step-number">{{ String(index + 1).padStart(2, '0') }}</span>
                    <h3>{{ step[0] }}</h3>
                    <p>{{ step[1] }}</p>
                </div>

            </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.process-wrapper {
    position: relative;
}

.process-line {
    position: absolute;

    left: 0;
    right: 0;

    top: 50%;

    height: 2px;

    transform: translateY(-50%);

    background: linear-gradient(
        90deg,
        transparent,
        rgba(34,211,238,.8),
        rgba(99,102,241,.8),
        transparent
    );

    box-shadow:
        0 0 20px rgba(34,211,238,.5),
        0 0 40px rgba(34,211,238,.25);

    z-index: 1;
}

.step-card {
    position: relative;

    padding: 40px 30px;

    border-radius: 24px;

    background: rgba(255,255,255,.03);

    border: 1px solid rgba(255,255,255,.08);

    backdrop-filter: blur(15px);

    transition: all .3s ease;
}

.step-card:hover {
    transform: translateY(-8px);

    border-color: rgba(34,211,238,.3);

    box-shadow:
        0 0 30px rgba(34,211,238,.08);
}

.step-number {
    position: relative;

    display: inline-block;

    font-size: 14px;
    font-weight: 700;

    color: #22D3EE;

    margin-bottom: 20px;

    letter-spacing: .2em;
}

.step-number::before {
    content: '';

    position: absolute;

    width: 12px;
    height: 12px;

    border-radius: 999px;

    background: #22D3EE;

    box-shadow:
        0 0 10px #22D3EE,
        0 0 20px #22D3EE;

    top: -25px;
    left: 0;
}

.step-card h3 {
    font-size: 28px;
    font-weight: 700;

    margin-bottom: 15px;
}

.step-card p {
    color: #94A3B8;
    line-height: 1.7;
}

@media (max-width: 1279px) {
    .process-line,
    .step-number::before {
        display: none;
    }
}

@media (max-width: 767px) {
    section {
        padding-block: 4.5rem;
    }

    .step-card {
        padding: 28px 24px;
    }

    .step-card:hover {
        transform: none;
    }
}
</style>
