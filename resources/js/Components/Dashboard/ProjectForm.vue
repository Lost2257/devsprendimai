<script setup>
const props = defineProps({
    form: { type: Object, required: true },
    project: { type: Object, default: null },
    submitLabel: { type: String, default: 'Išsaugoti projektą' },
})

const error = (field) => props.form.errors?.[field]
const setFile = (field, event) => {
    props.form[field] = event.target.files[0] || null
    if (props.form[field] && `remove_${field}` in props.form) props.form[`remove_${field}`] = false
}
const setGallery = (event) => { props.form.gallery = [...event.target.files] }
const toggleMediaRemoval = (field) => { props.form[`remove_${field}`] = !props.form[`remove_${field}`] }
const galleryIsRemoved = (image) => props.form.remove_gallery?.includes(image)
const toggleGalleryRemoval = (image) => {
    const removed = props.form.remove_gallery || []
    props.form.remove_gallery = galleryIsRemoved(image)
        ? removed.filter((item) => item !== image)
        : [...removed, image]
}
</script>

<template>
    <form class="project-form" @submit.prevent="$emit('submit')">
        <section class="form-section intro-section">
            <div>
                <span class="section-number">01</span>
                <h2>Projekto pagrindas</h2>
                <p>Informacija, kuri matoma projekto hero zonoje ir SEO.</p>
            </div>

            <div class="fields two-columns">
                <label class="field field-wide">
                    <span>Pavadinimas *</span>
                    <input v-model="form.title" type="text" placeholder="Pavyzdžiui, Baltic Homes platforma">
                    <small v-if="error('title')">{{ error('title') }}</small>
                </label>

                <label class="field field-wide">
                    <span>Trumpa projekto frazė</span>
                    <input v-model="form.subtitle" type="text" placeholder="Naujas būdas atrasti savo namus">
                    <small v-if="error('subtitle')">{{ error('subtitle') }}</small>
                </label>

                <label class="field">
                    <span>Projekto adresas (slug) *</span>
                    <div class="input-prefix"><i>/projektai/</i><input v-model="form.slug" type="text" placeholder="baltic-homes"></div>
                    <small v-if="!error('slug')">Rašykite tik trumpą adresą, pvz. „devsprendimai“. Pilną svetainės adresą dėkite žemiau.</small>
                    <small v-if="error('slug')">{{ error('slug') }}</small>
                </label>

                <label class="field">
                    <span>Klientas</span>
                    <input v-model="form.client" type="text" placeholder="Įmonės pavadinimas">
                </label>

                <label class="field">
                    <span>Industrija</span>
                    <input v-model="form.industry" type="text" placeholder="Nekilnojamasis turtas">
                </label>

                <label class="field">
                    <span>Metai</span>
                    <input v-model="form.year" type="text" placeholder="2026">
                </label>

                <label class="field">
                    <span>Projekto trukmė</span>
                    <input v-model="form.duration" type="text" placeholder="8 savaitės">
                </label>

                <label class="field">
                    <span>Gyvo projekto nuoroda</span>
                    <input v-model="form.url" type="url" placeholder="https://...">
                    <small v-if="error('url')">{{ error('url') }}</small>
                </label>

                <label class="field field-wide">
                    <span>Trumpas aprašymas</span>
                    <textarea v-model="form.description" rows="4" placeholder="Kokia projekto esmė ir sukurta vertė?"></textarea>
                </label>
            </div>
        </section>

        <section class="form-section translation-section">
            <div>
                <span class="section-number">EN</span>
                <h2>Angliška projekto versija</h2>
                <p>Neužpildyti laukai angliškame puslapyje automatiškai naudos lietuvišką tekstą.</p>
            </div>

            <div class="fields two-columns">
                <label class="field field-wide"><span>Project title</span><input v-model="form.title_en" type="text" placeholder="Project title"></label>
                <label class="field field-wide"><span>Short project phrase</span><input v-model="form.subtitle_en" type="text" placeholder="A concise value proposition"></label>
                <label class="field"><span>Industry</span><input v-model="form.industry_en" type="text" placeholder="Real estate"></label>
                <label class="field"><span>Duration</span><input v-model="form.duration_en" type="text" placeholder="8 weeks"></label>
                <label class="field field-wide"><span>Short description</span><textarea v-model="form.description_en" rows="4" placeholder="What was built and what value does it create?"></textarea></label>
                <label class="field field-wide"><span>Services</span><textarea v-model="form.services_en" rows="3" placeholder="UX/UI design, development, automation"></textarea></label>
                <label class="field field-wide story-field"><span>Challenge</span><textarea v-model="form.challenge_en" rows="5" placeholder="What problem needed to be solved?"></textarea></label>
                <label class="field field-wide story-field"><span>Solution</span><textarea v-model="form.solution_en" rows="5" placeholder="What direction was selected and why?"></textarea></label>
                <label class="field field-wide story-field"><span>Result</span><textarea v-model="form.result_en" rows="5" placeholder="What changed after launch?"></textarea></label>
            </div>
        </section>

        <section class="form-section">
            <div>
                <span class="section-number">02</span>
                <h2>Case study istorija</h2>
                <p>Šie blokai keisis lankytojui slenkant 3D projekto puslapiu.</p>
            </div>

            <div class="fields">
                <label class="field">
                    <span>Paslaugos</span>
                    <textarea v-model="form.services" rows="3" placeholder="UX/UI dizainas, programavimas, automatizacija"></textarea>
                </label>

                <label class="field">
                    <span>Technologijos</span>
                    <textarea v-model="form.technologies" rows="3" placeholder="Laravel, Vue, Inertia, Stripe"></textarea>
                </label>

                <label class="field story-field">
                    <span>Iššūkis</span>
                    <textarea v-model="form.challenge" rows="6" placeholder="Kokią problemą reikėjo išspręsti?"></textarea>
                </label>

                <label class="field story-field">
                    <span>Sprendimas</span>
                    <textarea v-model="form.solution" rows="6" placeholder="Kokią kryptį pasirinkome ir kodėl?"></textarea>
                </label>

                <label class="field story-field">
                    <span>Rezultatas</span>
                    <textarea v-model="form.result" rows="6" placeholder="Kas pasikeitė po projekto paleidimo?"></textarea>
                </label>
            </div>
        </section>

        <section class="form-section">
            <div>
                <span class="section-number">03</span>
                <h2>Rezultato rodikliai</h2>
                <p>Naudokite tikrus skaičius arba trumpus, aiškius pasiekimus.</p>
            </div>

            <div class="metrics-editor">
                <div v-for="index in 3" :key="index" class="metric-editor">
                    <span>Rodiklis 0{{ index }}</span>
                    <input v-model="form[['metric_one_value','metric_two_value','metric_three_value'][index - 1]]" type="text" placeholder="+42%">
                    <input v-model="form[['metric_one_label','metric_two_label','metric_three_label'][index - 1]]" type="text" placeholder="Daugiau užklausų">
                </div>
            </div>
        </section>

        <section class="form-section">
            <div>
                <span class="section-number">04</span>
                <h2>Vizualinė medžiaga</h2>
                <p>Didelis darbalaukio vaizdas, mobilioji versija ir papildomi galerijos kadrai.</p>
            </div>

            <div class="media-editor">
                <div v-if="project?.thumbnail" class="existing-media desktop-preview" :class="{ 'marked-for-removal': form.remove_thumbnail }">
                    <img :src="`/storage/${project.thumbnail}`" alt="Esamas pagrindinis vaizdas">
                    <span>Esamas pagrindinis vaizdas</span>
                    <button type="button" @click="toggleMediaRemoval('thumbnail')">{{ form.remove_thumbnail ? 'Atšaukti' : 'Pašalinti' }}</button>
                </div>
                <div v-if="project?.mobile_thumbnail" class="existing-media phone-preview" :class="{ 'marked-for-removal': form.remove_mobile_thumbnail }">
                    <img :src="`/storage/${project.mobile_thumbnail}`" alt="Esamas mobilus vaizdas">
                    <span>Esamas mobilus vaizdas</span>
                    <button type="button" @click="toggleMediaRemoval('mobile_thumbnail')">{{ form.remove_mobile_thumbnail ? 'Atšaukti' : 'Pašalinti' }}</button>
                </div>

                <label class="upload-zone">
                    <span>Pagrindinis projekto vaizdas</span>
                    <strong>{{ form.thumbnail?.name || 'Pasirinkti failą' }}</strong>
                    <input type="file" accept="image/*" @change="setFile('thumbnail', $event)">
                    <small v-if="error('thumbnail')">{{ error('thumbnail') }}</small>
                </label>

                <label class="upload-zone">
                    <span>Mobilios versijos vaizdas</span>
                    <strong>{{ form.mobile_thumbnail?.name || 'Pasirinkti failą' }}</strong>
                    <input type="file" accept="image/*" @change="setFile('mobile_thumbnail', $event)">
                    <small v-if="error('mobile_thumbnail')">{{ error('mobile_thumbnail') }}</small>
                </label>

                <label class="upload-zone gallery-upload">
                    <span>3D galerija · iki 6 vaizdų</span>
                    <strong>{{ form.gallery?.length ? `${form.gallery.length} nauji failai` : 'Pridėti kelis failus' }}</strong>
                    <input type="file" accept="image/*" multiple @change="setGallery">
                    <small v-if="project?.gallery?.length && !error('gallery')" class="upload-hint">Nauji vaizdai bus pridėti prie paliktų galerijoje.</small>
                    <small v-if="error('gallery')">{{ error('gallery') }}</small>
                </label>

                <div v-if="project?.gallery?.length" class="existing-gallery">
                    <figure v-for="image in project.gallery" :key="image" :class="{ 'marked-for-removal': galleryIsRemoved(image) }">
                        <img :src="`/storage/${image}`" alt="Galerijos vaizdas">
                        <button type="button" @click="toggleGalleryRemoval(image)">{{ galleryIsRemoved(image) ? 'Atšaukti' : 'Pašalinti' }}</button>
                    </figure>
                </div>

                <label class="color-field">
                    <span>3D scenos akcento spalva</span>
                    <div><input v-model="form.accent_color" type="color"><input v-model="form.accent_color" type="text" maxlength="7"></div>
                    <small v-if="error('accent_color')">{{ error('accent_color') }}</small>
                </label>
            </div>
        </section>

        <div class="form-actions">
            <label class="featured-toggle">
                <input v-model="form.featured" type="checkbox">
                <i></i>
                <span><strong>Rodyti pagrindiniame puslapyje</strong><small>Projektas taps matomas darbų galerijoje.</small></span>
            </label>

            <button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Saugoma...' : submitLabel }} <span>→</span>
            </button>
        </div>
    </form>
