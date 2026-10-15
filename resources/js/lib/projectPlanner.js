export const PROJECT_BRIEF_EVENT = 'devsprendimai:project-brief'
export const MESSAGE_LIMIT = 5000

export const projectGoals = [
    { id: 'enquiries', service: 'Interneto svetainė', path: '/interneto-svetaines', lt: 'Gauti daugiau užklausų', en: 'Get more enquiries', titleLt: 'Svetainė su aiškia užklausos kelione', titleEn: 'A website with a clear enquiry journey', detailLt: 'Pradėkime nuo stipraus pasiūlymo, patogios mobilios versijos ir paprasto kontakto.', detailEn: 'Start with a clear offer, a smooth mobile experience and an easy way to get in touch.' },
    { id: 'sales', service: 'El. parduotuvė', path: '/el-parduotuves', lt: 'Parduoti internetu', en: 'Sell online', titleLt: 'El. parduotuvė, patogi pirkėjui', titleEn: 'An online store built around the buyer', detailLt: 'Pirmiausia suplanuokime katalogą, atsiskaitymą ir pristatymą. Papildomas funkcijas plėskime pagal poreikį.', detailEn: 'Plan the catalogue, checkout and delivery first. Add further features when you need them.' },
    { id: 'customers', service: 'CRM sistema', path: '/crm-sistemos', lt: 'Suvaldyti klientus', en: 'Organise customer work', titleLt: 'Vienas vaizdas klientams ir pardavimams', titleEn: 'One place for customers and sales', detailLt: 'Sujunkime kontaktus, pardavimo etapus ir kitus veiksmus, kad komanda žinotų, kas vyksta.', detailEn: 'Bring contacts, sales stages and next actions together so the team knows what happens next.' },
    { id: 'time', service: 'Automatizacija', path: '/automatizacija', lt: 'Mažiau rankinio darbo', en: 'Reduce repetitive work', titleLt: 'Pradžia nuo vieno pasikartojančio darbo', titleEn: 'Start with one repetitive task', detailLt: 'Išsirinkime konkretų procesą, suskaičiuokime jam skiriamą laiką ir įvertinkime automatizavimo naudą.', detailEn: 'Choose a specific process, measure the time it takes and assess the value of automating it.' },
    { id: 'product', service: 'Aplikacijų kūrimas', path: '/aplikaciju-kurimas', lt: 'Paleisti naują produktą', en: 'Launch a new product', titleLt: 'Mažesnis pirmas žingsnis. Aiškesnis MVP.', titleEn: 'A smaller first step. A focused MVP.', detailLt: 'Atrinkime svarbiausią naudotojo scenarijų ir pirmos versijos funkcijas. Plėtrą planuokime pagal grįžtamąjį ryšį.', detailEn: 'Focus the first version on the key user journey. Plan the next features around real feedback.' },
]

export const projectStages = [
    { id: 'new', lt: 'Pradedu nuo nulio', en: 'Starting from scratch' },
    { id: 'improve', lt: 'Tobulinu turimą', en: 'Improving existing' },
    { id: 'explore', lt: 'Dar renkuosi kryptį', en: 'Exploring options' },
]

export const projectBudgets = [
    { id: 'discuss', lt: 'Norėčiau pasitarti', en: 'I would like advice' },
    { id: 'under1000', lt: 'Iki 1 000 €', en: 'Under €1,000' },
    { id: '1000to3000', lt: '1 000–3 000 €', en: '€1,000–3,000' },
    { id: '3000to6000', lt: '3 000–6 000 €', en: '€3,000–6,000' },
    { id: 'over6000', lt: 'Daugiau nei 6 000 €', en: 'Over €6,000' },
]

// No storage, network or analytics: this only builds a visitor-editable draft.
export function createProjectBrief({ goal, stage, budget }, language = 'lt') {
    const selectedGoal = projectGoals.find((item) => item.id === goal)
    const selectedStage = projectStages.find((item) => item.id === stage)
    const selectedBudget = projectBudgets.find((item) => item.id === budget)
    if (!selectedGoal || !selectedStage || !selectedBudget) return null
    const en = language === 'en'
    const locale = en ? 'en' : 'lt'
    const lines = en ? [
        'My project brief',
        `Goal: ${selectedGoal.en}.`,
        `Current situation: ${selectedStage.en}.`,
        `Planned budget: ${selectedBudget.en}.`,
        'I would like to discuss a suitable scope and receive a proposal.',
    ] : [
        'Mano projekto planas',
        `Tikslas: ${selectedGoal.lt}.`,
        `Dabartinė situacija: ${selectedStage.lt}.`,
        `Planuojamas biudžetas: ${selectedBudget.lt}.`,
        'Norėčiau aptarti tinkamą projekto apimtį ir gauti pasiūlymą.',
    ]
    return { service: selectedGoal.service, message: lines.join('\n'), goal: selectedGoal[locale] }
}

export function mergeProjectBrief(current, previousDraft, nextDraft) {
    if (typeof nextDraft !== 'string' || !nextDraft.trim()) return null
    const existing = typeof current === 'string' ? current : ''
    let merged
    if (previousDraft && existing.includes(previousDraft)) {
        merged = existing.replace(previousDraft, nextDraft)
    } else if (existing.includes(nextDraft)) {
        merged = existing
    } else {
        merged = [existing.trim(), nextDraft].filter(Boolean).join('\n\n')
    }
    // Never silently trim or overwrite a visitor's own words.
    return merged.length <= MESSAGE_LIMIT ? merged : null
}

export function sendProjectBrief(brief) {
    if (typeof window === 'undefined' || !brief) return
    window.dispatchEvent(new CustomEvent(PROJECT_BRIEF_EVENT, { detail: brief }))
}
