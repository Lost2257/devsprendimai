<template>

<section
    id="solutions"
    class="py-32"
>

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">

            Automatizacijų pavyzdžiai

        </span>

        <h2 class="section-title">

            Ką galima
            <span>automatizuoti?</span>

        </h2>

        <p class="section-text">

            Kiekvieną automatizaciją kuriame individualiai pagal jūsų
            verslo procesus. Štai keli realūs pavyzdžiai.

        </p>

    </div>

    <div class="space-y-8">

        <!-- 1 -->

        <div class="workflow-card">

            <div class="workflow-title">

                Nauja užklausa svetainėje

            </div>

            <div class="workflow">

                <div class="node">
                    🌐
                    <span>Kontaktų forma</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    📊
                    <span>CRM sistema</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    📧
                    <span>El. laiškas</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    💬
                    <span>Slack / Teams</span>
                </div>

            </div>

        </div>

        <!-- 2 -->

        <div class="workflow-card">

            <div class="workflow-title">

                Naujas užsakymas

            </div>

            <div class="workflow">

                <div class="node">
                    🛒
                    <span>El. parduotuvė</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    📄
                    <span>Sąskaita</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    💶
                    <span>Apskaita</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    📧
                    <span>Klientui laiškas</span>
                </div>

            </div>

        </div>

        <!-- 3 -->

        <div class="workflow-card">

            <div class="workflow-title">

                AI klientų aptarnavimas

            </div>

            <div class="workflow">

                <div class="node">
                    💬
                    <span>Kliento žinutė</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    🤖
                    <span>OpenAI</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    📚
                    <span>Jūsų duomenys</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    ✉️
                    <span>Atsakymas</span>
                </div>

            </div>

        </div>

        <!-- 4 -->

        <div class="workflow-card">

            <div class="workflow-title">

                Registracija į susitikimą

            </div>

            <div class="workflow">

                <div class="node">
                    📅
                    <span>Forma</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    📆
                    <span>Google Calendar</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    📧
                    <span>Patvirtinimas</span>
                </div>

                <div class="arrow">→</div>

                <div class="node">
                    🔔
                    <span>Priminimas</span>
                </div>

            </div>

        </div>

    </div>

</div>

</section>

</template>

<style scoped>

.section-label{

display:inline-flex;
padding:10px 18px;
border-radius:999px;
background:rgba(34,211,238,.08);
border:1px solid rgba(34,211,238,.2);
color:#22d3ee;
font-weight:600;

}

.section-title{

margin-top:30px;
font-size:56px;
font-weight:900;

}

.section-title span{

color:#22d3ee;

}

.section-text{

max-width:760px;
margin:30px auto 0;
font-size:20px;
line-height:1.9;
color:#94a3b8;

}

.workflow-card{

padding:42px;

border-radius:28px;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

transition:.3s;

}

.workflow-card:hover{

border-color:rgba(34,211,238,.35);

box-shadow:0 20px 45px rgba(34,211,238,.12);

}

.workflow-title{

font-size:28px;

font-weight:800;

margin-bottom:35px;

}

.workflow{

display:flex;

justify-content:space-between;

align-items:center;

gap:20px;

flex-wrap:wrap;

}

.node{

flex:1;

min-width:150px;

padding:22px;

text-align:center;

border-radius:20px;

background:rgba(34,211,238,.05);

border:1px solid rgba(34,211,238,.12);

font-size:34px;

display:flex;

flex-direction:column;

gap:12px;

transition:.3s;

}

.node:hover{

transform:translateY(-4px);

}

.node span{

font-size:15px;

color:#cbd5e1;

font-weight:600;

}

.arrow{

font-size:32px;

color:#22d3ee;

font-weight:700;

}

@media(max-width:900px){

.workflow{

flex-direction:column;

}

.arrow{

transform:rotate(90deg);

}

.node{

width:100%;

}

}

</style>