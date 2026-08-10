<script setup>
import { ref, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ProjectForm from '@/Components/Dashboard/ProjectForm.vue'

const slugEdited = ref(false)
const lastGeneratedSlug = ref('')
const form = useForm({
    title: '', subtitle: '', slug: '', client: '', industry: '', year: new Date().getFullYear().toString(), duration: '', url: '',
    description: '', services: '', technologies: '', accent_color: '#22D3EE', challenge: '', solution: '', result: '',
    title_en: '', subtitle_en: '', industry_en: '', duration_en: '', description_en: '', services_en: '', challenge_en: '', solution_en: '', result_en: '',
    metric_one_value: '', metric_one_label: '', metric_two_value: '', metric_two_label: '', metric_three_value: '', metric_three_label: '',
    thumbnail: null, mobile_thumbnail: null, gallery: [], featured: false,
})

watch(() => form.title, (title) => {
    if (slugEdited.value) return
    const generatedSlug = title.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
    lastGeneratedSlug.value = generatedSlug
    form.slug = generatedSlug
})

watch(() => form.slug, (slug) => {
    if (slug && slug !== lastGeneratedSlug.value) slugEdited.value = true
})

const submit = () => form.post('/dashboard/projects', { forceFormData: true })
</script>

<template>
    <AdminLayout>
        <div class="page-head">
            <div><span>Turinio studija</span><h1>Naujas projektas</h1><p>Sukurkite pilną interaktyvią projekto istoriją.</p></div>
            <Link href="/dashboard/projects">← Visi projektai</Link>
        </div>
        <ProjectForm :form="form" submit-label="Publikuoti projektą" @submit="submit" />
    </AdminLayout>
</template>

<style scoped>
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 1.6rem; }
.page-head span { color: #22d3ee; font-size: .68rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
.page-head h1 { margin-top: .45rem; font-size: clamp(2rem,4vw,3.2rem); font-weight: 850; letter-spacing: -.05em; }
.page-head p { margin-top: .35rem; color: #718096; }
.page-head > a { color: #91a0b4; font-size: .8rem; font-weight: 750; }
@media(max-width:640px){.page-head{align-items:flex-start;flex-direction:column-reverse}.page-head> a{margin-bottom:.5rem}}
</style>
