<template>

<section class="py-32" id="packages">

<div class="max-w-7xl mx-auto px-6">

    <div class="text-center mb-20">

        <span class="section-label">

            Sprendimai verslui

        </span>

        <h2 class="section-title">

            CRM sistema
            <span>pagal jūsų veiklą.</span>

        </h2>

        <p class="section-text">

            Kiekvienas verslas dirba skirtingai.
            Todėl kuriame individualius CRM sprendimus,
            pritaikytus jūsų procesams.

        </p>

    </div>

    <div class="grid lg:grid-cols-3 gap-8">

        <div class="solution-card">

            <div class="icon">🏗</div>

            <h3>Statybos įmonėms</h3>

            <ul>

                <li>✓ Objektų valdymas</li>
                <li>✓ Darbuotojų užduotys</li>
                <li>✓ Sąmatos</li>
                <li>✓ Dokumentai</li>
                <li>✓ Terminų kontrolė</li>

            </ul>

        </div>

        <div class="solution-card featured">

            <div class="badge">

                Populiariausia

            </div>

            <div class="icon">🏢</div>

            <h3>Paslaugų verslams</h3>

            <ul>

                <li>✓ Klientų valdymas</li>
                <li>✓ Projektų eiga</li>
                <li>✓ Užduotys komandai</li>
                <li>✓ Sąskaitos</li>
                <li>✓ Automatizuoti laiškai</li>

            </ul>

        </div>

        <div class="solution-card">

            <div class="icon">🛒</div>

            <h3>Prekybai</h3>

            <ul>

                <li>✓ Užsakymai</li>
                <li>✓ Sandėlio valdymas</li>
                <li>✓ Tiekėjai</li>
                <li>✓ Ataskaitos</li>
                <li>✓ Integracijos</li>

            </ul>

        </div>

    </div>

    <div class="custom-box">

        <h3>

            Neradote savo veiklos?

        </h3>

        <p>

            Sukursime individualią CRM sistemą,
            pritaikytą būtent jūsų įmonės darbo procesams.
            Nėra jokių šablonų ar apribojimų.

        </p>

        <a
            href="#contact"
            class="cta-btn"
        >

            Aptarti projektą

        </a>

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

.solution-card{

position:relative;

padding:42px;

border-radius:28px;

background:rgba(255,255,255,.03);

border:1px solid rgba(255,255,255,.08);

transition:.35s;

}

.solution-card:hover{

transform:translateY(-8px);

border-color:rgba(34,211,238,.35);

box-shadow:0 25px 60px rgba(34,211,238,.15);

}

.featured{

border:1px solid rgba(34,211,238,.4);

box-shadow:0 0 40px rgba(34,211,238,.15);

}

.badge{

position:absolute;

top:20px;
right:20px;

padding:8px 14px;

border-radius:999px;

background:#22d3ee;

color:#000;

font-size:13px;

font-weight:700;

}

.icon{

font-size:44px;

margin-bottom:26px;

}

.solution-card h3{

font-size:30px;

font-weight:800;

margin-bottom:24px;

}

.solution-card ul{

display:flex;

flex-direction:column;

gap:14px;

color:#cbd5e1;

line-height:1.8;

}

.custom-box{

margin-top:80px;

padding:60px;

border-radius:32px;

text-align:center;

background:linear-gradient(
135deg,
rgba(34,211,238,.08),
rgba(255,255,255,.02)
);

border:1px solid rgba(34,211,238,.15);

}

.custom-box h3{

font-size:42px;

font-weight:900;

margin-bottom:20px;

}

.custom-box p{

max-width:760px;

margin:0 auto;

color:#94a3b8;

font-size:20px;

line-height:1.9;

}

.cta-btn{

display:inline-flex;

margin-top:36px;

padding:18px 38px;

border-radius:18px;

background:#22d3ee;

color:#000;

font-weight:700;

transition:.3s;

}

.cta-btn:hover{

transform:translateY(-4px);

box-shadow:0 15px 40px rgba(34,211,238,.35);

}

</style>