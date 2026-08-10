<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { useLocale } from '@/composables/useLocale'

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['privacy', 'cookies', 'terms'].includes(value),
    },
})

const { isEnglish, localize } = useLocale()
const updated = '2026-08-10'

const documents = {
    lt: {
        privacy: {
            eyebrow: 'Teisinė informacija · 01',
            title: 'Privatumo politika',
            description: 'Kaip DevSprendimai renka, naudoja ir saugo jūsų asmens duomenis.',
            intro: 'Ši politika paaiškina, kokius asmens duomenis tvarkau, kai lankotės devsprendimai.lt, siunčiate projekto užklausą ar tampate klientu.',
            highlights: ['Aiškūs tikslai', 'Ribotas saugojimas', 'Jokių duomenų pardavimų'],
            sections: [
                { title: '1. Duomenų valdytojas', paragraphs: ['Duomenų valdytojas – Aivaras Razvodovskis, vykdantis individualią veiklą pagal pažymą Nr. 1400067. Korespondencijos adresas: Pramonės pr. 28, Kaunas, Lietuva. El. paštas: info@devsprendimai.lt, telefonas: +370 606 06227.'] },
                { title: '2. Kokius duomenis tvarkau ir kodėl', paragraphs: ['Kai pateikiate užklausą, tvarkau vardą, el. paštą, telefono numerį, įmonės pavadinimą, pasirinktą paslaugą ir žinutę. Šie duomenys naudojami atsakyti, įvertinti projektą, parengti pasiūlymą ir imtis veiksmų prieš sudarant sutartį.', 'Svetainės saugumui ir bendrai lankomumo statistikai registruojamas IP adresas, lankytas adresas, naršyklės informacija bei apsilankymo laikas. Duomenys nenaudojami reklaminei profiliacijai.'], bullets: ['Užklausos ir ikisutartinis bendravimas – BDAR 6 str. 1 d. b p.', 'Bendravimas su įmonių atstovais, svetainės saugumas ir vidinė statistika – teisėtas interesas pagal BDAR 6 str. 1 d. f p.', 'Sutarčių ir apskaitos duomenys – sutarties vykdymas ir teisinių prievolių laikymasis.'] },
                { title: '3. Saugojimo terminai', bullets: ['Kontaktinės ir projekto užklausos saugomos iki 2 metų nuo paskutinio bendravimo, nebent jos tampa sutartinių santykių dalimi arba duomenų reikia teisiniams reikalavimams.', 'Techniniai lankomumo duomenys saugomi iki 90 dienų.', 'Sutarčių, sąskaitų ir apskaitos dokumentai saugomi tiek, kiek reikalauja taikomi teisės aktai.'] },
                { title: '4. Duomenų gavėjai', paragraphs: ['Duomenis gali tvarkyti tik paslaugoms būtini partneriai: Hostinger (svetainės ir el. pašto infrastruktūra), techninės priežiūros ar apskaitos paslaugų teikėjai. Jie duomenis tvarko pagal mano nurodymus ir taikomas apsaugos priemones. Duomenys neparduodami.', 'Duomenys gali būti pateikti valstybės institucijoms, kai tai privaloma pagal teisės aktus. Perdavimas už Europos ekonominės erdvės ribų galimas tik taikant BDAR numatytą teisėtą perdavimo pagrindą ir apsaugos priemones.'] },
                { title: '5. Jūsų teisės', paragraphs: ['Turite teisę prašyti susipažinti su savo duomenimis, juos ištaisyti, ištrinti ar apriboti jų tvarkymą, nesutikti su tvarkymu, pagrįstu teisėtu interesu, ir, kai taikoma, gauti duomenis perkeliamu formatu. Prašymą pateikite el. paštu info@devsprendimai.lt. Prieš įvykdant prašymą gali būti paprašyta patvirtinti tapatybę.'] },
                { title: '6. Skundai ir saugumas', paragraphs: ['Jeigu manote, kad duomenys tvarkomi netinkamai, pirmiausia kviečiu susisiekti tiesiogiai. Taip pat turite teisę pateikti skundą Valstybinei duomenų apsaugos inspekcijai.', 'Taikomos prieigos kontrolės, saugaus ryšio, atsarginių kopijų ir kitos pagrįstos techninės bei organizacinės priemonės. Vis dėlto nė vienas perdavimo ar saugojimo būdas negali užtikrinti absoliutaus saugumo.'], links: [{ label: 'Valstybinė duomenų apsaugos inspekcija', href: 'https://vdai.lrv.lt/' }] },
                { title: '7. Politikos pakeitimai', paragraphs: ['Politika gali būti atnaujinama pasikeitus svetainei, paslaugoms ar teisės aktams. Aktualios redakcijos data visada skelbiama šio puslapio viršuje.'] },
            ],
        },
        cookies: {
            eyebrow: 'Teisinė informacija · 02',
            title: 'Slapukų politika',
            description: 'Informacija apie devsprendimai.lt naudojamus būtinuosius slapukus.',
            intro: 'Šiuo metu svetainė nenaudoja reklamos, „Google Analytics“, „Meta Pixel“ ar kitų nebūtinų sekimo slapukų. Naudojami tik saugiam svetainės ir formų veikimui būtini slapukai.',
            highlights: ['Tik būtinieji', 'Jokios reklaminės profiliacijos', 'Išankstinis sutikimas nebūtinas'],
            sections: [
                { title: '1. Kas yra slapukai', paragraphs: ['Slapukai – tai nedideli tekstiniai failai, kuriuos svetainė išsaugo jūsų įrenginyje. Jie padeda užtikrinti saugią sesiją, apsaugoti formas ir palaikyti pagrindines svetainės funkcijas.'] },
                { title: '2. Naudojami slapukai', table: [
                    ['Slapukas', 'Paskirtis', 'Trukmė'],
                    ['laravel_session', 'Būtinas sesijos veikimui ir saugumui.', 'Iki 120 min. neveiklos'],
                    ['XSRF-TOKEN', 'Apsaugo formas nuo neteisėtų užklausų (CSRF).', 'Sesijos metu'],
                ] },
                { title: '3. Teisinis pagrindas', paragraphs: ['Būtinieji slapukai reikalingi jūsų aiškiai prašomai paslaugai suteikti ir svetainės saugumui užtikrinti, todėl išankstinis sutikimas jiems nereikalingas. Apie jų naudojimą vis tiek pateikiama ši informacija.'] },
                { title: '4. Naršyklės nustatymai', paragraphs: ['Slapukus galite ištrinti arba blokuoti naršyklės nustatymuose. Užblokavus būtinuosius slapukus kontaktinė forma, prisijungimas ar kitos apsaugotos funkcijos gali neveikti tinkamai.'] },
                { title: '5. Jei bus įdiegta analitika', paragraphs: ['Prieš įdiegiant analitinius ar rinkodaros slapukus ši politika bus atnaujinta ir bus parodytas pasirinkimo langas. Nebūtinieji slapukai nebus įrašomi, kol aiškiai nepasirinksite „Sutinku“; „Nesutinku“ bus toks pat lengvai pasiekiamas pasirinkimas.'], links: [{ label: 'VDAI informacija apie slapukus', href: 'https://vdai.lrv.lt/lt/naujienos/slapuku-naudojimo-interneto-svetainese-apibendrinimas/' }] },
                { title: '6. Kontaktai', paragraphs: ['Klausimus apie slapukus siųskite adresu info@devsprendimai.lt. Duomenų valdytojo rekvizitai pateikti Privatumo politikoje.'] },
            ],
        },
        terms: {
            eyebrow: 'Teisinė informacija · 03',
            title: 'Paslaugų teikimo sąlygos',
            description: 'Bendros DevSprendimai skaitmeninių paslaugų teikimo sąlygos.',
            intro: 'Šios sąlygos taikomos interneto svetainių, el. parduotuvių, CRM, aplikacijų, automatizavimo, priežiūros ir kitoms individualiai sutartoms skaitmeninėms paslaugoms.',
            highlights: ['Aiški projekto apimtis', 'Skaidrūs mokėjimai', 'Vartotojų teisės'],
            sections: [
                { title: '1. Paslaugų teikėjas', paragraphs: ['Aivaras Razvodovskis, individualios veiklos pažymos Nr. 1400067, Pramonės pr. 28, Kaunas, Lietuva. El. paštas: info@devsprendimai.lt, tel. +370 606 06227.'] },
                { title: '2. Sąlygų taikymas ir sutarties sudarymas', paragraphs: ['Svetainėje pateikta informacija ir užklausos forma savaime nėra įpareigojantis pasiūlymas ar sutartis. Sutartis sudaroma šalims raštu patvirtinus individualų pasiūlymą, darbų apimtį, kainą, terminus ir mokėjimo tvarką. Individualaus pasiūlymo ar sutarties nuostatos turi pirmenybę prieš šias bendrąsias sąlygas.'] },
                { title: '3. Apimtis, kaina ir mokėjimai', paragraphs: ['Svetainėje rodomos kainos yra orientacinės pradinės kainos, jeigu aiškiai nenurodyta kitaip. Galutinė kaina priklauso nuo funkcijų, integracijų, turinio ir terminų bei patvirtinama prieš pradedant darbus.', 'Mokėjimų etapai ir terminai nustatomi pasiūlyme. Vėluojant atsiskaityti darbai gali būti pagrįstai sustabdyti, apie tai iš anksto informavus klientą. Papildomi darbai atliekami tik suderinus jų apimtį ir kainą.'] },
                { title: '4. Kliento pareigos', bullets: ['Laiku pateikti teisingą informaciją, turinį, prieigas ir sprendimus.', 'Užtikrinti, kad pateiktą tekstą, vaizdus, prekių ženklus ir kitą medžiagą klientas turi teisę naudoti.', 'Per sutartą laiką patikrinti pateiktus etapus ir pateikti vieningą grįžtamąjį ryšį.', 'Saugiai laikyti perduotus prisijungimo duomenis ir informuoti apie saugumo incidentus.'] },
                { title: '5. Terminai, pakeitimai ir priėmimas', paragraphs: ['Terminai skaičiuojami nuo sutarto avanso ir visos darbui būtinos informacijos gavimo, jei pasiūlyme nenustatyta kitaip. Klientui vėluojant pateikti medžiagą ar sprendimus, terminai gali būti atitinkamai pratęsti.', 'Į kainą įtrauktas korekcijų skaičius nustatomas individualiame pasiūlyme. Apimties pakeitimai gali keisti kainą ir terminus. Apie pastebėtus trūkumus klientas turi pranešti kuo greičiau ir suteikti pagrįstą galimybę juos ištaisyti.'] },
                { title: '6. Intelektinė nuosavybė', paragraphs: ['Individualiai sukurtų ir visiškai apmokėtų rezultatų turtinės teisės klientui perduodamos tokia apimtimi, kuri nustatyta pasiūlyme ar sutartyje. Iki visiško atsiskaitymo rezultatai negali būti naudojami komerciniam paleidimui, jei raštu nesusitarta kitaip.', 'Trečiųjų šalių programinei įrangai, šriftams, nuotraukoms, įskiepiams ir atvirojo kodo komponentams taikomos jų licencijos. Bendrosios žinios, metodai ir anksčiau sukurti pakartotinai naudojami komponentai lieka jų teisėto savininko nuosavybe.'] },
                { title: '7. Vartotojų teisė atsisakyti nuotolinės sutarties', paragraphs: ['Fizinis asmuo, sudarantis sutartį asmeniniais tikslais, paprastai turi teisę per 14 dienų nuo nuotolinės paslaugų sutarties sudarymo jos atsisakyti nenurodydamas priežasties. Apie atsisakymą pakanka aiškiai pranešti el. paštu info@devsprendimai.lt.', 'Jeigu vartotojas aiškiai paprašo pradėti darbus nepasibaigus 14 dienų terminui ir vėliau atsisako sutarties, jis sumoka proporcingai už iki atsisakymo suteiktas paslaugas. Teisė atsisakyti visiškai suteiktos paslaugos pasibaigia tik vartotojui iš anksto aiškiai sutikus pradėti vykdymą ir patvirtinus, kad visiškai įvykdžius sutartį ši teisė bus prarasta. Konkretaus užsakymo dokumentuose pateikiama visa privaloma informacija ir pasirinkimai.'], links: [{ label: 'ES informacija apie teisę atsisakyti sutarties', href: 'https://europa.eu/youreurope/citizens/consumers/shopping/returns/index_en.htm' }] },
                { title: '8. Atsakomybė ir paslaugų tęstinumas', paragraphs: ['Paslaugos teikiamos profesionaliai ir pagal sutartą specifikaciją. Paslaugų teikėjas neatsako už kliento pateiktos medžiagos teisėtumą, kliento ar trečiųjų šalių pakeitimus, išorinių platformų sutrikimus ar aplinkybes, kurių pagrįstai negali kontroliuoti.', 'Jokia šių sąlygų nuostata neriboja atsakomybės, kurios negalima riboti pagal įstatymus, ir nepanaikina privalomų vartotojų teisių. Atsarginės kopijos, priežiūra ir reagavimo terminai taikomi tik tada, kai jie įtraukti į pasirinktą planą ar atskirą susitarimą.'] },
                { title: '9. Sutarties nutraukimas', paragraphs: ['Sutarties nutraukimo pagrindai ir atsiskaitymas nustatomi individualiame susitarime. Nutraukus projektą atsiskaitoma už faktiškai atliktus ir perduodamus darbus bei pagrįstas, iš anksto suderintas išlaidas. Vartotojams visais atvejais taikomos imperatyvios vartotojų apsaugos taisyklės.'] },
                { title: '10. Skundai ir ginčai', paragraphs: ['Klausimus ar pretenzijas siųskite info@devsprendimai.lt. Vartotojo rašytinis prašymas išnagrinėjamas nemokamai ir motyvuotas atsakymas pateikiamas ne vėliau kaip per 14 dienų.', 'Jeigu atsakymas netenkina arba per 14 dienų jo negaunate, galite kreiptis į Valstybinę vartotojų teisių apsaugos tarnybą, A. Goštauto g. 12, 01108 Vilnius, arba pateikti prašymą per Vartotojų teisių informacinę sistemą. Ginčams taikoma Lietuvos Respublikos teisė, neapribojant vartotojui privalomai taikomos apsaugos.'], links: [{ label: 'Valstybinė vartotojų teisių apsaugos tarnyba', href: 'https://vvtat.lrv.lt/' }, { label: 'Pateikti prašymą per VTIS', href: 'https://www.vtis.lt/' }] },
                { title: '11. Sąlygų pakeitimai', paragraphs: ['Atnaujintos sąlygos taikomos naujiems užsakymams nuo jų paskelbimo. Jau sudarytoms sutartims pakeitimai taikomi tik teisės aktų nustatyta tvarka arba šalims susitarus.'] },
            ],
        },
    },
    en: {
        privacy: {
            eyebrow: 'Legal information · 01', title: 'Privacy Policy', description: 'How DevSprendimai collects, uses and protects personal data.',
            intro: 'This policy explains how I process personal data when you visit devsprendimai.lt, submit a project enquiry or become a client.', highlights: ['Clear purposes', 'Limited retention', 'No sale of data'],
            sections: [
                { title: '1. Data controller', paragraphs: ['The controller is Aivaras Razvodovskis, operating under individual activity certificate No. 1400067. Correspondence address: Pramonės pr. 28, Kaunas, Lithuania. Email: info@devsprendimai.lt, phone: +370 606 06227.'] },
                { title: '2. Data, purposes and legal bases', paragraphs: ['When you submit an enquiry, I process your name, email, phone number, company, selected service and message to respond, assess the project, prepare a proposal and take steps before entering into a contract.', 'For website security and basic internal statistics, the IP address, visited URL, browser information and visit time are logged. These data are not used for advertising profiles.'], bullets: ['Enquiries and pre-contract communication – Article 6(1)(b) GDPR.', 'Communication with company representatives, website security and internal statistics – legitimate interests under Article 6(1)(f) GDPR.', 'Contract and accounting data – contract performance and compliance with legal obligations.'] },
                { title: '3. Retention', bullets: ['Enquiries are retained for up to 2 years after the last communication, unless they form part of a contract or are needed for legal claims.', 'Technical visit data are retained for up to 90 days.', 'Contracts, invoices and accounting records are retained for the periods required by applicable law.'] },
                { title: '4. Recipients and transfers', paragraphs: ['Data may be processed by providers necessary for the service, including Hostinger (website and email infrastructure), technical maintenance and accounting providers. They process data under appropriate obligations. Personal data are not sold.', 'Data may be disclosed to public authorities where legally required. Transfers outside the EEA take place only with a lawful GDPR transfer mechanism and appropriate safeguards.'] },
                { title: '5. Your rights', paragraphs: ['You may request access, correction, deletion or restriction, object to processing based on legitimate interests and, where applicable, request data portability. Contact info@devsprendimai.lt. Identity verification may be required before fulfilling a request.'] },
                { title: '6. Complaints and security', paragraphs: ['If you believe your data are processed improperly, please contact me first. You may also lodge a complaint with the Lithuanian State Data Protection Inspectorate.', 'Reasonable access controls, secure connections, backups and organisational safeguards are used, although no transmission or storage method can guarantee absolute security.'], links: [{ label: 'Lithuanian State Data Protection Inspectorate', href: 'https://vdai.lrv.lt/en/' }] },
                { title: '7. Changes', paragraphs: ['This policy may be updated when the website, services or law changes. The current revision date is always shown at the top of this page.'] },
            ],
        },
        cookies: {
            eyebrow: 'Legal information · 02', title: 'Cookie Policy', description: 'Information about essential cookies used by devsprendimai.lt.',
            intro: 'The website currently does not use advertising, Google Analytics, Meta Pixel or other non-essential tracking cookies. Only cookies required for secure site and form operation are used.', highlights: ['Essential only', 'No advertising profiles', 'No prior consent required'],
            sections: [
                { title: '1. What cookies are', paragraphs: ['Cookies are small text files saved on your device. They support a secure session, protect forms and enable core website functionality.'] },
                { title: '2. Cookies used', table: [['Cookie', 'Purpose', 'Duration'], ['laravel_session', 'Required for session operation and security.', 'Up to 120 min. of inactivity'], ['XSRF-TOKEN', 'Protects forms against unauthorised requests (CSRF).', 'During the session']] },
                { title: '3. Legal basis', paragraphs: ['Essential cookies are necessary to provide a service explicitly requested by you and to secure the website. Prior consent is therefore not required, but this information is provided for transparency.'] },
                { title: '4. Browser settings', paragraphs: ['You can delete or block cookies in your browser. Blocking essential cookies may prevent contact forms, sign-in or other protected functions from working correctly.'] },
                { title: '5. Future analytics', paragraphs: ['Before any analytics or marketing cookies are introduced, this policy will be updated and a consent interface will be shown. Non-essential cookies will remain disabled until you actively accept them, with an equally accessible refusal option.'], links: [{ label: 'Lithuanian DPA guidance on cookies', href: 'https://vdai.lrv.lt/lt/naujienos/slapuku-naudojimo-interneto-svetainese-apibendrinimas/' }] },
                { title: '6. Contact', paragraphs: ['Questions about cookies may be sent to info@devsprendimai.lt. Full controller details are available in the Privacy Policy.'] },
            ],
        },
        terms: {
            eyebrow: 'Legal information · 03', title: 'Terms of Service', description: 'General terms for DevSprendimai digital services.',
            intro: 'These terms apply to websites, e-commerce, CRM, applications, automation, maintenance and other digital services agreed individually with a client.', highlights: ['Defined project scope', 'Transparent payments', 'Consumer rights'],
            sections: [
                { title: '1. Service provider', paragraphs: ['Aivaras Razvodovskis, individual activity certificate No. 1400067, Pramonės pr. 28, Kaunas, Lithuania. Email: info@devsprendimai.lt, phone: +370 606 06227.'] },
                { title: '2. Application and contract formation', paragraphs: ['Website information and the enquiry form are not a binding offer or contract. A contract is formed when the parties confirm in writing an individual proposal covering scope, price, timing and payment. The individual proposal or contract prevails over these general terms.'] },
                { title: '3. Scope, price and payment', paragraphs: ['Website prices are indicative starting prices unless expressly stated otherwise. The final price depends on functionality, integrations, content and timing and is confirmed before work starts.', 'Payment stages are set out in the proposal. Work may be reasonably paused after notice if payment is overdue. Additional work is performed only after its scope and price are agreed.'] },
                { title: '4. Client responsibilities', bullets: ['Provide accurate information, content, access and decisions on time.', 'Ensure the client has rights to use all supplied text, images, trademarks and materials.', 'Review milestones and provide consolidated feedback within the agreed time.', 'Protect credentials and report security incidents.'] },
                { title: '5. Timing, changes and acceptance', paragraphs: ['Unless agreed otherwise, timelines begin after receipt of the agreed advance payment and all information needed for the work. Client delays may extend the schedule accordingly.', 'Included revisions are defined in the proposal. Scope changes may affect price and timing. The client should report defects promptly and allow a reasonable opportunity to correct them.'] },
                { title: '6. Intellectual property', paragraphs: ['Economic rights to bespoke, fully paid deliverables are transferred to the extent stated in the proposal or contract. Deliverables may not be commercially launched before full payment unless agreed otherwise.', 'Third-party software, fonts, images, plugins and open-source components remain subject to their licences. General know-how, methods and pre-existing reusable components remain the property of their lawful owner.'] },
                { title: '7. Consumer withdrawal rights', paragraphs: ['An individual contracting for personal purposes generally has 14 days from conclusion of a distance service contract to withdraw without giving a reason. A clear notice sent to info@devsprendimai.lt is sufficient.', 'If the consumer expressly requests performance to begin during the 14-day period and later withdraws, the consumer pays a proportionate amount for services supplied. The right is lost after full performance only where the consumer gave prior express consent and acknowledged that full performance would end the right. The order documents will provide all mandatory information and choices.'], links: [{ label: 'EU information on withdrawal rights', href: 'https://europa.eu/youreurope/citizens/consumers/shopping/returns/index_en.htm' }] },
                { title: '8. Liability and continuity', paragraphs: ['Services are provided professionally and to the agreed specification. The provider is not responsible for the legality of client materials, changes by the client or third parties, external platform failures or circumstances outside reasonable control.', 'Nothing in these terms excludes liability that cannot legally be excluded or limits mandatory consumer rights. Backups, maintenance and response times apply only when included in the selected plan or a separate agreement.'] },
                { title: '9. Termination', paragraphs: ['Termination and settlement are governed by the individual agreement. On termination, payment is due for work actually completed and transferred and for reasonable pre-agreed expenses. Mandatory consumer protections always apply.'] },
                { title: '10. Complaints and disputes', paragraphs: ['Send questions or claims to info@devsprendimai.lt. A consumer’s written request will be reviewed free of charge and a reasoned response provided within 14 days.', 'If the response is unsatisfactory or not received within 14 days, consumers may contact the Lithuanian State Consumer Rights Protection Authority, A. Goštauto g. 12, 01108 Vilnius, or submit a request through VTIS. Lithuanian law applies without limiting mandatory consumer protection.'], links: [{ label: 'State Consumer Rights Protection Authority', href: 'https://vvtat.lrv.lt/en/' }, { label: 'Submit a request through VTIS', href: 'https://www.vtis.lt/' }] },
                { title: '11. Changes', paragraphs: ['Updated terms apply to new orders from publication. Existing contracts change only as permitted by law or agreed by the parties.'] },
            ],
        },
    },
}

