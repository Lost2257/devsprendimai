<script setup>
import { computed, nextTick, onMounted, onBeforeUnmount, ref, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'
import { MESSAGE_LIMIT, mergeProjectBrief, PROJECT_BRIEF_EVENT } from '@/lib/projectPlanner'
import { analytics } from '@/lib/analytics'

const props = defineProps({
    service: { type: String, default: 'Kita' },
})
const { isEnglish, localize } = useLocale()
const serviceOptions = computed(() => isEnglish.value
    ? ['Website', 'Online store', 'CRM system', 'Application development', 'Automation', 'Website maintenance', 'Other']
    : ['Interneto svetainė', 'El. parduotuvė', 'CRM sistema', 'Aplikacijų kūrimas', 'Automatizacija', 'Svetainės priežiūra', 'Kita'])

const serviceTranslations = {
    'Interneto svetainė': 'Website',
    'El. parduotuvė': 'Online store',
    'CRM sistema': 'CRM system',
    'Aplikacijų kūrimas': 'Application development',
    'Automatizacija': 'Automation',
    'Svetainės priežiūra': 'Website maintenance',
    'Kita': 'Other',
}
const reverseServiceTranslations = Object.fromEntries(
    Object.entries(serviceTranslations).map(([lithuanian, english]) => [english, lithuanian]),
)
const selectedService = computed(() => {
    const requestedService = props.service || (isEnglish.value ? 'Other' : 'Kita')

    if (serviceOptions.value.includes(requestedService)) return requestedService

    const translatedService = isEnglish.value
        ? serviceTranslations[requestedService]
        : reverseServiceTranslations[requestedService]

    return serviceOptions.value.includes(translatedService)
        ? translatedService
        : (isEnglish.value ? 'Other' : 'Kita')
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    service: selectedService.value,
    message: '',
    website: '',
})
const draftNotice = ref('')
let previousDraft = ''

const applyProjectBrief = async (event) => {
    const brief = event.detail
    if (!brief || typeof brief.message !== 'string') return
    const service = isEnglish.value ? (serviceTranslations[brief.service] || brief.service) : (reverseServiceTranslations[brief.service] || brief.service)
    if (!serviceOptions.value.includes(service)) return
    const merged = mergeProjectBrief(form.message, previousDraft, brief.message)
    if (merged === null) {
        draftNotice.value = isEnglish.value
            ? 'Your existing message is too long to add the draft. It has been kept unchanged; please review it below.'
            : 'Jūsų žinutė per ilga, kad pridėtume planą. Išsaugojome ją nepakeistą – peržiūrėkite ją žemiau.'
    } else {
        form.service = service
        form.message = merged
        previousDraft = brief.message
        clearError('service')
        clearError('message')
        draftNotice.value = isEnglish.value
            ? 'Your draft is ready. Review it and add your contact details. Nothing has been sent yet.'
            : 'Planas perkeltas. Peržiūrėkite jį ir įrašykite kontaktus. Užklausa dar neišsiųsta.'
    }
    await nextTick()
    const target = document.getElementById(merged === null ? 'contact-message' : 'contact-name')
    target?.focus({ preventScroll: true })
    document.querySelector('#contact .contact-form')?.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' })
}

onMounted(() => window.addEventListener(PROJECT_BRIEF_EVENT, applyProjectBrief))
onBeforeUnmount(() => window.removeEventListener(PROJECT_BRIEF_EVENT, applyProjectBrief))

const clearError = (field) => delete form.errors[field]
watch(selectedService, (service) => {
    form.service = service
    clearError('service')
})

const submit = () => {
    const completeLead = analytics?.beginLeadSubmission()
    form.post(localize('/contact'), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset()
            form.service = selectedService.value
            previousDraft = ''
            draftNotice.value = ''
            completeLead?.(page.props.flash?.enquiryConversion, page.props)
        },
        onError: async (errors) => {
            await nextTick()
            const field = ['name', 'email', 'phone', 'company', 'service', 'message'].find((name) => errors[name])
            if (field) document.getElementById(`contact-${field}`)?.focus()
        },
    })
}
</script>

