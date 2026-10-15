// Buyer guidance shared by the interactive pages and their initial HTML.
// Related links use existing service slugs; the renderer adds the current locale.
export const serviceGuides = {
    lt: {
        'interneto-svetaines': {
            title: 'Kokią svetainę verta kurti jūsų verslui?',
            intro: 'Esu Aivaras, svetainių kūrėjas Kaune. Su klientais visoje Lietuvoje dirbu nuotoliu: pirmiausia aptariame, ką lankytojas turi sužinoti ir atlikti, o tuomet pasirenkame tinkamą svetainės apimtį.',
            considerations: [
                { title: 'Įmonės svetainė ar nukreipimo puslapis?', text: 'Vienam pasiūlymui ar kampanijai gali pakakti vieno puslapio. Jei turite skirtingas paslaugas, darbus ir auditorijas, atskiri turiningi puslapiai padeda lankytojui rasti jam aktualią informaciją.' },
                { title: 'WordPress ar individualus programavimas?', text: 'WordPress gali tikti įprastai turinio svetainei, kurią norėsite redaguoti patys. Nestandartiniams skaičiavimams, paskyroms ar procesams vertiname individualų sprendimą. Pasirinkimą lemia funkcijos ir priežiūros poreikis, ne vien dizainas.' },
                { title: 'Atnaujinant svarbu išsaugoti tai, kas veikia', text: 'Prieš keičiant esamą svetainę įvertiname jos turinį, adresus ir svarbiausius lankytojų kelius. Jei keičiasi adresai, į darbų apimtį įtraukiame jų atitikmenų ir nukreipimų planą.' },
            ],
            faq: [
                { q: 'Nuo ko priklauso svetainės kūrimo kaina?', a: 'Nuo puslapių ir kalbų skaičiaus, dizaino, turinio parengimo bei funkcijų: formų, rezervacijų ar kitų integracijų. Skelbiamos kainos yra startinės; konkrečią apimtį, galutinę kainą ir terminą patvirtiname pasiūlyme prieš pradėdami darbus.' },
                { q: 'Ar į kainą įeina domenas, hostingas ir tekstai?', a: 'Domeną, hostingą, mokamas licencijas, tekstų ir nuotraukų parengimą aptariame atskirai. Pasiūlyme atskiriame vienkartinius kūrimo darbus nuo periodinių išlaidų ir nurodome, kokią medžiagą pateikiate jūs.' },
                { q: 'Ar galėsiu pats keisti svetainės turinį?', a: 'Taip, jei į sutartą apimtį įtraukiamas turinio valdymas. Iš anksto numatome redaguojamus tekstus, vaizdus ir kitus laukus, o perduodant svetainę parodome, kaip juos prižiūrėti.' },
                { q: 'Ar techninis SEO garantuoja vietą Google paieškoje?', a: 'Ne. Tvarkingi puslapių pavadinimai, indeksavimo nustatymai, struktūra ir greitis sudaro techninį pagrindą. Indeksavimą bei pozicijas lemia ir turinio naudingumas, konkurencija bei kiti veiksniai. Atnaujinant svetainę taip pat svarbu suplanuoti pasikeitusių adresų nukreipimus.' },
            ],
            related: [
                { slug: 'el-parduotuves', label: 'El. parduotuvių kūrimas, kai reikia parduoti internetu' },
                { slug: 'svetainiu-prieziura', label: 'Svetainių priežiūra po paleidimo' },
                { slug: 'aplikaciju-kurimas', label: 'Aplikacijų kūrimas nestandartinėms funkcijoms' },
            ],
        },
        'el-parduotuves': {
            title: 'Kas svarbu prieš kuriant el. parduotuvę?',
            intro: 'Parduotuvės apimtį lemia ne tik dizainas. Aptariame prekių katalogą, variantus, atsiskaitymo būdus ir kasdienį užsakymų tvarkymą, kad sprendimas atitiktų jūsų prekybos modelį.',
            considerations: [
                { title: 'Katalogo struktūra ir turinys', text: 'Kategorijos, prekės variantai, filtrai ir nuotraukos turi padėti pirkėjui pasirinkti. Prieš kūrimą sutariame, kas paruoš prekių informaciją ir ar reikės ją importuoti iš esamos sistemos.' },
                { title: 'Mokėjimai ir pristatymas', text: 'Integracijas renkamės pagal rinką, valiutas ir jūsų tiekėjų sutartis. Iš anksto įvertiname grąžinimo eigą, pristatymo taisykles ir tai, kokius veiksmus turėtų atlikti parduotuvės administratorius.' },
                { title: 'Likučiai ir augimas', text: 'Jei prekiaujate keliais kanalais, svarbu nuspręsti, kuri sistema saugos pagrindinius prekių bei likučių duomenis. Sandėlio, apskaitos ar CRM sujungimą vertiname pagal realias integracijų galimybes.' },
            ],
            faq: [
                { q: 'Ar tinka WooCommerce ir nuo ko priklauso parduotuvės kaina?', a: 'WooCommerce gali tikti prekybai su įprastu katalogu ir prieinamomis integracijomis. Kainą keičia prekių bei variantų struktūra, dizainas, importai, mokėjimo ir pristatymo logika. Sudėtingesnius reikalavimus įvertiname prieš pasirinkdami platformą.' },
                { q: 'Kas sudaro sutartis su mokėjimų ir pristatymo tiekėjais?', a: 'Paslaugų sutartis savo verslo vardu sudarote jūs. Integracijai reikia aktyvių paskyrų ir tinkamų prieigų, kuriomis dalijamasi saugiu būdu. Tiekėjų komisiniai, abonementai ir kiti jų mokesčiai nėra parduotuvės kūrimo kaina.' },
                { q: 'Ar galima perkelti prekes iš esamos parduotuvės?', a: 'Dažnai galima, tačiau pirmiausia patikriname eksportą, prekių variantus, vaizdus ir duomenų kokybę. Atskirai suderiname užsakymų ar klientų duomenų perkėlimą. Keičiant prekių adresus suplanuojame nukreipimus.' },
                { q: 'Kas turi būti paruošta iki parduotuvės paleidimo?', a: 'Reikalinga sutarta prekių informacija, kainos, pristatymo ir grąžinimo tvarka bei veikiantys tiekėjų nustatymai. Paleidimo apimtyje suderiname bandomuosius pirkimus, pranešimus ir užsakymo kelią nuo krepšelio iki administravimo.' },
            ],
            related: [
                { slug: 'automatizacija', label: 'Užsakymų ir duomenų srautų automatizavimas' },
                { slug: 'svetainiu-prieziura', label: 'El. parduotuvės techninė priežiūra' },
                { slug: 'crm-sistemos', label: 'CRM klientų ir pardavimų procesams' },
            ],
        },
        'crm-sistemos': {
            title: 'Kada verta rinktis individualią CRM sistemą?',
            intro: 'CRM kūrimą pradedame nuo jūsų darbo eigos: iš kur ateina užklausos, kas už jas atsakingas ir kada jos tampa klientais. Svarbiausia ne modulių skaičius, o aiškus kasdienis procesas.',
            considerations: [
                { title: 'Esamas įrankis ar individuali sistema?', text: 'Jei standartinė CRM patenkina poreikį, jos pritaikymas gali būti praktiškesnis už naują kūrimą. Individualus sprendimas vertas svarstymo, kai svarbūs procesai, rolės ar integracijos netelpa į esamą įrankį.' },
                { title: 'Pirmoji versija ir atsakomybės', text: 'Pirmam etapui išskiriame svarbiausius kontaktus, pardavimo būsenas ir užduotis. Kartu sutariame, kas mato, keičia ir eksportuoja duomenis, kad sistema tiktų komandos atsakomybėms.' },
                { title: 'Duomenų kokybė prieš migraciją', text: 'Dublikatai, skirtingi laukų formatai ir trūkstama informacija turi įtakos perkėlimui. Prieš importą susitariame dėl laukų atitikmenų ir patikriname bandomąjį duomenų rinkinį.' },
            ],
            faq: [
                { q: 'Ar galima pradėti tik nuo kelių CRM funkcijų?', a: 'Taip. Galime išskirti kontaktų bazę, pardavimo etapus ir užduotis, o kitus modulius planuoti vėliau. Pirmo etapo apimtį sutariame pagal dažniausią komandos darbą, ne pagal kuo ilgesnį funkcijų sąrašą.' },
                { q: 'Ar perkelsite duomenis iš Excel arba kitos CRM?', a: 'Perkėlimo galimybę ir apimtį patvirtiname įvertinę eksportą, duomenų formatą, kiekį ir kokybę. Atskirai aptariame valymą, dublikatų tvarkymą, priedus ir istorijos duomenis. Svarbu turėti teisę perduoti bei naudoti šiuos duomenis.' },
                { q: 'Ar CRM galima sujungti su svetaine ir apskaita?', a: 'Taip, jei naudojamos sistemos suteikia tinkamas API ar kitas leidžiamas duomenų perdavimo priemones. Prieš įtraukdami integraciją patikriname prieigų, tiekėjo plano, limitų ir licencijų reikalavimus.' },
                { q: 'Kaip numatomos darbuotojų prieigos?', a: 'Pagal sutartą rolę ir realų poreikį: kokius klientus, laukus bei veiksmus darbuotojas gali matyti ar atlikti. Papildomus audito žurnalus, patvirtinimus ir duomenų saugojimo taisykles aptariame projektuodami sistemą.' },
            ],
            related: [
                { slug: 'automatizacija', label: 'CRM procesų automatizavimas ir integracijos' },
                { slug: 'aplikaciju-kurimas', label: 'Klientų portalų ir verslo aplikacijų kūrimas' },
                { slug: 'interneto-svetaines', label: 'Svetainė naujoms klientų užklausoms' },
            ],
        },
        automatizacija: {
            title: 'Kurį verslo procesą automatizuoti pirmiausia?',
            intro: 'Pradedame nuo pasikartojančios užduoties, kurios pradžia, duomenys ir norimas rezultatas yra aiškūs. Tai leidžia įvertinti, ar automatizacija praktiška, ir neperkelti neaiškaus proceso į sudėtingą sistemą.',
            considerations: [
                { title: 'Aiškios taisyklės ir išimtys', text: 'Aprašome, kas pradeda procesą, iš kur gaunami duomenys ir kada reikalingas žmogaus sprendimas. Prieš automatizuojant svarbu suprasti ne tik įprastą kelią, bet ir trūkstamų ar neteisingų duomenų atvejus.' },
                { title: 'Tiekėjų API ir naudojimo limitai', text: 'Patikriname, ar jūsų planas suteikia reikiamą prieigą, kokie taikomi užklausų limitai ir ar integracija leidžiama. Išorinių paslaugų abonementai bei jų pakeitimai turi įtakos bendroms išlaidoms.' },
                { title: 'Kas vyksta sutrikus srautui?', text: 'Suderiname klaidų pranešimus, pakartojimų tvarką ir apsaugą nuo pasikartojančių veiksmų. Kritiniams žingsniams, pavyzdžiui, mokėjimams ar masinių pranešimų siuntimui, aptariame papildomus patvirtinimus.' },
            ],
            faq: [
                { q: 'Kokius procesus galima automatizuoti?', a: 'Pavyzdžiui, svetainės užklausų perdavimą į CRM, priminimus, dokumentų paruošimą ar duomenų sinchronizavimą. Tinkamumas priklauso nuo aiškių taisyklių, duomenų kokybės ir sistemų teikiamų prieigų.' },
                { q: 'Ar integracija įmanoma, jei sistema neturi API?', a: 'Kartais galima naudoti tiekėjo leidžiamą eksportą, importą ar kitą oficialią integraciją. Jei patikimo ir leidžiamo būdo nėra, jo nežadame: aptariame proceso pakeitimą ar tinkamesnį įrankį.' },
                { q: 'Kaip vertinama automatizacijos nauda?', a: 'Prieš kūrimą verta užfiksuoti užduoties dažnį, jai skiriamą laiką ir taisytinas klaidas. Po paleidimo galima palyginti tuos pačius rodiklius. Sutaupymo ar finansinės grąžos procentų nežadame be realių duomenų.' },
                { q: 'Ar automatizacijai reikės priežiūros?', a: 'Gali reikėti, nes tiekėjai keičia API, prieigas ar naudojimo taisykles. Pasiūlyme atskiriame sukūrimą nuo sutartos stebėsenos, reagavimo ir tolesnių pakeitimų.' },
            ],
            related: [
                { slug: 'crm-sistemos', label: 'CRM sistema klientų procesams valdyti' },
                { slug: 'el-parduotuves', label: 'El. parduotuvė su reikiamomis integracijomis' },
                { slug: 'aplikaciju-kurimas', label: 'Individuali aplikacija procesui valdyti' },
            ],
        },
        'aplikaciju-kurimas': {
            title: 'Nuo idėjos iki aiškios pirmos aplikacijos versijos',
            intro: 'Klientų portalą, vidinę verslo sistemą ar naują produktą pradedame nuo vienos svarbiausios vartotojo užduoties. Prototipas ir sutarta MVP apimtis padeda sprendimus patikrinti prieš kuriant visus modulius.',
            considerations: [
                { title: 'Web, PWA ar atskira mobilioji programėlė?', text: 'Web aplikacija pasiekiama naršyklėje. PWA gali suteikti diegimo ir kai kurias mobiliesiems naudingas funkcijas, tačiau jų palaikymas skiriasi pagal įrenginį. Atskiros iOS ar Android programėlės poreikį ir kūrimo apimtį vertiname atskirai.' },
                { title: 'MVP nėra visas ateities funkcijų sąrašas', text: 'Pirmam etapui paliekame tai, ko reikia pagrindinei užduočiai atlikti ir idėjai patikrinti. Mokėjimus, roles, administravimą ir integracijas įtraukiame pagal jų būtinumą šiam tikslui.' },
                { title: 'Eksploatavimas po paleidimo', text: 'Dar planuodami aptariame hostingą, atsargines kopijas, išorinių paslaugų išlaidas ir atsakomybę už priežiūrą. Tai padeda vertinti ne tik sukūrimo kainą, bet ir tolesnį produkto naudojimą.' },
            ],
            faq: [
                { q: 'Ką reikia turėti, kad galėtume pradėti?', a: 'Pakanka aiškios problemos, būsimo vartotojo ir kelių pagrindinių veiksmų aprašymo. Jei turite eskizą, esamą procesą ar pavyzdį, jis padės. Pirmiausia patiksliname tikslą, prioritetus ir ką turėtų leisti padaryti pirmoji versija.' },
                { q: 'Kiek laiko trunka aplikacijos kūrimas?', a: 'Terminas priklauso nuo ekranų, rolių, verslo taisyklių, integracijų ir duomenų perkėlimo. Jį nustatome įvertinę konkrečią apimtį; išorinių tiekėjų prieigos bei sprendimų derinimas taip pat gali turėti įtakos eigai.' },
                { q: 'Ar vėliau bus galima pridėti naujų funkcijų?', a: 'Galima planuoti etapais: pirmą versiją, jos naudojimo įvertinimą ir kitus modulius. Plėtros galimybes numatome projektuodami, tačiau kiekvieno naujo etapo apimtį ir kainą įvertiname atskirai.' },
                { q: 'Ką gausiu perdavus aplikaciją?', a: 'Perduodami sutartyje numatyti rezultatai, prieigos ir projekto medžiaga. Iš anksto aptariame kodą, dokumentaciją, apmokymą ir palaikymą. Trečiųjų šalių komponentams bei paslaugoms galioja jų licencijos ir naudojimo sąlygos.' },
            ],
            related: [
                { slug: 'crm-sistemos', label: 'CRM kūrimas klientų ir pardavimų valdymui' },
                { slug: 'automatizacija', label: 'API integracijos ir procesų automatizavimas' },
                { slug: 'interneto-svetaines', label: 'Svetainė produkto ar verslo pristatymui' },
            ],
        },
        'svetainiu-prieziura': {
            title: 'Kokia priežiūra reikalinga jūsų svetainei?',
            intro: 'Priežiūros apimtį deriname pagal naudojamą technologiją, svetainės svarbą verslui ir keitimų dažnį. Įmonės pristatymo puslapio ir aktyvios el. parduotuvės poreikiai skiriasi.',
            considerations: [
                { title: 'Pirmiausia — esamos būklės patikra', text: 'Įvertiname TVS, įskiepius, licencijas, hostingą ir turimas kopijas. Seniai neatnaujintos ar modifikuotos sistemos sutvarkymas gali būti atskiras darbas prieš pradedant reguliarią priežiūrą.' },
                { title: 'Kopija ir atkūrimo galimybė', text: 'Svarbu ne vien turėti kopiją: aptariame jos dažnį, saugojimo vietą, laiką ir atkūrimo patikrą. El. parduotuvei įvertiname, kiek naujų užsakymų duomenų rizikuotume prarasti atkūrimo atveju.' },
                { title: 'Aiški reagavimo ir pakeitimų apimtis', text: 'Sutariame darbo laiką, pranešimo apie sutrikimą kanalą ir darbų prioritetus. Smulkūs turinio keitimai, gedimų taisymas ir naujų funkcijų kūrimas nėra tas pats — jų ribos turi būti aiškios.' },
            ],
            faq: [
                { q: 'Ar prižiūrite kitų kūrėjų sukurtas svetaines?', a: 'Galimybę patvirtiname po techninės peržiūros ir turimų prieigų įvertinimo. Svarbi naudojama technologija, licencijų būklė ir galimybė saugiai atlikti pakeitimus. Pradinius sutvarkymo darbus, jei jų reikia, aptariame atskirai.' },
                { q: 'Ar priežiūros planas garantuoja, kad svetainė niekada nesuges?', a: 'Ne. Atnaujinimai, kopijos ir patikros padeda mažinti riziką, bet nepašalina visų gedimų ar atakų galimybės. Reagavimo laiką, atkūrimo darbus ir atsakomybės ribas nustatome konkrečiame susitarime.' },
                { q: 'Ar į mėnesinę kainą įeina naujos funkcijos?', a: 'Tik tai, kas aiškiai numatyta plane. Sutartos smulkių pakeitimų apimties nereikėtų tapatinti su nauju moduliu, integracija ar dizaino atnaujinimu. Didesniems darbams parengiame atskirą įvertinimą.' },
                { q: 'Ar domenas, hostingas ir licencijos įtraukti į planą?', a: 'Jų įtraukimas turi būti aiškiai nurodytas pasiūlyme; automatiškai jų nelaikome įtrauktomis paslaugomis. Taip pat suderiname, kas atsakingas už pratęsimus ir kas gauna tiekėjų pranešimus.' },
            ],
            related: [
                { slug: 'interneto-svetaines', label: 'Svetainės kūrimas ir atnaujinimas' },
                { slug: 'el-parduotuves', label: 'El. parduotuvių kūrimas' },
            ],
        },
    },
    en: {
        'interneto-svetaines': {
            title: 'What kind of website does your business need?',
            intro: 'I am Aivaras, a website developer based in Kaunas. I work remotely with clients across Lithuania: first we discuss what visitors need to learn and do, then choose a suitable website scope.',
            considerations: [
                { title: 'Company website or landing page?', text: 'A single page may be enough for one offer or campaign. If you have different services, projects and audiences, separate pages with useful content help visitors find what matters to them.' },
                { title: 'WordPress or custom development?', text: 'WordPress can suit a conventional content website that you want to edit yourself. For custom calculations, accounts or workflows, we assess a tailored solution. Functionality and maintenance needs guide the choice, not design alone.' },
                { title: 'Preserve what works during a redesign', text: 'Before replacing a website, we review its content, URLs and key visitor journeys. If addresses change, the agreed scope should include a mapping and redirect plan.' },
            ],
            faq: [
                { q: 'What affects the price of a website?', a: 'The number of pages and languages, design, content preparation and features such as forms, bookings or integrations. Published prices are starting points; we confirm the specific scope, final price and timeline in a proposal before work begins.' },
                { q: 'Are the domain, hosting and copy included?', a: 'We discuss domains, hosting, paid licences, copy and photography separately. The proposal distinguishes one-off development from recurring costs and explains which materials you will provide.' },
                { q: 'Will I be able to edit the content myself?', a: 'Yes, when content management is included in the agreed scope. We define the editable text, images and other fields in advance, then show you how to manage them during handover.' },
                { q: 'Does technical SEO guarantee a position on Google?', a: 'No. Clear page titles, indexing settings, structure and performance provide a technical foundation. Indexing and rankings also depend on useful content, competition and other factors. A redesign should also account for redirects when URLs change.' },
            ],
            related: [
                { slug: 'el-parduotuves', label: 'E-commerce development for selling online' },
                { slug: 'svetainiu-prieziura', label: 'Website maintenance after launch' },
                { slug: 'aplikaciju-kurimas', label: 'Application development for custom functionality' },
            ],
        },
        'el-parduotuves': {
            title: 'What should you plan before building an online store?',
            intro: 'Store scope depends on more than design. We discuss the product catalogue, variants, payment options and daily order handling so the solution fits your sales model.',
            considerations: [
                { title: 'Catalogue structure and content', text: 'Categories, variants, filters and photographs should help customers choose. Before development, we agree who prepares product information and whether it needs importing from an existing system.' },
                { title: 'Payments and delivery', text: 'Integrations depend on your markets, currencies and provider agreements. We consider returns, delivery rules and the actions the store administrator needs to perform.' },
                { title: 'Stock and future growth', text: 'If you sell through several channels, we need to decide which system holds the primary product and stock records. Warehouse, accounting or CRM connections are assessed against the available integration options.' },
            ],
            faq: [
                { q: 'Is WooCommerce suitable, and what affects the price?', a: 'WooCommerce can suit stores with conventional catalogues and available integrations. Product and variant structures, design, imports, payment and delivery logic affect the cost. We assess more complex requirements before choosing a platform.' },
                { q: 'Who signs agreements with payment and delivery providers?', a: 'You sign provider agreements in your business name. Integrations require active accounts and appropriate access shared through a secure method. Provider commissions, subscriptions and other fees are separate from store development costs.' },
                { q: 'Can products be migrated from an existing store?', a: 'Often yes, but we first check the export, product variants, images and data quality. Order or customer data migration is agreed separately. If product URLs change, we plan redirects.' },
                { q: 'What needs to be ready before launch?', a: 'The agreed product information, prices, delivery and returns policies, and working provider settings are needed. The launch scope covers agreed test purchases, notifications and the order journey from basket to administration.' },
            ],
            related: [
                { slug: 'automatizacija', label: 'Order and data workflow automation' },
                { slug: 'svetainiu-prieziura', label: 'Technical maintenance for your store' },
                { slug: 'crm-sistemos', label: 'CRM for customer and sales processes' },
            ],
        },
        'crm-sistemos': {
            title: 'When is a custom CRM the right choice?',
            intro: 'CRM development starts with your workflow: where enquiries arrive, who owns them and how they become customers. A clear daily process matters more than the number of modules.',
            considerations: [
                { title: 'Existing tool or a custom system?', text: 'If a standard CRM meets your needs, configuring it may be more practical than building a new one. A custom solution is worth considering when important workflows, roles or integrations do not fit an existing tool.' },
                { title: 'First release and responsibilities', text: 'We identify the essential contacts, sales stages and tasks for the first phase. We also agree who can view, edit and export data so the system reflects the team’s responsibilities.' },
                { title: 'Data quality before migration', text: 'Duplicates, inconsistent field formats and missing information affect migration. Before importing, we map the fields and validate a sample data set.' },
            ],
            faq: [
                { q: 'Can we start with just a few CRM features?', a: 'Yes. We can begin with contacts, sales stages and tasks, planning other modules later. The initial scope follows the team’s most frequent work rather than the longest possible feature list.' },
                { q: 'Can data be migrated from Excel or another CRM?', a: 'We confirm feasibility and scope after reviewing the export, format, volume and quality. Data cleaning, duplicates, attachments and historical records are discussed separately. You must have the right to transfer and use the data.' },
                { q: 'Can the CRM connect to our website and accounting system?', a: 'Yes, where those systems offer suitable APIs or other permitted data exchange methods. Before including an integration, we check access, provider plan, limits and licensing requirements.' },
                { q: 'How are employee permissions planned?', a: 'Around agreed roles and actual needs: which customers, fields and actions each person can view or use. Additional audit logs, approvals and data retention rules are discussed during system design.' },
            ],
            related: [
                { slug: 'automatizacija', label: 'CRM workflow automation and integrations' },
                { slug: 'aplikaciju-kurimas', label: 'Client portals and business applications' },
                { slug: 'interneto-svetaines', label: 'A website for new customer enquiries' },
            ],
        },
        automatizacija: {
            title: 'Which business process should you automate first?',
            intro: 'We start with a repeated task that has a clear trigger, inputs and expected outcome. This helps assess whether automation is practical instead of turning an unclear process into a complicated system.',
            considerations: [
                { title: 'Clear rules and exceptions', text: 'We describe what starts the process, where data comes from and when a human decision is needed. Before automation, it is important to understand missing or invalid data cases as well as the usual path.' },
                { title: 'Provider APIs and usage limits', text: 'We check whether your plan offers the required access, what request limits apply and whether the integration is permitted. External subscriptions and provider changes affect the total operating cost.' },
                { title: 'What happens when a workflow fails?', text: 'We agree on error notifications, retries and safeguards against duplicate actions. Critical steps such as payments or bulk messaging may need additional approvals.' },
            ],
            faq: [
                { q: 'Which processes can be automated?', a: 'Examples include sending website enquiries to a CRM, reminders, document preparation and data synchronisation. Suitability depends on clear rules, data quality and the access provided by each system.' },
                { q: 'Is an integration possible if a system has no API?', a: 'Sometimes a provider-approved export, import or other official integration is available. If there is no reliable, permitted method, we do not promise one; we discuss changing the process or using a more suitable tool.' },
                { q: 'How do we evaluate the benefit of automation?', a: 'Before development, record the task frequency, time spent and errors needing correction. The same measures can be compared after launch. We do not promise savings or financial return percentages without real data.' },
                { q: 'Will an automation need maintenance?', a: 'It may, because providers change APIs, access and usage rules. The proposal separates implementation from any agreed monitoring, response and future changes.' },
            ],
            related: [
                { slug: 'crm-sistemos', label: 'A CRM for managing customer workflows' },
                { slug: 'el-parduotuves', label: 'An online store with the integrations you need' },
                { slug: 'aplikaciju-kurimas', label: 'A custom application for managing the process' },
            ],
        },
        'aplikaciju-kurimas': {
            title: 'From an idea to a clearly scoped first application',
            intro: 'A client portal, internal business system or new product starts with one important user task. A prototype and agreed MVP scope help validate decisions before building every module.',
            considerations: [
                { title: 'Web, PWA or a separate mobile app?', text: 'A web application runs in the browser. A PWA can offer installation and some mobile-oriented features, but support varies by device. The need and scope for a separate iOS or Android app are assessed independently.' },
                { title: 'An MVP is not the entire future feature list', text: 'The first phase includes what is needed to complete the main task and test the idea. Payments, roles, administration and integrations are included according to their importance to that goal.' },
                { title: 'Operating the product after launch', text: 'During planning, we discuss hosting, backups, external service costs and responsibility for maintenance. This helps assess ongoing operation as well as the initial development price.' },
            ],
            faq: [
                { q: 'What do I need before we can start?', a: 'A clear problem, an intended user and a description of a few main actions are enough. A sketch, existing process or example can help. First, we clarify the goal, priorities and what the first release should enable.' },
                { q: 'How long does application development take?', a: 'The timeline depends on screens, roles, business rules, integrations and data migration. We confirm it after assessing the specific scope; external provider access and decision-making can also affect delivery.' },
                { q: 'Can new features be added later?', a: 'We can plan in phases: a first release, evaluation of its use and subsequent modules. We account for expansion during design, but scope and cost are assessed separately for each new phase.' },
                { q: 'What is included in application handover?', a: 'The deliverables, access and project materials defined in the agreement are handed over. We discuss source code, documentation, training and support in advance. Third-party components and services remain subject to their own licences and terms.' },
            ],
            related: [
                { slug: 'crm-sistemos', label: 'CRM development for customers and sales' },
                { slug: 'automatizacija', label: 'API integrations and workflow automation' },
                { slug: 'interneto-svetaines', label: 'A website to introduce your product or business' },
            ],
        },
        'svetainiu-prieziura': {
            title: 'What maintenance does your website need?',
            intro: 'Maintenance scope depends on the technology, the website’s importance to your business and how often it changes. A company presentation site and an active online store have different needs.',
            considerations: [
                { title: 'Start with a review of the current state', text: 'We assess the CMS, plugins, licences, hosting and existing backups. Bringing an outdated or heavily modified system into a maintainable state may be a separate task before recurring care begins.' },
                { title: 'Backups and the ability to restore', text: 'Having a backup is only part of the plan: we discuss frequency, storage location, retention and recovery checks. For a store, we consider how much new order data could be at risk during restoration.' },
                { title: 'Clear response and change scope', text: 'We agree on working hours, how to report an incident and work priorities. Small content edits, fault repairs and new feature development are different tasks, so their boundaries should be clear.' },
            ],
            faq: [
                { q: 'Do you maintain websites built by other developers?', a: 'We confirm feasibility after a technical review and assessment of available access. Technology, licence status and the ability to make changes safely all matter. Any initial remediation is discussed separately.' },
                { q: 'Does a maintenance plan guarantee the site will never fail?', a: 'No. Updates, backups and checks help reduce risk, but cannot eliminate every failure or attack. Response times, recovery work and responsibility are defined in the specific agreement.' },
                { q: 'Does the monthly price include new features?', a: 'Only work explicitly included in the plan. An allowance for small changes is not the same as a new module, integration or redesign. Larger work receives a separate estimate.' },
                { q: 'Are the domain, hosting and licences included?', a: 'Any inclusion must be stated clearly in the proposal; they are not automatically part of the service. We also agree who handles renewals and receives provider notifications.' },
            ],
            related: [
                { slug: 'interneto-svetaines', label: 'Website development and redesign' },
                { slug: 'el-parduotuves', label: 'E-commerce development' },
            ],
        },
    },
}
