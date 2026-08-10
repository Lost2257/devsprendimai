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

        <h1
            class="
            text-3xl
            font-bold
            mb-8
            "
        >
            SEO nustatymai
        </h1>

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
                >

            </div>

            <div>

                <label class="label">
                    Meta description
                </label>

                <textarea
                    v-model="form.site_description"
                    rows="5"
                    class="input"
                ></textarea>

            </div>

            <div>

                <label class="label">
                    SEO raktažodžiai
                </label>

                <textarea
                    v-model="form.site_keywords"
                    rows="4"
                    class="input"
                    placeholder="interneto svetainių kūrimas, laravel programuotojas, vue.js..."
                ></textarea>

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
                Išsaugoti
            </button>

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
</style>