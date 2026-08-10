<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const menuOpen = ref(false)
const currentPath = computed(() => page.url.split('?')[0])
const user = computed(() => page.props.auth?.user)

const navigation = [
    { index: '01', label: 'Apžvalga', href: '/dashboard' },
    { index: '02', label: 'Projektai', href: '/dashboard/projects' },
    { index: '03', label: 'Užklausos', href: '/dashboard/inquiries', badge: true },
    { index: '04', label: 'Vartotojai', href: '/dashboard/users' },
    { index: '05', label: 'SEO ir nustatymai', href: '/dashboard/settings' },
]

const active = (href) => href === '/dashboard' ? currentPath.value === href : currentPath.value.startsWith(href)
</script>

<template>
    <div class="admin-shell">
        <div class="admin-grid" aria-hidden="true"></div>

        <header class="mobile-admin-header">
            <Link href="/dashboard" class="mobile-brand">DS<span>.</span></Link>
            <span>Valdymo centras</span>
            <button type="button" :aria-expanded="menuOpen" @click="menuOpen = !menuOpen"><i></i><i></i></button>
        </header>

        <Transition name="backdrop"><button v-if="menuOpen" class="admin-backdrop" aria-label="Uždaryti meniu" @click="menuOpen = false"></button></Transition>

        <aside class="admin-sidebar" :class="{ open: menuOpen }">
            <div class="brand-block">
                <Link href="/dashboard" class="admin-brand" @click="menuOpen = false">
                    <span class="brand-mark">DS</span>
                    <span><strong>DevSprendimai</strong><small>Valdymo centras</small></span>
                </Link>
                <div class="system-status"><i></i> Sistema veikia</div>
            </div>

            <nav aria-label="Administravimo navigacija">
                <span class="nav-label">Pagrindinis meniu</span>
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    class="admin-nav-link"
                    :class="{ active: active(item.href) }"
                    @click="menuOpen = false"
                >
                    <i>{{ item.index }}</i><span>{{ item.label }}</span>
                    <b v-if="item.badge && $page.props.newInquiries">{{ $page.props.newInquiries }}</b>
                    <em v-else>↗</em>
                </Link>
            </nav>

            <div class="sidebar-bottom">
                <a href="/" target="_blank" class="site-link"><span>Atidaryti svetainę</span><i>↗</i></a>
                <div class="admin-user">
                    <div>{{ user?.name?.charAt(0)?.toUpperCase() || 'A' }}</div>
                    <span><strong>{{ user?.name || 'Administratorius' }}</strong><small>{{ user?.email }}</small></span>
                    <Link href="/logout" method="post" as="button" title="Atsijungti">→</Link>
                </div>
            </div>
        </aside>

        <main class="admin-content">
            <div class="admin-content-inner"><slot /></div>
        </main>
    </div>
</template>