const document = computed(() => documents[isEnglish.value ? 'en' : 'lt'][props.type])
const navItems = computed(() => [
    ['privacy', isEnglish.value ? 'Privacy' : 'Privatumas', '/privatumo-politika'],
    ['cookies', isEnglish.value ? 'Cookies' : 'Slapukai', '/slapuku-politika'],
    ['terms', isEnglish.value ? 'Terms' : 'Sąlygos', '/paslaugu-teikimo-salygos'],
])
</script>

<template>
    <SeoHead :title="`${document.title} | DevSprendimai`" :description="document.description" />

    <article class="legal-page">
        <div class="legal-horizon" aria-hidden="true"></div>
        <div class="site-container">
            <nav class="legal-nav" :aria-label="isEnglish ? 'Legal documents' : 'Teisiniai dokumentai'">
                <Link v-for="item in navItems" :key="item[0]" :href="localize(item[2])" :class="{ active: item[0] === type }">{{ item[1] }}</Link>
            </nav>

            <header class="legal-hero">
                <span>{{ document.eyebrow }}</span>
                <h1>{{ document.title }}</h1>
                <p>{{ document.intro }}</p>
                <div class="legal-meta">
                    <span>{{ isEnglish ? 'Effective' : 'Galioja nuo' }} · {{ updated }}</span>
                    <i></i>
                    <span>DS / LEGAL</span>
                </div>
                <div class="legal-highlights">
                    <div v-for="(highlight, index) in document.highlights" :key="highlight"><small>0{{ index + 1 }}</small>{{ highlight }}</div>
                </div>
            </header>

            <div class="legal-layout">
                <aside>
                    <span>{{ isEnglish ? 'Contents' : 'Turinys' }}</span>
                    <a v-for="(section, index) in document.sections" :key="section.title" :href="`#legal-${index + 1}`"><small>0{{ index + 1 }}</small>{{ section.title.replace(/^\d+\.\s*/, '') }}</a>
                </aside>

                <div class="legal-content">
                    <section v-for="(section, index) in document.sections" :id="`legal-${index + 1}`" :key="section.title">
                        <span class="section-index">/ {{ String(index + 1).padStart(2, '0') }}</span>
                        <h2>{{ section.title }}</h2>
                        <p v-for="paragraph in section.paragraphs" :key="paragraph">{{ paragraph }}</p>
                        <ul v-if="section.bullets">
                            <li v-for="bullet in section.bullets" :key="bullet">{{ bullet }}</li>
                        </ul>
                        <div v-if="section.table" class="legal-table-wrap">
                            <table>
                                <thead><tr><th v-for="cell in section.table[0]" :key="cell">{{ cell }}</th></tr></thead>
                                <tbody><tr v-for="row in section.table.slice(1)" :key="row[0]"><td v-for="cell in row" :key="cell">{{ cell }}</td></tr></tbody>
                            </table>
                        </div>
                        <div v-if="section.links" class="legal-links">
                            <a v-for="link in section.links" :key="link.href" :href="link.href" target="_blank" rel="noopener noreferrer">{{ link.label }} <i>↗</i></a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </article>
