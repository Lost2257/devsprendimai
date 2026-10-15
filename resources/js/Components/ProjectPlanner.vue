<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'
import { createProjectBrief, projectGoals, projectStages, projectBudgets, sendProjectBrief } from '@/lib/projectPlanner'

const { isEnglish, localize } = useLocale()
const goal = ref('enquiries')
const stage = ref('new')
const budget = ref('discuss')
const language = computed(() => isEnglish.value ? 'en' : 'lt')
const recommendation = computed(() => projectGoals.find((item) => item.id === goal.value))
const sendToForm = () => sendProjectBrief(createProjectBrief({ goal: goal.value, stage: stage.value, budget: budget.value }, language.value))
</script>

<template>
    <section id="project-planner" class="planner-section" aria-labelledby="planner-heading">
        <div class="site-container">
            <div class="planner-shell">
                <div class="planner-inputs">
                    <span class="planner-kicker">{{ isEnglish ? 'Your project, a clearer direction' : 'Jūsų projektas. Aiškesnė kryptis.' }}</span>
                    <h2 id="planner-heading">{{ isEnglish ? 'Where should we start?' : 'Nuo ko pradėti?' }}</h2>
                    <p class="planner-intro">{{ isEnglish ? 'Three choices turn your idea into a project brief. No technical knowledge needed.' : 'Trys pasirinkimai – ir jūsų idėja virsta projekto planu. Be techninių terminų.' }}</p>

                    <fieldset class="goal-fieldset">
                        <legend><span>01</span> {{ isEnglish ? 'What is your main goal?' : 'Koks svarbiausias tikslas?' }}</legend>
                        <div class="goal-options">
                            <label v-for="item in projectGoals" :key="item.id" class="goal-option" :class="{ selected: goal === item.id }">
                                <input v-model="goal" type="radio" name="project-goal" :value="item.id">
                                <span>{{ item[language] }}</span><i aria-hidden="true">{{ goal === item.id ? '✓' : '+' }}</i>
                            </label>
                        </div>
                    </fieldset>

                    <div class="planner-selects">
                        <label for="planner-stage"><span><b>02</b> {{ isEnglish ? 'Current situation' : 'Dabartinė situacija' }}</span>
                            <select id="planner-stage" v-model="stage"><option v-for="item in projectStages" :key="item.id" :value="item.id">{{ item[language] }}</option></select>
                        </label>
                        <label for="planner-budget"><span><b>03</b> {{ isEnglish ? 'Planned budget' : 'Planuojamas biudžetas' }}</span>
                            <select id="planner-budget" v-model="budget"><option v-for="item in projectBudgets" :key="item.id" :value="item.id">{{ item[language] }}</option></select>
                        </label>
                    </div>
                </div>

                <div class="planner-result">
                    <div class="result-top"><span class="result-dot" aria-hidden="true"></span>{{ isEnglish ? 'Suggested direction' : 'Siūloma kryptis' }}<span class="result-mark" aria-hidden="true">↗</span></div>
                    <div aria-live="polite" aria-atomic="true" class="result-body">
                        <h3>{{ isEnglish ? recommendation.titleEn : recommendation.titleLt }}</h3>
                        <p>{{ isEnglish ? recommendation.detailEn : recommendation.detailLt }}</p>
                    </div>
                    <div class="result-next">
                        <span>{{ isEnglish ? 'A focused first conversation' : 'Konkretus pirmas pokalbis' }}</span>
                        <ul>
                            <li><i aria-hidden="true">✓</i>{{ isEnglish ? 'Agree on the essentials' : 'Suderiname svarbiausius poreikius' }}</li>
                            <li><i aria-hidden="true">✓</i>{{ isEnglish ? 'Separate launch needs from later ideas' : 'Atskiriame startą nuo vėlesnės plėtros' }}</li>
                            <li><i aria-hidden="true">✓</i>{{ isEnglish ? 'Confirm scope, price and next steps' : 'Patvirtiname apimtį, kainą ir eigą' }}</li>
                        </ul>
                    </div>
                    <button type="button" class="planner-action" @click="sendToForm">{{ isEnglish ? 'Use this brief in my enquiry' : 'Perkelti planą į užklausą' }} <span aria-hidden="true">→</span></button>
                    <p class="planner-note">{{ isEnglish ? 'Nothing is sent automatically. Review your draft before submitting. This is a starting point, not a quote.' : 'Nieko nesiunčiame automatiškai. Juodraštį galėsite peržiūrėti. Tai pradinė kryptis, ne kainos pasiūlymas.' }}</p>
                    <Link class="planner-details" :href="localize(recommendation.path)">{{ isEnglish ? 'Explore this service' : 'Plačiau apie šią paslaugą' }} <span aria-hidden="true">↗</span></Link>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.planner-section{position:relative;padding:0 0 clamp(4.5rem,8vw,8rem);scroll-margin-top:7rem}