<template>
    <section id="contact" class="contact-section">
        <div class="contact-glow" aria-hidden="true"></div>
        <div class="site-container">
            <div class="contact-shell">
                <div class="contact-copy">
                    <span class="contact-kicker">{{ isEnglish ? 'Next step' : 'Kitas žingsnis' }}</span>
                    <h2>{{ isEnglish ? 'Have an idea?' : 'Turite idėją?' }}<br><span>{{ isEnglish ? 'Let’s turn it into a working product.' : 'Paverskime ją veikiančiu sprendimu.' }}</span></h2>
                    <p>{{ isEnglish ? 'Tell me briefly about your goal. I will reply with focused questions and a clear next-step direction. The scope and price are agreed before work begins.' : 'Trumpai papasakokite apie savo tikslą. Atsakysiu su konkrečiais klausimais ir tolimesnių žingsnių kryptimi. Apimtį ir kainą suderiname prieš pradedant darbus.' }}</p>

                    <div class="direct-contact">
                        <span>{{ isEnglish ? 'Or contact directly' : 'Arba susisiekite tiesiogiai' }}</span>
                        <a href="mailto:info@devsprendimai.lt">info@devsprendimai.lt <i>↗</i></a>
                        <a href="tel:+37060606227">+370 606 06227 <i>↗</i></a>
                    </div>
                </div>

                <form class="contact-form" @submit.prevent="submit">
                    <div v-if="draftNotice" class="draft-notice" role="status">{{ draftNotice }}</div>
                    <div class="contact-trap" aria-hidden="true" inert>
                        <label for="contact-website">Website</label>
                        <input id="contact-website" v-model="form.website" type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="field-grid">
                        <div class="field">
                            <label for="contact-name">{{ isEnglish ? 'Name' : 'Vardas' }} <span>*</span></label>
                            <input id="contact-name" v-model="form.name" type="text" autocomplete="name" required maxlength="120" :placeholder="isEnglish ? 'Your name' : 'Jūsų vardas'" :aria-invalid="!!form.errors.name" :aria-describedby="form.errors.name ? 'contact-name-error' : undefined" @input="clearError('name')">
                            <small v-if="form.errors.name" id="contact-name-error">{{ form.errors.name }}</small>
                        </div>
                        <div class="field">
                            <label for="contact-email">{{ isEnglish ? 'Email' : 'El. paštas' }} <span>*</span></label>
                            <input id="contact-email" v-model="form.email" type="email" autocomplete="email" required maxlength="254" :placeholder="isEnglish ? 'name@company.com' : 'vardas@imone.lt'" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'contact-email-error' : undefined" @input="clearError('email')">
                            <small v-if="form.errors.email" id="contact-email-error">{{ form.errors.email }}</small>
                        </div>
                        <div class="field">
                            <label for="contact-phone">{{ isEnglish ? 'Phone' : 'Telefonas' }}</label>
                            <input id="contact-phone" v-model="form.phone" type="tel" autocomplete="tel" maxlength="40" placeholder="+370" :aria-invalid="!!form.errors.phone" :aria-describedby="form.errors.phone ? 'contact-phone-error' : undefined" @input="clearError('phone')">
                            <small v-if="form.errors.phone" id="contact-phone-error">{{ form.errors.phone }}</small>
                        </div>
                        <div class="field">
                            <label for="contact-company">{{ isEnglish ? 'Company' : 'Įmonė' }}</label>
                            <input id="contact-company" v-model="form.company" type="text" autocomplete="organization" maxlength="160" :placeholder="isEnglish ? 'Company name' : 'Įmonės pavadinimas'" :aria-invalid="!!form.errors.company" :aria-describedby="form.errors.company ? 'contact-company-error' : undefined" @input="clearError('company')">
                            <small v-if="form.errors.company" id="contact-company-error">{{ form.errors.company }}</small>
                        </div>
                    </div>

                    <div class="field">
                        <label for="contact-service">{{ isEnglish ? 'Service of interest' : 'Dominanti paslauga' }} <span>*</span></label>
                        <select id="contact-service" v-model="form.service" required :aria-invalid="!!form.errors.service" :aria-describedby="form.errors.service ? 'contact-service-error' : undefined" @change="clearError('service')">
                            <option value="">{{ isEnglish ? 'Select a service' : 'Pasirinkite paslaugą' }}</option>
                            <option v-for="option in serviceOptions" :key="option">{{ option }}</option>
                        </select>
                        <small v-if="form.errors.service" id="contact-service-error">{{ form.errors.service }}</small>
                    </div>

                    <div class="field">
                        <label for="contact-message">{{ isEnglish ? 'About the project' : 'Apie projektą' }} <span>*</span></label>
                        <textarea id="contact-message" v-model="form.message" rows="5" required minlength="10" :maxlength="MESSAGE_LIMIT" :placeholder="isEnglish ? 'What would you like to build or improve?' : 'Ką norite sukurti ar patobulinti?'" :aria-invalid="!!form.errors.message" :aria-describedby="form.errors.message ? 'contact-message-error' : undefined" @input="clearError('message')"></textarea>
                        <small v-if="form.errors.message" id="contact-message-error">{{ form.errors.message }}</small>
                    </div>

                    <button type="submit" :disabled="form.processing">
                        <span>{{ form.processing ? (isEnglish ? 'Sending...' : 'Siunčiama...') : (isEnglish ? 'Get a proposal' : 'Gauti pasiūlymą') }}</span>
                        <i aria-hidden="true">↗</i>
                    </button>

                    <p class="form-note">
                        {{ isEnglish ? 'Your details will be used to respond to this enquiry, not to subscribe you to marketing. More in the' : 'Jūsų duomenis naudosime atsakymui į šią užklausą, ne reklamos prenumeratai. Daugiau –' }}
                        <Link :href="localize('/privatumo-politika')">{{ isEnglish ? 'Privacy Policy' : 'Privatumo politika' }}</Link>
                        .
                    </p>

                    <div v-if="$page.props.flash?.success" class="form-message success" role="status">{{ $page.props.flash.success }}</div>
                    <div v-if="$page.props.flash?.error" class="form-message error" role="alert">{{ $page.props.flash.error }}</div>
                </form>
            </div>
        </div>
    </section>
