<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    settings: Object
})

const form = useForm({
    site_title: props.settings?.site_title || '',
    site_description: props.settings?.site_description || '',
    site_keywords: props.settings?.site_keywords || '',
    google_site_verification: props.settings?.google_site_verification || '',
    bing_site_verification: props.settings?.bing_site_verification || '',
    og_image: null
})

const submit = () => {

    form.transform(data => ({
        ...data,
        _method: 'put'
    }))
    .post('/dashboard/settings', {
        forceFormData: true
    })
}
</script>

<template>

<AdminLayout>

    <div class="max-w-4xl">

        <span class="eyebrow">Paieškos sistemų valdymas</span>
        <h1
            class="
            text-3xl
            font-bold
            mb-8
            "
        >
            SEO nustatymai
        </h1>

        <p class="intro">Pagrindiniai duomenys, kuriuos Google, Bing ir socialiniai tinklai naudoja jūsų svetainei suprasti ir pateikti.</p>

        <div class="search-tools"><a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer">Atidaryti Google Search Console ↗</a><a href="/sitemap.xml" target="_blank">Patikrinti sitemap.xml ↗</a></div>

        <form
            @submit.prevent="submit"
            class="
            bg-white/5
            border
            border-white/10
            rounded-3xl
            p-8
            space-y-6
            "
        >

            <div>

                <label class="label">
                    Svetainės pavadinimas
                </label>

                <input
                    v-model="form.site_title"
                    class="input"
                    maxlength="70"
                >
                <small>{{ form.site_title.length }}/70 · rekomenduojama 45–65 ženklai</small>

            </div>

            <div>

                <label class="label">
                    Meta description
                </label>

                <textarea
                    v-model="form.site_description"
                    rows="5"
                    class="input"
                    maxlength="170"
                ></textarea>
                <small>{{ form.site_description.length }}/170 · rekomenduojama 120–160 ženklų</small>

            </div>

            <div>

                <label class="label">
                    Teminės frazės turinio planavimui
                </label>

                <textarea
                    v-model="form.site_keywords"
                    rows="4"
                    class="input"
                    placeholder="interneto svetainių kūrimas, laravel programuotojas, vue.js..."
                ></textarea>
                <small>Google nenaudoja „meta keywords“. Šis laukas paliekamas tik jūsų vidiniam turinio planui.</small>

            </div>

            <div class="verification-grid">
                <div>
                    <label class="label">Google Search Console kodas</label>
                    <input v-model="form.google_site_verification" class="input" placeholder="Tik content reikšmė iš meta žymos">
                    <small>Įklijuokite tik kodą, ne visą &lt;meta&gt; žymą.</small>
                </div>
                <div>
                    <label class="label">Bing Webmaster Tools kodas</label>
                    <input v-model="form.bing_site_verification" class="input" placeholder="Tik patvirtinimo kodas">
                    <small>Naudojamas msvalidate.01 meta žymoje.</small>
                </div>
            </div>

            <div>

                <label class="label">
                    OG nuotrauka
                </label>

                <input
                    type="file"
                    @input="
                        form.og_image =
                        $event.target.files[0]
                    "
                    class="input"
                >

                <img v-if="settings?.og_image" :src="`/storage/${settings.og_image}`" alt="Dabartinis socialinis vaizdas" class="og-preview">
                <small>Rekomenduojamas formatas 1200 × 630 px, iki 5 MB.</small>

            </div>

            <button
                type="submit"
                class="
                px-6
                py-3
                rounded-xl
                bg-cyan-400
                text-black
                font-semibold
                "
            >
                {{ form.processing ? 'Saugoma…' : 'Išsaugoti SEO' }}
            </button>

            <p v-if="form.hasErrors" class="error">Patikrinkite pažymėtus laukus ir bandykite dar kartą.</p>

        </form>

    </div>

</AdminLayout>

</template>

<style scoped>
.input{
    width:100%;
    padding:14px 16px;

    background:rgba(255,255,255,.04);

    border:1px solid rgba(255,255,255,.08);

    border-radius:12px;

    color:white;
}

.label{
    display:block;
    margin-bottom:8px;

    color:#94A3B8;
}
.eyebrow{color:#22d3ee;font-size:.68rem;font-weight:800;letter-spacing:.17em;text-transform:uppercase}.intro{max-width:680px;margin:-1.25rem 0 1rem;color:#718096;line-height:1.7}.search-tools{display:flex;flex-wrap:wrap;gap:.55rem;margin-bottom:2rem}.search-tools a{padding:.65rem .8rem;border:1px solid rgba(34,211,238,.16);border-radius:.65rem;color:#67e8f9;background:rgba(34,211,238,.045);font-size:.68rem;font-weight:800}.verification-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.input+small,.verification-grid small,.og-preview+small{display:block;margin-top:.45rem;color:#64748b;font-size:.65rem;line-height:1.5}.og-preview{width:100%;max-width:420px;aspect-ratio:1200/630;margin-top:1rem;border:1px solid rgba(255,255,255,.09);border-radius:1rem;object-fit:cover}.error{color:#fca5a5;font-size:.72rem}@media(max-width:700px){.verification-grid{grid-template-columns:1fr}}
</style>
