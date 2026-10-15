<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    stats: Object,
    mailNotificationsEnabled: { type: Boolean, default: false },
    recentProjects: { type: Array, default: () => [] },
    recentInquiries: { type: Array, default: () => [] },
    visitSeries: { type: Array, default: () => [] },
    trafficSources: { type: Array, default: () => [] },
    topPages: { type: Array, default: () => [] },
    devices: { type: Array, default: () => [] },
    campaigns: { type: Array, default: () => [] },
    seoHealth: { type: Object, default: () => ({ score: 0, checks: [] }) },
})

const maxVisits = computed(() => Math.max(...props.visitSeries.map((day) => day.count), 1))
const maxSourceVisits = computed(() => Math.max(...props.trafficSources.map((source) => Number(source.visits)), 1))
const deviceTotal = computed(() => Math.max(props.devices.reduce((total, device) => total + Number(device.visits), 0), 1))
const readinessChecks = computed(() => props.seoHealth.checks.filter((check) => !check.informational))
const completedChecks = computed(() => readinessChecks.value.filter((check) => check.ok).length)
const today = new Intl.DateTimeFormat('lt-LT', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }).format(new Date())

const statCards = computed(() => [
    { label: 'Šiandienos peržiūros', value: props.stats.today_visits, meta: 'Svetainės puslapių peržiūros', accent: true },
    { label: 'Šio mėnesio peržiūros', value: props.stats.month_visits, meta: `${props.stats.unique_visitors || 0} apytiksliai unikalūs lankytojai` },
    { label: 'Naujos užklausos', value: props.stats.new_inquiries, meta: `${props.stats.inquiries} iš viso`, accent: true },
    { label: 'Publikuoti projektai', value: props.stats.projects, meta: 'Portfolio turinys' },
])
</script>

