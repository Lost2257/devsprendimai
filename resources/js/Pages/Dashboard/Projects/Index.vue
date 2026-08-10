<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({ projects: { type: Array, default: () => [] } })

const destroyProject = (project) => {
    if (confirm(`Ištrinti projektą „${project.title}“? Šio veiksmo atšaukti nepavyks.`)) {
        router.delete(`/dashboard/projects/${project.id}`, { preserveScroll: true })
    }
}
</script>

<template>
    <AdminLayout>
        <header class="page-head">
            <div><span>Portfolio studija</span><h1>Projektai</h1><p>{{ projects.length }} {{ projects.length === 1 ? 'projektas' : 'projektai' }} · valdykite viešas 3D istorijas.</p></div>
            <Link href="/dashboard/projects/create">Naujas projektas <i>↗</i></Link>
        </header>

        <section v-if="projects.length" class="project-grid">
            <article v-for="(project, index) in projects" :key="project.id" class="project-card">
                <Link class="project-visual" :href="`/projektai/${project.slug}`" target="_blank">
                    <img v-if="project.thumbnail" :src="`/storage/${project.thumbnail}`" :alt="project.title">
                    <div v-else class="visual-placeholder"><b>DS</b><span>Įkelkite projekto viršelį</span></div>
                    <div class="visual-shade"></div>
                    <span class="index">{{ String(index + 1).padStart(2, '0') }}</span>
                    <span v-if="project.featured" class="featured">Pagrindinis</span>
                    <span class="open-label">Atidaryti istoriją ↗</span>
                </Link>
                <div class="project-info">
                    <div class="project-title">
                        <div><span>{{ project.industry || project.client || 'Projektas' }}</span><h2>{{ project.title }}</h2></div>
                        <i :style="{ background: project.accent_color || '#22D3EE' }"></i>
                    </div>
                    <p>{{ project.subtitle || project.description || 'Pridėkite trumpą projekto pristatymą.' }}</p>
                    <div class="project-meta"><span>{{ project.year || '—' }}</span><span>{{ project.services || 'Paslaugos nenurodytos' }}</span></div>
                    <div class="actions">
                        <Link :href="`/dashboard/projects/${project.id}/edit`">Redaguoti <b>→</b></Link>
                        <button type="button" @click="destroyProject(project)">Ištrinti</button>
                    </div>
                </div>
            </article>
        </section>

        <section v-else class="empty-state">
            <div>DS<span>+</span></div><span>Portfolio tuščias</span><h2>Sukurkite pirmą projekto istoriją.</h2>
            <p>Įkelkite vizualus, iššūkį, sprendimą ir rezultatus — viešą 3D puslapį sugeneruosime automatiškai.</p>
            <Link href="/dashboard/projects/create">Kurti projektą ↗</Link>
        </section>
    </AdminLayout>
</template>

