<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ProjectForm from '@/Components/Dashboard/ProjectForm.vue'

const props = defineProps({ project: Object })
const value = (field, fallback = '') => props.project[field] ?? fallback

const form = useForm({
    title: value('title'), subtitle: value('subtitle'), slug: value('slug'), client: value('client'), industry: value('industry'), year: value('year'), duration: value('duration'), url: value('url'),
    description: value('description'), services: value('services'), technologies: value('technologies'), accent_color: value('accent_color', '#22D3EE'), challenge: value('challenge'), solution: value('solution'), result: value('result'),
    title_en: value('title_en'), subtitle_en: value('subtitle_en'), industry_en: value('industry_en'), duration_en: value('duration_en'), description_en: value('description_en'), services_en: value('services_en'), challenge_en: value('challenge_en'), solution_en: value('solution_en'), result_en: value('result_en'),
    metric_one_value: value('metric_one_value'), metric_one_label: value('metric_one_label'), metric_two_value: value('metric_two_value'), metric_two_label: value('metric_two_label'), metric_three_value: value('metric_three_value'), metric_three_label: value('metric_three_label'),
    thumbnail: null, mobile_thumbnail: null, gallery: [], remove_thumbnail: false, remove_mobile_thumbnail: false, remove_gallery: [], featured: !!props.project.featured,
})

const submit = () => form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/projects/${props.project.id}`, { forceFormData: true })
</script>

<template>
    <AdminLayout>
        <div class="page-head">
            <div><span>Projekto redagavimas</span><h1>{{ project.title }}</h1><p>Atnaujinkite istoriją, rezultatus ir 3D galeriją.</p></div>
            <div class="head-actions"><a :href="`/projektai/${project.slug}`" target="_blank">Peržiūrėti gyvai ↗</a><Link href="/dashboard/projects">← Visi projektai</Link></div>
        </div>
        <ProjectForm :form="form" :project="project" submit-label="Išsaugoti pakeitimus" @submit="submit" />
    </AdminLayout>
</template>

<style scoped>
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 1.6rem; }
.page-head span { color: #22d3ee; font-size: .68rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
.page-head h1 { margin-top: .45rem; font-size: clamp(2rem,4vw,3.2rem); font-weight: 850; letter-spacing: -.05em; }
.page-head p { margin-top: .35rem; color: #718096; }
.head-actions { display: flex; gap: .75rem; }
.head-actions a { padding: .65rem .8rem; border: 1px solid rgba(255,255,255,.09); border-radius: .7rem; color: #a8b4c5; font-size: .74rem; font-weight: 750; }
@media(max-width:700px){.page-head{align-items:flex-start;flex-direction:column}.head-actions{flex-wrap:wrap}}
</style>