<style scoped>
.admin-shell { position:relative;min-height:100vh;color:#f8fafc;background:#080c14;font-family:'Manrope',sans-serif; }
.admin-grid { position:fixed;inset:0;opacity:.26;background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(circle at 70% 20%,black,transparent 75%);pointer-events:none; }
.admin-sidebar { position:fixed;z-index:40;inset:0 auto 0 0;display:flex;flex-direction:column;width:286px;padding:1rem;border-right:1px solid rgba(255,255,255,.075);background:rgba(8,13,23,.88);backdrop-filter:blur(22px); }
.brand-block { padding:.6rem .5rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07); }
.admin-brand { display:flex;align-items:center;gap:.75rem; }
.brand-mark { display:grid;place-items:center;width:2.65rem;height:2.65rem;border:1px solid rgba(34,211,238,.28);border-radius:.85rem;color:#061016;background:#22d3ee;font-size:.78rem;font-weight:900;box-shadow:0 10px 28px rgba(34,211,238,.15); }
.admin-brand>span:last-child { display:flex;flex-direction:column; }
.admin-brand strong { font-size:.9rem;letter-spacing:-.02em; }.admin-brand small { margin-top:.12rem;color:#546277;font-size:.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase; }
.system-status { display:flex;align-items:center;gap:.45rem;margin:1rem 0 0 3.4rem;color:#65748a;font-size:.62rem;font-weight:750; }.system-status i{width:6px;height:6px;border-radius:50%;background:#34d399;box-shadow:0 0 10px #34d399;}
.admin-sidebar nav { display:grid;gap:.3rem;margin-top:1.5rem; }
.nav-label { margin:0 .65rem .55rem;color:#3f4c60;font-size:.58rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase; }
.admin-nav-link { display:grid;grid-template-columns:2rem 1fr auto;align-items:center;min-height:3.25rem;padding:0 .75rem;border:1px solid transparent;border-radius:.85rem;color:#8492a6;transition:color .2s ease,background .2s ease,border-color .2s ease; }
.admin-nav-link>i { color:#465469;font-size:.58rem;font-style:normal;font-weight:800;letter-spacing:.08em; }.admin-nav-link>span{font-size:.78rem;font-weight:750}.admin-nav-link>em{color:#3f4d61;font-size:.75rem;font-style:normal}.admin-nav-link>b{display:grid;place-items:center;min-width:1.5rem;height:1.5rem;padding:0 .35rem;border-radius:999px;color:#071018;background:#22d3ee;font-size:.62rem;}
.admin-nav-link:hover { color:#dbe6f2;background:rgba(255,255,255,.035); }.admin-nav-link.active{border-color:rgba(34,211,238,.14);color:#ecfeff;background:linear-gradient(90deg,rgba(34,211,238,.12),rgba(34,211,238,.025));}.admin-nav-link.active>i,.admin-nav-link.active>em{color:#22d3ee;}
.sidebar-bottom { display:grid;gap:.75rem;margin-top:auto; }
.site-link { display:flex;align-items:center;justify-content:space-between;padding:.85rem;border:1px solid rgba(255,255,255,.07);border-radius:.8rem;color:#8391a5;font-size:.7rem;font-weight:750; }.site-link i{color:#22d3ee;font-style:normal;}
.admin-user { display:grid;grid-template-columns:2.35rem 1fr auto;align-items:center;gap:.65rem;padding:.7rem;border:1px solid rgba(255,255,255,.07);border-radius:.9rem;background:rgba(255,255,255,.025); }.admin-user>div{display:grid;place-items:center;width:2.35rem;height:2.35rem;border-radius:.7rem;color:#67e8f9;background:rgba(34,211,238,.1);font-size:.8rem;font-weight:850}.admin-user>span{display:flex;min-width:0;flex-direction:column}.admin-user strong{font-size:.7rem}.admin-user small{overflow:hidden;margin-top:.12rem;color:#526177;font-size:.58rem;text-overflow:ellipsis}.admin-user button{color:#65748a;}
.admin-content { position:relative;z-index:2;min-height:100vh;margin-left:286px; }.admin-content-inner{width:min(100%,1500px);margin:0 auto;padding:2.25rem;}
.mobile-admin-header,.admin-backdrop{display:none;}

@media(max-width:900px){
    .mobile-admin-header{position:fixed;z-index:50;inset:0 0 auto;display:grid;grid-template-columns:auto 1fr auto;align-items:center;height:4.6rem;padding:0 1rem;border-bottom:1px solid rgba(255,255,255,.08);background:rgba(8,13,23,.9);backdrop-filter:blur(18px)}.mobile-brand{display:grid;place-items:center;width:2.5rem;height:2.5rem;border-radius:.75rem;color:#071018;background:#22d3ee;font-size:.72rem;font-weight:900}.mobile-brand span{color:#0e7490}.mobile-admin-header>span{padding-left:.75rem;color:#77869a;font-size:.68rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.mobile-admin-header button{position:relative;width:2.7rem;height:2.7rem;border:1px solid rgba(255,255,255,.09);border-radius:.75rem}.mobile-admin-header button i{position:absolute;left:.72rem;right:.72rem;height:1px;background:white}.mobile-admin-header button i:first-child{top:1.05rem}.mobile-admin-header button i:last-child{top:1.55rem}
    .admin-sidebar{width:min(86vw,310px);transform:translateX(-105%);transition:transform .3s cubic-bezier(.2,.7,.2,1)}.admin-sidebar.open{transform:none}.admin-backdrop{position:fixed;z-index:35;inset:0;display:block;background:rgba(0,0,0,.62);backdrop-filter:blur(3px)}.admin-content{margin-left:0;padding-top:4.6rem}.admin-content-inner{padding:1.25rem}
    .backdrop-enter-active,.backdrop-leave-active{transition:opacity .25s ease}.backdrop-enter-from,.backdrop-leave-to{opacity:0}
}

@media(max-width:480px){.admin-content-inner{padding:1rem}.admin-sidebar{padding-bottom:calc(1rem + env(safe-area-inset-bottom))}}
</style>
