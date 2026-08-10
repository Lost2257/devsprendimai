<template>
    <section
        id="contact"
        class="relative py-24 overflow-hidden"
    >
        <!-- Glow -->
        <div class="contact-glow"></div>

        <div
            class="max-w-5xl mx-auto px-6 text-center relative z-10"
        >
            <p
                class="text-cyan-400 uppercase tracking-[0.3em] mb-4"
            >
                05
            </p>

            <h2
                class="text-4xl md:text-6xl font-bold mt-4"
            >
                Paverskime
            <span class="text-cyan-400">
                 jūsų idėją
            </span>
                <br>
                veikiančiu sprendimu
            </h2>

            <p
                class="mt-6 text-slate-400 max-w-2xl mx-auto"
            >
                Kartu aptarsime projekto tikslus, galimus sprendimus
                ir tolimesnius žingsnius nuo idėjos iki rezultato.
            </p>

            <div
                class="flex flex-col md:flex-row justify-center gap-6 mt-12"
            >
                <a
                    href="mailto:info@devsprendimai.lt"
                    class="
                    px-8 py-4
                    bg-cyan-400
                    text-black
                    rounded-full
                    font-semibold
                    hover:scale-105
                    transition
                    "
                >
                    Aptarkime projektą
                </a>

                <a
                    href="tel:+37060606227"
                    class="
                    px-8 py-4
                    border
                    border-slate-700
                    rounded-full
                    hover:border-cyan-400
                    transition
                    "
                >
                    +370 60606227
                </a>
            </div>
        </div>
    </section>
</template>

<style scoped>
.contact-glow {
    position: absolute;

    left: 50%;
    top: 50%;

    width: 900px;
    height: 900px;

    transform: translate(-50%, -50%);

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(34,211,238,.15),
            rgba(79,70,229,.08),
            transparent 70%
        );

    filter: blur(80px);
}
</style>