.planner-shell{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(0,.85fr);overflow:hidden;border:1px solid rgba(103,232,249,.2);border-radius:1.5rem;background:#0b121e}
.planner-inputs{padding:clamp(1.25rem,3vw,2.5rem)}
.planner-kicker{color:#67e8f9;font-size:.68rem;letter-spacing:.16em;text-transform:uppercase;font-weight:800}
.planner-inputs h2{margin-top:.8rem;color:#f1f5f9;font-size:clamp(2rem,3.4vw,3.5rem);letter-spacing:-.05em;font-weight:850;line-height:1.05}
.planner-intro{max-width:38rem;margin-top:.85rem;color:#a3b0c1;font-size:.94rem;line-height:1.7}
.goal-fieldset{margin-top:1.8rem;min-width:0}
.goal-fieldset legend,.planner-selects label>span{display:block;margin-bottom:.7rem;color:#d3deeb;font-size:.78rem;font-weight:750}
.goal-fieldset legend>span,.planner-selects b{margin-right:.5rem;color:#67e8f9;font-size:.65rem}
.goal-options{display:flex;flex-wrap:wrap;gap:.55rem}
.goal-option{position:relative;display:flex;align-items:center;justify-content:space-between;gap:.8rem;min-height:2.8rem;padding:.6rem .85rem;border:1px solid #2b384b;border-radius:.65rem;color:#b9c5d5;cursor:pointer;font-size:.78rem;transition:background .15s ease,border-color .15s ease}
.goal-option input{position:absolute;width:1px;height:1px;opacity:0}
.goal-option i{font-style:normal;color:#87a3b2}
.goal-option.selected{border-color:#22d3ee;background:rgba(34,211,238,.1);color:#d4faff}
.goal-option.selected i{color:#67e8f9}
.goal-option:focus-within{outline:2px solid #67e8f9;outline-offset:3px}
.planner-selects{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.85rem;margin-top:1.5rem}
.planner-selects label{min-width:0}
.planner-selects select{width:100%;min-height:3rem;padding:.7rem 2.1rem .7rem .8rem;border:1px solid #2b384b;border-radius:.65rem;color:#d3deeb;background-color:#08101a;font-size:.8rem}
.planner-selects select:focus{outline:2px solid #67e8f9;outline-offset:3px;box-shadow:none}
.planner-result{display:flex;flex-direction:column;min-width:0;padding:clamp(1.25rem,3vw,2.5rem);border-left:1px solid rgba(103,232,249,.16);background:linear-gradient(140deg,rgba(34,211,238,.075),rgba(34,211,238,.015))}
.result-top{display:flex;align-items:center;gap:.55rem;color:#83e5f1;font-size:.65rem;letter-spacing:.12em;text-transform:uppercase;font-weight:800}
.result-dot{width:6px;height:6px;flex-shrink:0;border-radius:50%;background:#22d3ee;box-shadow:0 0 12px rgba(34,211,238,.4)}
.result-mark{margin-left:auto;font-size:1.4rem;line-height:1}
.result-body h3{margin-top:1.2rem;color:#f1f9fc;font-size:clamp(1.5rem,2.4vw,2.2rem);font-weight:800;line-height:1.16;letter-spacing:-.035em}
.result-body>p{margin-top:.9rem;color:#a6b6c7;font-size:.86rem;line-height:1.7}
.result-next{margin-block:1.5rem}
.result-next>span{color:#d4e8ed;font-size:.75rem;font-weight:750}
.result-next ul{display:grid;gap:.55rem;margin-top:.65rem}
.result-next li{display:flex;gap:.65rem;color:#a6b6c7;font-size:.75rem;line-height:1.5}
.result-next i{font-style:normal;color:#67e8f9}
.planner-action{display:flex;justify-content:space-between;align-items:center;gap:1rem;width:100%;min-height:3.4rem;margin-top:auto;padding:.8rem 1rem;border-radius:.75rem;color:#061016;background:#22d3ee;text-align:left;font-size:.82rem;font-weight:850}
.planner-action:hover{background:#67e8f9}
.planner-action:focus-visible,.planner-details:focus-visible{outline:2px solid #d5faff;outline-offset:4px}
.planner-note{margin-top:.8rem;color:#8da5b6;font-size:.69rem;line-height:1.6}
.planner-details{align-self:flex-start;display:inline-flex;align-items:center;gap:.6rem;min-height:2.5rem;margin-top:.45rem;color:#9cebf4;font-size:.74rem;font-weight:700;text-decoration:underline;text-underline-offset:4px;text-decoration-color:#37646b}
@media(max-width:850px){.planner-shell{grid-template-columns:1fr}.planner-result{border-left:0;border-top:1px solid rgba(103,232,249,.16)}.result-next{margin-block:1.2rem}.result-body h3{max-width:32rem}.planner-action{margin-top:0}}
@media(max-width:480px){.planner-selects{grid-template-columns:1fr}.planner-selects select{font-size:1rem}.goal-options{display:grid;grid-template-columns:1fr}.goal-option{min-height:2.9rem}.planner-shell{border-radius:1.1rem}.planner-action{min-height:3.5rem}.result-top{letter-spacing:.08em}}
@media(prefers-reduced-motion:reduce){.goal-option{transition:none}}
</style>
