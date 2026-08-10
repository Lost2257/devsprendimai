<template>

<section class="py-32">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">

            Kūrimo procesas

        </span>

        <h2 class="section-title">

            CRM kuriame
            <span>žingsnis po žingsnio.</span>

        </h2>

        <p class="section-text">

            Kiekviena sistema kuriama individualiai.
            Prieš programuodami suprantame jūsų verslą,
            procesus ir tik tada pradedame kūrimą.

        </p>

    </div>

    <div class="timeline">

        <div class="step">

            <div class="number">01</div>

            <div class="content">

                <h3>Verslo analizė</h3>

                <p>

                    Susipažįstame su jūsų įmonės veikla,
                    darbo eiga ir identifikuojame procesus,
                    kuriuos galima automatizuoti.

                </p>

            </div>

        </div>

        <div class="step">

            <div class="number">02</div>

            <div class="content">

                <h3>CRM projektavimas</h3>

                <p>

                    Suprojektuojame sistemos struktūrą,
                    modulius, vartotojų roles ir visą
                    būsimos sistemos logiką.

                </p>

            </div>

        </div>

        <div class="step">

            <div class="number">03</div>

            <div class="content">

                <h3>Programavimas</h3>

                <p>

                    Kuriame individualų funkcionalumą,
                    integracijas bei automatizacijas,
                    pritaikytas jūsų verslui.

                </p>

            </div>

        </div>

        <div class="step">

            <div class="number">04</div>

            <div class="content">

                <h3>Testavimas ir paleidimas</h3>

                <p>

                    Ištestuojame sistemą,
                    apmokome komandą ir
                    užtikriname sklandų paleidimą.

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

.timeline{

max-width:900px;
margin:90px auto 0;

display:flex;
flex-direction:column;
gap:35px;

}

.step{

display:flex;
gap:30px;

padding:34px;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

border-radius:28px;

transition:.35s;

}

.step:hover{

transform:translateY(-6px);

border-color:rgba(34,211,238,.35);

box-shadow:0 20px 45px rgba(34,211,238,.12);

}

.number{

width:80px;
height:80px;

flex-shrink:0;

display:flex;
align-items:center;
justify-content:center;

border-radius:22px;

background:rgba(34,211,238,.1);

color:#22d3ee;

font-size:26px;

font-weight:800;

}

.content h3{

font-size:30px;

font-weight:800;

margin-bottom:14px;

}

.content p{

color:#94a3b8;

line-height:1.9;

font-size:18px;

}

@media(max-width:768px){

.step{

flex-direction:column;

}

.number{

width:65px;
height:65px;

font-size:22px;

}

.section-title{

font-size:42px;

}

}

</style>