<style scoped>
.page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:2rem;margin-bottom:2rem}.page-head>div>span,.empty-state>span{color:#22d3ee;font-size:.66rem;font-weight:850;letter-spacing:.17em;text-transform:uppercase}.page-head h1{margin-top:.45rem;font-size:clamp(2.6rem,5vw,4.8rem);font-weight:850;letter-spacing:-.065em;line-height:.9}.page-head p{margin-top:.7rem;color:#68778c;font-size:.78rem}.page-head>a{display:flex;align-items:center;justify-content:space-between;gap:1rem;min-width:10.5rem;min-height:3.2rem;padding:0 1rem;border-radius:.85rem;color:#061016;background:#22d3ee;font-size:.73rem;font-weight:850;box-shadow:0 15px 40px rgba(34,211,238,.14)}.page-head>a i{font-style:normal}
.project-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.project-card{overflow:hidden;border:1px solid rgba(255,255,255,.075);border-radius:1.25rem;background:rgba(255,255,255,.022);transition:border-color .25s,transform .25s}.project-card:hover{border-color:rgba(34,211,238,.2);transform:translateY(-3px)}.project-visual{position:relative;display:block;overflow:hidden;aspect-ratio:16/9;background:#0a111b}.project-visual img{width:100%;height:100%;object-fit:cover;transition:transform .7s cubic-bezier(.2,.8,.2,1)}.project-card:hover .project-visual img{transform:scale(1.035)}.visual-shade{position:absolute;inset:0;background:linear-gradient(180deg,transparent 40%,rgba(2,6,12,.82))}.visual-placeholder{display:flex;width:100%;height:100%;align-items:center;justify-content:center;flex-direction:column;gap:.45rem;background:radial-gradient(circle at 65% 35%,rgba(34,211,238,.12),transparent 35%),#09111c}.visual-placeholder b{color:#67e8f9;font-size:2rem}.visual-placeholder span{color:#526176;font-size:.65rem}.index,.featured,.open-label{position:absolute;z-index:1}.index{left:1rem;top:1rem;color:rgba(255,255,255,.48);font-size:.6rem;font-weight:850}.featured{right:1rem;top:1rem;padding:.35rem .55rem;border:1px solid rgba(103,232,249,.28);border-radius:999px;color:#a5f3fc;background:rgba(2,12,19,.65);font-size:.55rem;font-weight:800}.open-label{left:1rem;bottom:1rem;color:#fff;font-size:.67rem;font-weight:800;opacity:.8}
.project-info{padding:1.15rem}.project-title{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem}.project-title span{color:#64748b;font-size:.58rem;font-weight:750;letter-spacing:.12em;text-transform:uppercase}.project-title h2{margin-top:.28rem;font-size:1.25rem;font-weight:820;letter-spacing:-.03em}.project-title i{width:.55rem;height:.55rem;margin-top:.2rem;border-radius:50%}.project-info>p{display:-webkit-box;overflow:hidden;min-height:2.4rem;margin-top:.7rem;color:#718096;font-size:.69rem;line-height:1.65;-webkit-box-orient:vertical;-webkit-line-clamp:2}.project-meta{display:flex;gap:.5rem;margin-top:1rem}.project-meta span{overflow:hidden;padding:.35rem .5rem;border-radius:.35rem;color:#64748b;background:rgba(255,255,255,.035);font-size:.56rem;text-overflow:ellipsis;white-space:nowrap}.project-meta span:last-child{min-width:0}.actions{display:flex;align-items:center;justify-content:space-between;margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.065)}.actions a{display:flex;align-items:center;gap:1rem;color:#cbd5e1;font-size:.68rem;font-weight:800}.actions a b{color:#22d3ee}.actions button{color:#7c899b;font-size:.62rem}.actions button:hover{color:#fb7185}
.empty-state{display:grid;place-items:center;min-height:560px;padding:3rem;border:1px dashed rgba(255,255,255,.1);border-radius:1.3rem;text-align:center;background:radial-gradient(circle at 50% 40%,rgba(34,211,238,.06),transparent 30%)}.empty-state>div{position:relative;display:grid;place-items:center;width:5rem;height:5rem;margin-bottom:1rem;border:1px solid rgba(34,211,238,.18);border-radius:1.3rem;color:#67e8f9;background:rgba(34,211,238,.055);font-size:1.4rem;font-weight:900}.empty-state>div span{position:absolute;right:-.3rem;bottom:-.3rem;display:grid;place-items:center;width:1.6rem;height:1.6rem;border-radius:50%;color:#061016;background:#22d3ee;font-size:.8rem}.empty-state h2{margin-top:.65rem;font-size:clamp(1.6rem,4vw,2.6rem);letter-spacing:-.04em}.empty-state p{max-width:31rem;margin-top:.65rem;color:#6b7a90;font-size:.75rem;line-height:1.7}.empty-state>a{margin-top:1.4rem;padding:.8rem 1rem;border-radius:.65rem;color:#061016;background:#22d3ee;font-size:.7rem;font-weight:850}
@media(max-width:820px){.project-grid{grid-template-columns:1fr}}@media(max-width:640px){.page-head{align-items:flex-start;flex-direction:column}.page-head>a{width:100%}.project-card{border-radius:1rem}.empty-state{min-height:480px;padding:1.5rem}}
</style>