</template>

<style scoped>
.legal-page { position:relative; min-height:100vh; overflow:hidden; padding:8rem 0 7rem; }
.legal-horizon { position:absolute; inset:0 0 auto; height:44rem; opacity:.45; background:radial-gradient(circle at 75% 18%,rgba(34,211,238,.15),transparent 30%),linear-gradient(rgba(34,211,238,.055) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,.055) 1px,transparent 1px); background-size:auto,52px 52px,52px 52px; mask-image:linear-gradient(to bottom,black,transparent); pointer-events:none; }
.legal-nav { position:relative; z-index:2; display:flex; width:max-content; max-width:100%; padding:.35rem; overflow-x:auto; border:1px solid rgba(255,255,255,.09); border-radius:999px; background:rgba(8,14,24,.7); backdrop-filter:blur(16px); }
.legal-nav a { padding:.7rem 1rem; border-radius:999px; color:#718097; font-size:.68rem; font-weight:800; white-space:nowrap; transition:.2s ease; }
.legal-nav a:hover { color:#dbeafe; }
.legal-nav a.active { color:#041016; background:#22d3ee; }
.legal-hero { position:relative; z-index:1; max-width:920px; padding:clamp(3rem,8vw,7rem) 0 5rem; }
.legal-hero > span { color:#67e8f9; font-size:.7rem; font-weight:850; letter-spacing:.2em; text-transform:uppercase; }
.legal-hero h1 { margin-top:1rem; font-size:clamp(3.4rem,8vw,7.5rem); font-weight:850; letter-spacing:-.075em; line-height:.9; }
.legal-hero > p { max-width:760px; margin-top:1.8rem; color:#91a0b5; font-size:clamp(1rem,1.5vw,1.18rem); line-height:1.8; }
.legal-meta { display:flex; align-items:center; gap:.8rem; margin-top:1.5rem; color:#506077; font-size:.62rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
.legal-meta i { width:.3rem; height:.3rem; border-radius:50%; background:#22d3ee; box-shadow:0 0 12px #22d3ee; }
.legal-highlights { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin-top:2.8rem; }
.legal-highlights div { display:flex; align-items:center; gap:.7rem; min-height:4rem; padding:0 1rem; border:1px solid rgba(255,255,255,.08); border-radius:1rem; color:#aebacc; background:rgba(8,15,26,.62); font-size:.76rem; font-weight:750; }
.legal-highlights small { color:#22d3ee; font-size:.58rem; font-weight:900; }
.legal-layout { position:relative; display:grid; grid-template-columns:250px minmax(0,760px); align-items:start; justify-content:space-between; gap:clamp(3rem,8vw,8rem); }
.legal-layout aside { position:sticky; top:7rem; display:flex; flex-direction:column; border-top:1px solid rgba(255,255,255,.1); }
.legal-layout aside > span { padding:1.1rem 0; color:#526178; font-size:.62rem; font-weight:850; letter-spacing:.17em; text-transform:uppercase; }
.legal-layout aside a { display:grid; grid-template-columns:2rem 1fr; padding:.7rem 0; color:#718097; font-size:.72rem; line-height:1.35; transition:color .2s ease; }
.legal-layout aside a:hover { color:#67e8f9; }
.legal-layout aside small { color:#344258; font-size:.56rem; }
.legal-content section { position:relative; padding:0 0 3.5rem 3.2rem; scroll-margin-top:7rem; }
.legal-content section + section { padding-top:3.5rem; border-top:1px solid rgba(255,255,255,.08); }
.section-index { position:absolute; left:0; top:.35rem; color:#22d3ee; font-size:.58rem; font-weight:900; letter-spacing:.08em; }
.legal-content section + section .section-index { top:3.85rem; }
.legal-content h2 { margin-bottom:1.25rem; color:#edf7ff; font-size:clamp(1.45rem,2.5vw,2rem); font-weight:800; letter-spacing:-.035em; }
.legal-content p { color:#8998ad; line-height:1.85; }
.legal-content p + p { margin-top:1rem; }
.legal-content ul { display:grid; gap:.75rem; margin-top:1.25rem; }
.legal-content li { position:relative; padding-left:1.3rem; color:#8998ad; line-height:1.75; }
.legal-content li::before { content:''; position:absolute; left:0; top:.7rem; width:.35rem; height:.35rem; border:1px solid #22d3ee; transform:rotate(45deg); }
.legal-links { display:flex; flex-wrap:wrap; gap:.65rem; margin-top:1.4rem; }
.legal-links a { padding:.65rem .85rem; border:1px solid rgba(34,211,238,.18); border-radius:.7rem; color:#74ddea; background:rgba(34,211,238,.04); font-size:.7rem; font-weight:750; }
.legal-links i { font-style:normal; }
.legal-table-wrap { margin-top:1.25rem; overflow-x:auto; border:1px solid rgba(255,255,255,.08); border-radius:1rem; }
.legal-table-wrap table { width:100%; min-width:560px; border-collapse:collapse; }
.legal-table-wrap th,.legal-table-wrap td { padding:1rem; border-bottom:1px solid rgba(255,255,255,.07); color:#8998ad; font-size:.75rem; line-height:1.5; text-align:left; }
.legal-table-wrap th { color:#67e8f9; background:rgba(34,211,238,.035); font-size:.62rem; letter-spacing:.08em; text-transform:uppercase; }
.legal-table-wrap tr:last-child td { border-bottom:0; }
@media (max-width:900px) {
    .legal-layout { grid-template-columns:1fr; }
    .legal-layout aside { position:relative; top:auto; display:none; }
}
@media (max-width:640px) {
    .legal-page { padding:6rem 0 5rem; }
    .legal-nav { width:100%; }
    .legal-nav a { flex:1; text-align:center; }
    .legal-hero { padding:3.5rem 0 3rem; }
    .legal-hero h1 { font-size:clamp(3rem,15vw,4.3rem); overflow-wrap:anywhere; }
    .legal-highlights { grid-template-columns:1fr; }
    .legal-highlights div { min-height:3.5rem; }
    .legal-content section,.legal-content section + section { padding-left:0; padding-top:3rem; padding-bottom:3rem; }
    .legal-content section:first-child { padding-top:0; }
    .section-index,.legal-content section + section .section-index { position:relative; display:block; top:auto; margin-bottom:.6rem; }
}
</style>