</template>

<style scoped>
.project-form { display: grid; gap: 1rem; }
.form-section { display: grid; grid-template-columns: minmax(220px,.55fr) minmax(0,1.45fr); gap: 3rem; padding: 2rem; border: 1px solid rgba(255,255,255,.08); border-radius: 1.4rem; background: rgba(255,255,255,.025); }
.section-number { color: #22d3ee; font-size: .68rem; font-weight: 800; letter-spacing: .18em; }
.form-section h2 { margin-top: .7rem; font-size: 1.3rem; font-weight: 800; letter-spacing: -.025em; }
.form-section > div:first-child > p { margin-top: .65rem; color: #718096; font-size: .82rem; line-height: 1.6; }
.fields { display: grid; gap: 1rem; }
.two-columns { grid-template-columns: 1fr 1fr; }
.field-wide { grid-column: 1 / -1; }
.field { display: block; }
.field > span,
.color-field > span { display: block; margin-bottom: .45rem; color: #9dabbc; font-size: .72rem; font-weight: 750; }
.field input,
.field textarea,
.color-field input[type="text"] { width: 100%; border: 1px solid rgba(255,255,255,.09); border-radius: .78rem; color: white; background: rgba(4,9,17,.62); box-shadow: none; }
.field input { min-height: 3.1rem; padding: 0 .9rem; }
.field textarea { padding: .8rem .9rem; resize: vertical; }
.field input:focus,
.field textarea:focus,
.color-field input:focus { outline: none; border-color: rgba(34,211,238,.6); box-shadow: 0 0 0 3px rgba(34,211,238,.07); }
.field small,
.upload-zone small,
.color-field small { display: block; margin-top: .4rem; color: #fda4af; font-size: .7rem; }
.input-prefix { display: grid; grid-template-columns: auto 1fr; align-items: center; border: 1px solid rgba(255,255,255,.09); border-radius: .78rem; background: rgba(4,9,17,.62); }
.input-prefix i { padding-left: .9rem; color: #536177; font-size: .78rem; font-style: normal; }
.input-prefix input { border: 0; background: transparent; }
.story-field textarea { min-height: 9rem; }
.metrics-editor { display: grid; grid-template-columns: repeat(3,1fr); gap: .75rem; }
.metric-editor { display: grid; gap: .55rem; padding: 1rem; border: 1px solid rgba(255,255,255,.07); border-radius: 1rem; background: rgba(4,9,17,.45); }
.metric-editor > span { color: #536177; font-size: .62rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
.metric-editor input { width: 100%; min-height: 2.8rem; padding: 0 .75rem; border: 1px solid rgba(255,255,255,.08); border-radius: .65rem; color: white; background: rgba(255,255,255,.035); }
.metric-editor input:first-of-type { color: #67e8f9; font-size: 1.25rem; font-weight: 800; }
.media-editor { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
.upload-zone { position: relative; display: flex; flex-direction: column; justify-content: center; min-height: 8rem; padding: 1rem; border: 1px dashed rgba(103,232,249,.25); border-radius: 1rem; cursor: pointer; background: rgba(34,211,238,.025); }
.upload-zone > span { color: #708097; font-size: .7rem; }
.upload-zone > strong { margin-top: .35rem; color: #c8d3df; font-size: .86rem; }
.upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.gallery-upload { grid-column: 1 / -1; }
.existing-media { position: relative; overflow: hidden; min-height: 11rem; border: 1px solid rgba(255,255,255,.08); border-radius: 1rem; background: #070b12; }
.existing-media img { width: 100%; height: 100%; object-fit: cover; }
.existing-media span { position: absolute; left: .65rem; bottom: .65rem; padding: .35rem .55rem; border-radius: .5rem; background: rgba(0,0,0,.72); font-size: .65rem; }
.existing-media button,
.existing-gallery button { position: absolute; z-index: 2; right: .55rem; top: .55rem; padding: .4rem .58rem; border: 1px solid rgba(251,113,133,.35); border-radius: .55rem; color: #fecdd3; background: rgba(76,5,25,.82); font-size: .62rem; font-weight: 800; backdrop-filter: blur(10px); }
.marked-for-removal img { opacity: .25; filter: grayscale(1); }
.marked-for-removal::after { content: 'Bus pašalinta išsaugojus'; position: absolute; z-index: 1; left: 50%; top: 50%; color: #fda4af; font-size: .68rem; font-weight: 850; letter-spacing: .04em; transform: translate(-50%,-50%); white-space: nowrap; }
.marked-for-removal button { color: #cffafe; border-color: rgba(34,211,238,.35); background: rgba(8,47,73,.86); }
.phone-preview img { object-fit: contain; }
.existing-gallery { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(3,1fr); gap: .5rem; }
.existing-gallery figure { position: relative; overflow: hidden; margin: 0; border-radius: .7rem; background: #070b12; }
.existing-gallery img { width: 100%; aspect-ratio: 16/10; object-fit: cover; border-radius: .7rem; }
.upload-zone .upload-hint { color: #607087; }
.color-field { grid-column: 1 / -1; }
.color-field > div { display: grid; grid-template-columns: 3.2rem 1fr; gap: .6rem; }
.color-field input[type="color"] { width: 3.2rem; height: 3rem; padding: .25rem; border: 1px solid rgba(255,255,255,.1); border-radius: .7rem; background: rgba(4,9,17,.62); }
.form-actions { position: sticky; z-index: 10; bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .8rem .8rem .8rem 1.2rem; border: 1px solid rgba(255,255,255,.1); border-radius: 1.2rem; background: rgba(8,13,23,.9); box-shadow: 0 20px 55px rgba(0,0,0,.36); backdrop-filter: blur(18px); }
.featured-toggle { display: flex; align-items: center; gap: .7rem; cursor: pointer; }
.featured-toggle input { position: absolute; opacity: 0; }
.featured-toggle > i { position: relative; width: 2.6rem; height: 1.45rem; border-radius: 999px; background: #263247; transition: background .2s ease; }
.featured-toggle > i::after { content: ''; position: absolute; width: 1rem; height: 1rem; left: .23rem; top: .22rem; border-radius: 50%; background: #8593a8; transition: transform .2s ease,background .2s ease; }
.featured-toggle input:checked + i { background: rgba(34,211,238,.25); }
.featured-toggle input:checked + i::after { background: #22d3ee; transform: translateX(1.15rem); }
.featured-toggle span { display: flex; flex-direction: column; }
.featured-toggle strong { font-size: .78rem; }
.featured-toggle small { color: #607087; font-size: .65rem; }
.form-actions button { display: flex; align-items: center; gap: 1rem; min-height: 3.1rem; padding: 0 1.2rem; border-radius: .85rem; color: #061016; background: #22d3ee; font-size: .8rem; font-weight: 850; }
.form-actions button:disabled { opacity: .6; }

@media (max-width: 900px) {
    .form-section { grid-template-columns: 1fr; gap: 1.5rem; }
}

@media (max-width: 640px) {
    .form-section { padding: 1.2rem; }
    .two-columns,
    .metrics-editor,
    .media-editor { grid-template-columns: 1fr; }
    .field-wide,
    .gallery-upload,
    .color-field,
    .existing-gallery { grid-column: auto; }
    .existing-gallery { grid-template-columns: 1fr 1fr; }
    .form-actions { bottom: .7rem; flex-direction: column; align-items: stretch; padding: .8rem; }
    .form-actions button { justify-content: space-between; }
}
</style>
