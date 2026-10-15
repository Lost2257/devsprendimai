<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'
import { analytics, CONSENT_EVENT, OPEN_CONSENT_EVENT, openConsentSettings } from '@/lib/analytics'

defineProps({ immersive: Boolean })
const page = usePage()
const { isEnglish, localize } = useLocale()
const enabled = computed(() => Boolean(page.props.analytics?.measurementId))
const visible = ref(false)
const choice = ref(null)
const panel = ref(null)
let returnFocus = null

function sync() {
    choice.value = analytics.getConsent()
    visible.value = choice.value === null
}
function open() {
    returnFocus = document.activeElement
    choice.value = analytics.getConsent()
    visible.value = true
    requestAnimationFrame(() => panel.value?.focus())
}
function choose(accept) {
    analytics.setConsent(accept)
    visible.value = false
    returnFocus?.focus()
}
onMounted(() => {
    sync()
    window.addEventListener(CONSENT_EVENT, sync)
    window.addEventListener(OPEN_CONSENT_EVENT, open)
})
onUnmounted(() => {
    window.removeEventListener(CONSENT_EVENT, sync)
    window.removeEventListener(OPEN_CONSENT_EVENT, open)
})
</script>

<template>
    <Teleport to="body">
        <section v-if="enabled && visible" ref="panel" class="cookie-panel" role="region" tabindex="-1" aria-labelledby="cookie-title" aria-describedby="cookie-description">
            <div class="cookie-copy">
                <span class="cookie-kicker">{{ isEnglish ? 'YOUR PRIVACY' : 'JŪSŲ PRIVATUMAS' }}</span>
                <h2 id="cookie-title">{{ isEnglish ? 'Cookies. Your choice.' : 'Slapukai. Jūsų pasirinkimas.' }}</h2>
                <p id="cookie-description">{{ isEnglish ? 'Essential cookies keep the site working. With your permission, Google Analytics and our internal visitor statistics help us understand traffic and improve the site. You can change your choice at any time.' : 'Būtinieji slapukai užtikrina svetainės veikimą. Jums sutikus, „Google Analytics“ ir vidinė lankomumo statistika padeda suprasti srautą bei tobulinti svetainę. Pasirinkimą galite bet kada pakeisti.' }}</p>
                <Link :href="localize('/slapuku-politika')">{{ isEnglish ? 'Cookie policy' : 'Slapukų politika' }} ↗</Link>
                <span v-if="choice !== null" class="cookie-status">{{ isEnglish ? `Analytics: ${choice ? 'enabled' : 'disabled'}` : `Analitika: ${choice ? 'įjungta' : 'išjungta'}` }}</span>
            </div>
            <div class="cookie-actions">
                <button type="button" @click="choose(false)">{{ isEnglish ? 'Essential only' : 'Tik būtinieji' }}</button>
                <button type="button" @click="choose(true)">{{ isEnglish ? 'Allow analytics' : 'Leisti analitiką' }}</button>
                <button v-if="choice !== null" type="button" class="cookie-close" @click="visible = false; returnFocus?.focus()">{{ isEnglish ? 'Keep my choice' : 'Palikti pasirinkimą' }}</button>
            </div>
        </section>
        <button v-if="enabled && immersive && !visible" type="button" class="cookie-reopen" @click="openConsentSettings">{{ isEnglish ? 'Cookies' : 'Slapukai' }}</button>
    </Teleport>
</template>

<style scoped>
.cookie-panel { position:fixed; z-index:200; inset:auto max(1rem, env(safe-area-inset-right)) max(1rem, env(safe-area-inset-bottom)) auto; width:min(720px, calc(100% - 2rem)); max-height:calc(100dvh - 2rem); overflow:auto; display:grid; grid-template-columns:minmax(0, 1fr) 170px; align-items:center; gap:1.5rem; padding:1.5rem; color:#eef5ff; background:#0b1421; border:1px solid #285361; border-radius:1.25rem; box-shadow:0 20px 65px #0008; }
.cookie-kicker { color:#67e8f9; font-size:.65rem; letter-spacing:.16em; font-weight:800; }
.cookie-panel h2 { margin:.4rem 0 .65rem; font-size:1.2rem; font-weight:800; line-height:1.3; }
.cookie-panel p { margin:0 0 .8rem; color:#b6c3d4; font-size:.84rem; line-height:1.65; }
.cookie-panel a { color:#8beaf7; font-size:.8rem; text-decoration:underline; text-underline-offset:3px; }
.cookie-status { display:block; margin-top:.5rem; color:#b6c3d4; font-size:.76rem; }
.cookie-actions { display:grid; gap:.65rem; }
.cookie-actions button { min-height:44px; padding:.7rem .8rem; border:1px solid #4a6577; border-radius:.7rem; color:#e8faff; background:#132a37; font-size:.8rem; font-weight:750; transition:background .15s; }
.cookie-actions button:hover { background:#204151; }
.cookie-actions .cookie-close { background:transparent; border-color:transparent; color:#b6c3d4; }
.cookie-panel :focus-visible, .cookie-reopen:focus-visible { outline:2px solid #67e8f9; outline-offset:3px; }
.cookie-reopen { position:fixed; z-index:80; left:max(1rem, env(safe-area-inset-left)); bottom:max(4.75rem, calc(env(safe-area-inset-bottom) + 4rem)); border:1px solid #285361; border-radius:2rem; padding:.6rem .9rem; min-height:44px; color:#b8d1dc; background:#0b1421; font-size:.72rem; }
@media (max-width:600px) {
    .cookie-panel { grid-template-columns:1fr; gap:1rem; padding:1.15rem; }
    .cookie-actions { grid-template-columns:1fr 1fr; }
    .cookie-actions .cookie-close { grid-column:1 / -1; }
}
</style>
