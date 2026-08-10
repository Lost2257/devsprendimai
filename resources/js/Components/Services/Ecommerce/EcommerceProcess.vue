<template>

<section class="py-32">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">
            Darbo eiga
        </span>

        <h2 class="section-title">

            Kaip kuriame
            <span>el. parduotuves?</span>

        </h2>

        <p class="section-text">

            Nuo pirmos idėjos iki sėkmingai veikiančios
            internetinės parduotuvės – viskuo pasirūpiname mes.

        </p>

    </div>

    <div class="space-y-8">

        <!-- 1 -->

        <div class="process-card">

            <div class="number">
                01
            </div>

            <div>

                <h3>
                    Analizė
                </h3>

                <p>

                    Išanalizuojame jūsų verslą, produktus,
                    tikslinę auditoriją ir konkurentus, kad
                    sukurtume efektyvią pardavimo strategiją.

                </p>

            </div>

        </div>

        <!-- 2 -->

        <div class="process-card">

            <div class="number">
                02
            </div>

            <div>

                <h3>
                    Dizainas
                </h3>

                <p>

                    Projektuojame modernų ir patogų dizainą,
                    orientuotą į geresnę naudotojo patirtį
                    bei didesnes konversijas.

                </p>

            </div>

        </div>

        <!-- 3 -->

        <div class="process-card">

            <div class="number">
                03
            </div>

            <div>

                <h3>
                    Programavimas
                </h3>

                <p>

                    Integruojame mokėjimų sistemas,
                    pristatymo paslaugas, SEO,
                    Analytics ir visą reikalingą funkcionalumą.

                </p>

            </div>

        </div>

        <!-- 4 -->

        <div class="process-card">

            <div class="number">
                04
            </div>

            <div>

                <h3>
                    Testavimas
                </h3>

                <p>

                    Patikriname visas funkcijas,
                    optimizuojame greitį,
                    užtikriname saugumą ir
                    pasiruošiame paleidimui.

                </p>

            </div>

        </div>

        <!-- 5 -->

        <div class="process-card">

            <div class="number">
                05
            </div>

            <div>

                <h3>
                    Paleidimas
                </h3>

                <p>

                    Paleidžiame parduotuvę,
                    prijungiame Analytics,
                    Pixel ir padedame pradėti
                    sėkmingus internetinius pardavimus.

                </p>

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

    font-size:56px;

    font-weight:900;

    margin-top:30px;

}

.section-title span{

    color:#22d3ee;

}

.section-text{

    max-width:760px;

    margin:28px auto 0;

    color:#94a3b8;

    font-size:20px;

    line-height:1.9;

}

.process-card{

    display:flex;

    gap:32px;

    align-items:flex-start;

    padding:40px;

    border-radius:28px;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    transition:.35s;

}

.process-card:hover{

    transform:translateY(-6px);

    border-color:rgba(34,211,238,.3);

    box-shadow:0 20px 50px rgba(34,211,238,.12);

}

.number{

    flex-shrink:0;

    width:72px;

    height:72px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:22px;

    background:#22d3ee;

    color:#000;

    font-size:24px;

    font-weight:900;

}

.process-card h3{

    font-size:30px;

    font-weight:800;

    margin-bottom:14px;

}

.process-card p{

    color:#94a3b8;

    line-height:1.9;

}

@media(max-width:768px){

.process-card{

    flex-direction:column;

}

}

</style>