<template>
    <AdminLayout>
        <header class="dashboard-head">
            <div><span>Valdymo apžvalga</span><h1>Labas, {{ $page.props.auth?.user?.name?.split(' ')[0] || 'Aivarai' }}.</h1><p>{{ today }} · štai kas vyksta jūsų svetainėje.</p></div>
            <Link href="/dashboard/projects/create">Naujas projektas <i>↗</i></Link>
        </header>

        <section v-if="!mailNotificationsEnabled" class="mail-warning" role="status">
            <strong>El. pašto pranešimai dar nesukonfigūruoti</strong>
            <p>Užklausos išsaugomos čia, tačiau pranešimai į jūsų paštą nesiunčiami. Kol prijungiamas pašto siuntimas, reguliariai peržiūrėkite naujas užklausas.</p>
            <Link href="/dashboard/inquiries">Peržiūrėti užklausas →</Link>
        </section>
        <p class="analytics-note">Nauji analitikos įrašai renkami tik lankytojui sutikus. Tai vidinė statistika, ne „Google Analytics“ ataskaita.</p>

        <section class="stat-grid">
            <article v-for="(card,index) in statCards" :key="card.label" :class="{ accent: card.accent }">
                <div><span>0{{ index + 1 }}</span><i>↗</i></div>
                <strong>{{ card.value }}</strong>
                <h2>{{ card.label }}</h2>
                <p>{{ card.meta }}</p>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="panel traffic-panel">
                <div class="panel-head"><div><span>Analitika</span><h2>Pastarųjų 7 dienų srautas</h2></div><strong>{{ stats.month_visits }}<small>šį mėnesį</small></strong></div>
                <div class="chart">
                    <div v-for="day in visitSeries" :key="day.label" class="chart-column">
                        <div class="bar-wrap"><i :style="{ height: `${Math.max((day.count / maxVisits) * 100, 4)}%` }"><b>{{ day.count }}</b></i></div>
                        <span>{{ day.label }}</span>
                    </div>
                </div>
            </article>

            <article class="panel quick-panel">
                <div class="panel-head"><div><span>Greiti veiksmai</span><h2>Turinio valdymas</h2></div></div>
                <nav>
                    <Link href="/dashboard/projects/create"><i>01</i><span><strong>Naujas projektas</strong><small>Pridėti 3D case study</small></span><b>→</b></Link>
                    <Link href="/dashboard/inquiries"><i>02</i><span><strong>Peržiūrėti užklausas</strong><small>{{ stats.new_inquiries }} laukia atsakymo</small></span><b>→</b></Link>
                    <Link href="/dashboard/settings"><i>03</i><span><strong>SEO nustatymai</strong><small>Pavadinimai ir socialinis vaizdas</small></span><b>→</b></Link>
                </nav>
            </article>
        </section>

        <section class="insight-grid">
            <article class="panel source-panel">
                <div class="panel-head"><div><span>Atradimas</span><h2>Iš kur ateina lankytojai</h2></div><small>Šis mėnuo</small></div>
                <div v-if="trafficSources.length" class="source-list">
                    <div v-for="source in trafficSources" :key="`${source.source}-${source.medium}`">
                        <i :style="{ width: `${Math.max((Number(source.visits) / maxSourceVisits) * 100, 3)}%` }"></i>
                        <span><strong>{{ source.source }}</strong><small>{{ source.medium || 'kitas šaltinis' }}</small></span>
                        <b>{{ source.visits }}<small>{{ source.visitors }} apyt. lankytojai</small></b>
                    </div>
                </div>
                <div v-else class="empty-state">Šaltinių duomenys pradės kauptis po atnaujinimo.</div>
            </article>

            <article class="panel pages-panel">
                <div class="panel-head"><div><span>Turinys</span><h2>Lankomiausi puslapiai</h2></div><small>Šis mėnuo</small></div>
                <div v-if="topPages.length" class="rank-list">
                    <div v-for="(page,index) in topPages" :key="page.url"><i>{{ String(index + 1).padStart(2, '0') }}</i><span><strong>{{ page.url }}</strong><small>{{ page.visitors }} unikalūs</small></span><b>{{ page.visits }}</b></div>
                </div>
                <div v-else class="empty-state">Puslapių peržiūrų kol kas nėra.</div>
            </article>

            <article class="panel seo-panel">
                <div class="seo-score"><span>Techninė SEO parengtis</span><strong>{{ completedChecks }}<small>/ {{ readinessChecks.length }} punktų</small></strong><i :style="{ '--score': `${seoHealth.score}%` }"></i></div>
                <p class="seo-note">Tai techninių punktų sąrašas, ne „Google“ reitingas ar lankytojų prognozė.</p>
                <div class="seo-checks">
                    <div v-for="check in seoHealth.checks" :key="check.label" :class="{ ready: check.ok, informational: check.informational }"><i>{{ check.informational ? 'i' : check.ok ? '✓' : '!' }}</i><span>{{ check.label }}<small v-if="check.detail">{{ check.detail }}</small></span></div>
                </div>
                <a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer">Indeksavimas ir pozicijos: Search Console ↗</a>
                <Link href="/dashboard/settings">Tvarkyti SEO nustatymus →</Link>
            </article>
        </section>

        <section class="dashboard-grid analytics-detail-grid">
            <article class="panel device-panel">
                <div class="panel-head"><div><span>Įrenginiai</span><h2>Lankytojų ekranai</h2></div></div>
                <div v-if="devices.length" class="device-list">
                    <div v-for="device in devices" :key="device.device"><span><strong>{{ device.device || 'Nežinoma' }}</strong><small>{{ Math.round((Number(device.visits) / deviceTotal) * 100) }}%</small></span><i><b :style="{ width: `${(Number(device.visits) / deviceTotal) * 100}%` }"></b></i></div>
                </div>
                <div v-else class="empty-state">Įrenginių duomenys pradės kauptis po atnaujinimo.</div>
            </article>

            <article class="panel campaign-panel">
                <div class="panel-head"><div><span>UTM kampanijos</span><h2>Aktyvios kampanijos</h2></div></div>
                <div v-if="campaigns.length" class="rank-list">
                    <div v-for="(campaign,index) in campaigns" :key="`${campaign.campaign}-${campaign.source}`"><i>{{ String(index + 1).padStart(2, '0') }}</i><span><strong>{{ campaign.campaign }}</strong><small>{{ campaign.source }}</small></span><b>{{ campaign.visits }}</b></div>
                </div>
                <div v-else class="empty-state">Naudokite <code>?utm_source=...&utm_campaign=...</code> kampanijų nuorodose.</div>
            </article>
        </section>

        <section class="dashboard-grid lower-grid">
            <article class="panel list-panel">
                <div class="panel-head"><div><span>Portfolio</span><h2>Naujausi projektai</h2></div><Link href="/dashboard/projects">Visi projektai →</Link></div>
                <div v-if="recentProjects.length" class="project-list">
                    <Link v-for="project in recentProjects" :key="project.id" :href="`/dashboard/projects/${project.id}/edit`">
                        <img v-if="project.thumbnail" :src="`/storage/${project.thumbnail}`" alt=""><div v-else class="project-placeholder">DS</div>
                        <span><strong>{{ project.title }}</strong><small>{{ project.services || 'Projektas' }}</small></span>
                        <i v-if="project.featured">Pagrindinis</i><b>↗</b>
                    </Link>
                </div>
                <div v-else class="empty-state">Projektų dar nėra. <Link href="/dashboard/projects/create">Sukurti pirmą →</Link></div>
            </article>

            <article class="panel list-panel">
                <div class="panel-head"><div><span>Kontaktai</span><h2>Naujausios užklausos</h2></div><Link href="/dashboard/inquiries">Visos užklausos →</Link></div>
                <div v-if="recentInquiries.length" class="inquiry-list">
                    <Link v-for="inquiry in recentInquiries" :key="inquiry.id" :href="`/dashboard/inquiries/${inquiry.id}`">
                        <div>{{ inquiry.name?.charAt(0) }}</div><span><strong>{{ inquiry.name }}</strong><small>{{ inquiry.company || inquiry.service }}</small></span><i :class="{ unread: !inquiry.is_read }">{{ inquiry.is_read ? 'Peržiūrėta' : 'Nauja' }}</i><b>→</b>
                    </Link>
                </div>
                <div v-else class="empty-state">Naujų užklausų kol kas nėra.</div>
            </article>
        </section>
    </AdminLayout>