</template>

<style scoped>
.contact-section { position: relative; overflow: hidden; padding: clamp(5rem, 9vw, 8rem) 0; scroll-margin-top: 5rem; }
.contact-trap { position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); white-space: nowrap; }
.draft-notice { padding: .85rem 1rem; border: 1px solid rgba(103,232,249,.3); border-radius: .75rem; color: #b5f1f7; background: rgba(34,211,238,.07); font-size: .8rem; line-height: 1.6; }
.contact-glow { position: absolute; left: 50%; bottom: -28rem; width: 70rem; height: 48rem; border-radius: 50%; background: rgba(34,211,238,.11); filter: blur(110px); transform: translateX(-50%); pointer-events: none; }
.contact-shell { position: relative; display: grid; grid-template-columns: minmax(0,.85fr) minmax(440px,1fr); gap: clamp(3rem,7vw,7rem); padding: clamp(2rem,5vw,4.5rem); overflow: hidden; border: 1px solid rgba(255,255,255,.1); border-radius: 2rem; background: linear-gradient(145deg, rgba(17,26,43,.76), rgba(10,15,26,.7)); box-shadow: 0 35px 100px rgba(0,0,0,.28); }
.contact-shell::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px); background-size: 40px 40px; mask-image: linear-gradient(120deg, black, transparent 70%); pointer-events: none; }
.contact-copy,
.contact-form { position: relative; z-index: 2; }
.contact-kicker { color: #67e8f9; font-size: .74rem; font-weight: 800; letter-spacing: .22em; text-transform: uppercase; }
.contact-copy h2 { margin-top: 1.2rem; font-size: clamp(2.5rem,4.3vw,4.6rem); font-weight: 850; letter-spacing: -.06em; line-height: 1.03; }
.contact-copy h2 span { color: #67e8f9; }
.contact-copy > p { margin-top: 1.5rem; color: #8e9cb1; line-height: 1.75; }
.direct-contact { display: flex; flex-direction: column; align-items: flex-start; gap: .65rem; margin-top: 3rem; }
.direct-contact > span { margin-bottom: .4rem; color: #536279; font-size: .68rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
.direct-contact a { display: flex; gap: .7rem; color: #c7d2df; font-weight: 700; }
.direct-contact a i { color: #22d3ee; font-style: normal; }
.contact-form { display: grid; gap: 1.15rem; scroll-margin-top: 7rem; }
.field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.15rem; }
.field label { display: block; margin-bottom: .45rem; color: #aab6c6; font-size: .74rem; font-weight: 750; }
.field label span { color: #22d3ee; }
.field input,
.field select,
.field textarea { width: 100%; border: 1px solid rgba(255,255,255,.1); border-radius: .85rem; color: #f8fafc; background: rgba(5,10,19,.7); box-shadow: none; transition: border-color .2s ease, box-shadow .2s ease; }
.field input,
.field select { min-height: 3.3rem; padding: 0 1rem; }
.field textarea { padding: .9rem 1rem; resize: vertical; }
.field input::placeholder,
.field textarea::placeholder { color: #4d5b70; }
.field input:focus,
.field select:focus,
.field textarea:focus { outline: none; border-color: rgba(34,211,238,.65); box-shadow: 0 0 0 3px rgba(34,211,238,.08); }
.field [aria-invalid="true"] { border-color: rgba(248,113,113,.7); }
.field small { display: block; margin-top: .35rem; color: #fda4af; font-size: .72rem; }
.contact-form button { display: flex; align-items: center; justify-content: space-between; min-height: 3.7rem; margin-top: .25rem; padding: 0 1.25rem; border-radius: .9rem; color: #061016; background: #22d3ee; font-weight: 850; transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease; }
.contact-form button:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 14px 35px rgba(34,211,238,.22); }
.contact-form button:disabled { cursor: wait; opacity: .65; }
.contact-form button i { font-style: normal; }
.form-note { color: #92a2b7; font-size: .72rem; line-height: 1.6; }
.form-note a { color:#79dce9; text-decoration:underline; text-decoration-color:rgba(121,220,233,.35); text-underline-offset:3px; }
.form-message { padding: .8rem 1rem; border-radius: .8rem; font-size: .84rem; }
.form-message.success { border: 1px solid rgba(52,211,153,.25); color: #6ee7b7; background: rgba(52,211,153,.08); }
.form-message.error { border: 1px solid rgba(248,113,113,.25); color: #fca5a5; background: rgba(248,113,113,.08); }

@media (max-width: 960px) {
    .contact-shell { grid-template-columns: 1fr; }
    .contact-copy > p { max-width: 650px; }
}

@media (max-width: 600px) {
    .contact-section { padding-block: 4.5rem; }
    .contact-shell { gap: 2.4rem; padding: 1.25rem; border-radius: 1.35rem; }
    .contact-kicker { font-size: .66rem; }
    .contact-copy h2 { margin-top: .9rem; font-size: clamp(2.35rem,11vw,3.25rem); line-height: 1.02; }
    .contact-copy > p { margin-top: 1.15rem; font-size: .94rem; }
    .direct-contact { margin-top: 1.7rem; }
    .direct-contact a { min-height: 2rem; align-items: center; }
    .field-grid { grid-template-columns: 1fr; }
    .contact-form { gap: .95rem; }
    .field-grid { gap: .95rem; }
    .field input,
    .field select { min-height: 3.55rem; font-size: 1rem; }
    .field textarea { min-height: 8.5rem; font-size: 1rem; }
    .contact-form button { min-height: 3.75rem; }
}
</style>