</template>

<style scoped>
.mail-warning{margin-bottom:1rem;padding:1.1rem 1.25rem;border:1px solid #f59e0b55;border-radius:1rem;background:#f59e0b0d;color:#fde68a}.mail-warning strong{font-size:.9rem}.mail-warning p{margin:.5rem 0;color:#c4b99b;font-size:.8rem;line-height:1.65}.mail-warning a{display:inline-block;padding:.35rem 0;font-size:.8rem;font-weight:800;text-decoration:underline;text-underline-offset:3px}.analytics-note{margin:0 0 1rem;color:#8a9bb1;font-size:.75rem;line-height:1.6}
.seo-note{margin-top:.85rem;color:#8293a9;font-size:.68rem;line-height:1.6}.seo-checks>div{align-items:flex-start}.seo-checks small{display:block;margin-top:.2rem;color:#8293a9;font-size:.61rem;line-height:1.55}.seo-checks>div.informational>i{color:#a4b4cb;background:rgba(148,163,184,.1);flex-shrink:0}
.dashboard-head{display:flex;align-items:flex-end;justify-content:space-between;gap:2rem;margin-bottom:2rem}.dashboard-head>div>span,.panel-head>div>span{color:#22d3ee;font-size:.65rem;font-weight:800;letter-spacing:.17em;text-transform:uppercase}.dashboard-head h1{margin-top:.5rem;font-size:clamp(2.5rem,5vw,4.8rem);font-weight:850;letter-spacing:-.065em;line-height:.92}.dashboard-head p{margin-top:.65rem;color:#68778c;font-size:.82rem;text-transform:capitalize}.dashboard-head>a{display:flex;align-items:center;gap:1rem;min-height:3.2rem;padding:0 1.1rem;border-radius:.85rem;color:#061016;background:#22d3ee;font-size:.75rem;font-weight:850;box-shadow:0 15px 40px rgba(34,211,238,.14)}.dashboard-head>a i{font-style:normal}
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.8rem}.stat-grid article{min-height:210px;padding:1.25rem;border:1px solid rgba(255,255,255,.075);border-radius:1.2rem;background:linear-gradient(145deg,rgba(255,255,255,.035),rgba(255,255,255,.015))}.stat-grid article.accent{border-color:rgba(34,211,238,.17);background:linear-gradient(145deg,rgba(34,211,238,.075),rgba(255,255,255,.015))}.stat-grid article>div{display:flex;justify-content:space-between;color:#4d5b70;font-size:.6rem;font-weight:800}.stat-grid article>div i{color:#22d3ee;font-style:normal}.stat-grid article>strong{display:block;margin-top:2rem;font-size:3.5rem;font-weight:850;letter-spacing:-.07em}.stat-grid article.accent>strong{color:#67e8f9}.stat-grid h2{margin-top:.45rem;font-size:.82rem;font-weight:750}.stat-grid p{margin-top:.3rem;color:#58677c;font-size:.68rem}
.dashboard-grid{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(300px,.65fr);gap:.8rem;margin-top:.8rem}.lower-grid{grid-template-columns:1fr 1fr}.panel{padding:1.4rem;border:1px solid rgba(255,255,255,.075);border-radius:1.2rem;background:rgba(255,255,255,.022)}.panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem}.panel-head h2{margin-top:.4rem;font-size:1rem;font-weight:800}.panel-head>strong{display:flex;flex-direction:column;align-items:flex-end;color:#67e8f9;font-size:1.8rem;letter-spacing:-.04em}.panel-head>strong small{color:#536177;font-size:.58rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.panel-head>a{color:#718096;font-size:.68rem;font-weight:750}
.panel-head>small{color:#536177;font-size:.58rem;font-weight:750;letter-spacing:.08em;text-transform:uppercase}.insight-grid{display:grid;grid-template-columns:1.1fr 1fr .72fr;gap:.8rem;margin-top:.8rem}.source-list,.rank-list{display:grid;margin-top:1.1rem}.source-list>div{position:relative;display:grid;grid-template-columns:1fr auto;align-items:center;gap:1rem;min-height:3.65rem;border-top:1px solid rgba(255,255,255,.06);overflow:hidden}.source-list>div>i{position:absolute;left:0;bottom:0;height:2px;background:linear-gradient(90deg,#22d3ee,#2563eb)}.source-list span,.source-list b,.rank-list span{display:flex;flex-direction:column}.source-list strong,.rank-list strong{font-size:.72rem}.source-list span small,.source-list b small,.rank-list small{margin-top:.2rem;color:#536177;font-size:.55rem;font-weight:650}.source-list b{text-align:right;color:#67e8f9;font-size:.82rem}.rank-list>div{display:grid;grid-template-columns:1.8rem minmax(0,1fr) auto;align-items:center;gap:.6rem;min-height:3.45rem;border-top:1px solid rgba(255,255,255,.06)}.rank-list>div>i{color:#455268;font-size:.55rem;font-style:normal;font-weight:800}.rank-list strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.rank-list>div>b{color:#67e8f9;font-size:.78rem}.seo-panel{display:flex;flex-direction:column}.seo-score>span{color:#22d3ee;font-size:.65rem;font-weight:800;letter-spacing:.17em;text-transform:uppercase}.seo-score>strong{display:block;margin-top:.8rem;font-size:3.4rem;letter-spacing:-.07em}.seo-score>strong small{color:#536177;font-size:.8rem;letter-spacing:0}.seo-score>i{display:block;height:4px;margin-top:.8rem;border-radius:99px;background:linear-gradient(90deg,#22d3ee var(--score),rgba(255,255,255,.08) var(--score))}.seo-checks{display:grid;margin-top:1rem}.seo-checks>div{display:flex;align-items:center;gap:.55rem;padding:.45rem 0;color:#78869a;font-size:.62rem}.seo-checks>div>i{display:grid;place-items:center;width:1.1rem;height:1.1rem;border-radius:99px;color:#f59e0b;background:rgba(245,158,11,.1);font-style:normal;font-weight:900}.seo-checks>div.ready>i{color:#67e8f9;background:rgba(34,211,238,.1)}.seo-panel>a{margin-top:auto;padding-top:1rem;color:#67e8f9;font-size:.65rem;font-weight:800}.analytics-detail-grid{grid-template-columns:1fr 1fr}.device-list{display:grid;gap:.85rem;margin-top:1.2rem}.device-list>div>span{display:flex;justify-content:space-between;font-size:.68rem}.device-list small{color:#66758a}.device-list>div>i{display:block;height:5px;margin-top:.45rem;overflow:hidden;border-radius:99px;background:rgba(255,255,255,.06)}.device-list>div>i>b{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#22d3ee,#2563eb)}.empty-state code{color:#67e8f9;font-size:.62rem}
.chart{display:flex;align-items:end;gap:.75rem;height:250px;margin-top:2rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.06)}.chart-column{display:flex;flex:1;height:100%;flex-direction:column;align-items:center;gap:.6rem}.bar-wrap{display:flex;align-items:end;width:100%;height:100%}.bar-wrap>i{position:relative;display:block;width:100%;min-height:4px;border-radius:.45rem .45rem .15rem .15rem;background:linear-gradient(#22d3ee,#2563eb);box-shadow:0 0 18px rgba(34,211,238,.1);transition:height .6s ease}.bar-wrap b{position:absolute;left:50%;top:-1.25rem;color:#6b7a90;font-size:.58rem;font-style:normal;transform:translateX(-50%)}.chart-column>span{color:#536177;font-size:.58rem}
.quick-panel nav{display:grid;margin-top:1.2rem;border-top:1px solid rgba(255,255,255,.07)}.quick-panel nav a{display:grid;grid-template-columns:2rem 1fr auto;align-items:center;gap:.6rem;min-height:4.6rem;border-bottom:1px solid rgba(255,255,255,.07)}.quick-panel nav a>i{color:#445166;font-size:.58rem;font-style:normal;font-weight:800}.quick-panel nav a>span{display:flex;flex-direction:column}.quick-panel nav strong{font-size:.75rem}.quick-panel nav small{margin-top:.2rem;color:#536177;font-size:.6rem}.quick-panel nav b{color:#22d3ee}.quick-panel nav a:hover strong{color:#67e8f9}
.project-list,.inquiry-list{display:grid;margin-top:1.1rem}.project-list>a,.inquiry-list>a{display:grid;grid-template-columns:3rem 1fr auto auto;align-items:center;gap:.7rem;min-height:4.5rem;border-top:1px solid rgba(255,255,255,.06)}.project-list img,.project-placeholder{width:3rem;height:2.6rem;border-radius:.55rem;object-fit:cover}.project-placeholder{display:grid;place-items:center;color:#67e8f9;background:rgba(34,211,238,.08);font-size:.65rem;font-weight:850}.project-list>a>span,.inquiry-list>a>span{display:flex;min-width:0;flex-direction:column}.project-list strong,.inquiry-list strong{overflow:hidden;font-size:.74rem;text-overflow:ellipsis;white-space:nowrap}.project-list small,.inquiry-list small{overflow:hidden;margin-top:.2rem;color:#526177;font-size:.6rem;text-overflow:ellipsis;white-space:nowrap}.project-list>a>i,.inquiry-list>a>i{padding:.3rem .45rem;border-radius:999px;color:#65d6e7;background:rgba(34,211,238,.08);font-size:.55rem;font-style:normal;font-weight:750}.inquiry-list>a>div{display:grid;place-items:center;width:2.4rem;height:2.4rem;border-radius:.65rem;color:#9aa8ba;background:rgba(255,255,255,.045);font-size:.7rem;font-weight:850}.inquiry-list>a>i{color:#758397;background:rgba(255,255,255,.04)}.inquiry-list>a>i.unread{color:#67e8f9;background:rgba(34,211,238,.1)}.project-list>a>b,.inquiry-list>a>b{color:#4e5d72}.empty-state{margin-top:1rem;padding:2rem;border:1px dashed rgba(255,255,255,.08);border-radius:.8rem;color:#617086;font-size:.72rem;text-align:center}.empty-state a{color:#22d3ee}
@media(max-width:1150px){.stat-grid{grid-template-columns:1fr 1fr}.dashboard-grid,.lower-grid,.insight-grid,.analytics-detail-grid{grid-template-columns:1fr}}
@media(max-width:640px){.dashboard-head{align-items:flex-start;flex-direction:column}.dashboard-head>a{width:100%;justify-content:space-between}.stat-grid{grid-template-columns:1fr 1fr}.stat-grid article{min-height:175px;padding:1rem}.stat-grid article>strong{margin-top:1.5rem;font-size:2.8rem}.panel{padding:1rem}.chart{gap:.35rem;height:210px}.project-list>a,.inquiry-list>a{grid-template-columns:2.6rem 1fr auto}.project-list>a>i,.inquiry-list>a>i{display:none}.project-list>a>b,.inquiry-list>a>b{display:none}}
</style>
