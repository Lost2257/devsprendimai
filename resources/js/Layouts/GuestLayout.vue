<template>
    <main class="auth-space">
        <div class="auth-grid" aria-hidden="true"></div>
        <div class="auth-beam auth-beam-one" aria-hidden="true"></div>
        <div class="auth-beam auth-beam-two" aria-hidden="true"></div>

        <div class="auth-frame">
            <header class="auth-header">
                <a href="/" class="auth-brand" aria-label="Grįžti į DevSprendimai svetainę">
                    <img src="/images/logo.png" alt="DevSprendimai">
                </a>
                <span><i></i> Saugi administravimo erdvė</span>
            </header>

            <section class="auth-layout">
                <div class="auth-intro">
                    <span class="auth-eyebrow">DevSprendimai · Valdymo centras</span>
                    <h1>Projektai.<br><em>Užklausos.</em><br>Rezultatai.</h1>
                    <p>Viena privati erdvė projektams, klientams ir svetainės turiniui valdyti.</p>

                    <div class="auth-status">
                        <div><span>01</span><strong>Projektų valdymas</strong></div>
                        <div><span>02</span><strong>Klientų užklausos</strong></div>
                        <div><span>03</span><strong>Analitika</strong></div>
                    </div>
                </div>

                <div class="auth-card-wrap">
                    <div class="auth-corner corner-top" aria-hidden="true"></div>
                    <div class="auth-corner corner-bottom" aria-hidden="true"></div>
                    <div class="auth-card">
                        <slot />
                    </div>
                </div>
            </section>

            <footer class="auth-footer">
                <span>DS / ADMIN ACCESS</span>
                <a href="/">Grįžti į svetainę <i>↗</i></a>
            </footer>
        </div>
    </main>
</template>

<style scoped>
.auth-space { position:relative; min-height:100svh; overflow:hidden; color:#eef7ff; background:radial-gradient(circle at 77% 46%,rgba(16,95,119,.22),transparent 33%),linear-gradient(135deg,#02060c,#07101b 54%,#02060b); }
.auth-grid { position:absolute; inset:-30% -15% -20%; opacity:.42; background-image:linear-gradient(rgba(34,211,238,.11) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,.11) 1px,transparent 1px); background-size:58px 58px; mask-image:linear-gradient(to bottom,transparent 3%,black 35%,black 75%,transparent); transform:perspective(600px) rotateX(58deg) translateY(23%); transform-origin:center; animation:grid-drift 16s linear infinite; }
.auth-grid::after { content:''; position:absolute; inset:0; background:linear-gradient(90deg,transparent,rgba(34,211,238,.12),transparent); animation:grid-scan 7s ease-in-out infinite; }
.auth-beam { position:absolute; width:34rem; height:34rem; border-radius:50%; filter:blur(120px); opacity:.13; pointer-events:none; }
.auth-beam-one { right:-10rem; top:-12rem; background:#22d3ee; }
.auth-beam-two { left:-15rem; bottom:-18rem; background:#2563eb; }
.auth-frame { position:relative; z-index:2; display:flex; width:min(calc(100% - 3rem),1320px); min-height:100svh; margin:auto; padding:1.2rem 0; flex-direction:column; }
.auth-header { display:flex; align-items:center; justify-content:space-between; min-height:4.6rem; padding:0 1.25rem; border:1px solid rgba(255,255,255,.09); border-radius:1.2rem; background:rgba(7,13,24,.68); backdrop-filter:blur(20px); }
.auth-brand { display:flex; align-items:center; }
.auth-brand img { width:auto; height:2.35rem; }
.auth-header > span { display:flex; align-items:center; gap:.65rem; color:#718198; font-size:.67rem; font-weight:800; letter-spacing:.13em; text-transform:uppercase; }
.auth-header > span i { width:.4rem; height:.4rem; border-radius:50%; background:#22d3ee; box-shadow:0 0 16px #22d3ee; animation:status-pulse 2.4s ease-in-out infinite; }
.auth-layout { display:grid; grid-template-columns:minmax(0,1fr) minmax(380px,.72fr); align-items:center; gap:clamp(3rem,8vw,8rem); width:min(100% - 2rem,1180px); margin:auto; padding:4rem 0; }
.auth-intro { position:relative; }
.auth-eyebrow { color:#55dff2; font-size:.7rem; font-weight:850; letter-spacing:.2em; text-transform:uppercase; }
.auth-intro h1 { margin-top:1.3rem; font-size:clamp(3.8rem,6.6vw,7rem); font-weight:850; letter-spacing:-.075em; line-height:.82; }
.auth-intro h1 em { color:#22d3ee; font-style:normal; text-shadow:0 0 45px rgba(34,211,238,.2); }
.auth-intro > p { max-width:34rem; margin-top:2rem; color:#8291a6; font-size:1rem; line-height:1.75; }
.auth-status { display:flex; flex-wrap:wrap; gap:.65rem 1.35rem; margin-top:2.2rem; }
.auth-status div { display:flex; align-items:center; gap:.55rem; }
.auth-status span { color:#22d3ee; font-size:.6rem; font-weight:900; }
.auth-status strong { color:#718096; font-size:.68rem; font-weight:750; }
.auth-card-wrap { position:relative; padding:1px; border-radius:1.65rem; background:linear-gradient(145deg,rgba(34,211,238,.5),rgba(255,255,255,.04) 40%,rgba(37,99,235,.25)); box-shadow:0 35px 100px rgba(0,0,0,.4),0 0 80px rgba(34,211,238,.07); }
.auth-card { position:relative; overflow:hidden; padding:clamp(1.6rem,4vw,3rem); border-radius:calc(1.65rem - 1px); background:linear-gradient(145deg,rgba(13,23,38,.95),rgba(5,11,21,.97)); }
.auth-card::before { content:''; position:absolute; inset:0; opacity:.35; background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px); background-size:32px 32px; mask-image:linear-gradient(135deg,black,transparent 72%); pointer-events:none; }
.auth-corner { position:absolute; z-index:3; width:2rem; height:2rem; pointer-events:none; }
.corner-top { top:-.55rem; right:-.55rem; border-top:1px solid #22d3ee; border-right:1px solid #22d3ee; }
.corner-bottom { bottom:-.55rem; left:-.55rem; border-bottom:1px solid #22d3ee; border-left:1px solid #22d3ee; }
.auth-footer { display:flex; align-items:center; justify-content:space-between; padding:.85rem .5rem 0; color:#435167; font-size:.62rem; font-weight:800; letter-spacing:.13em; }
.auth-footer a { display:flex; align-items:center; gap:.45rem; color:#68778d; transition:color .2s ease; }
.auth-footer a:hover { color:#67e8f9; }
.auth-footer i { font-style:normal; }
@keyframes grid-drift { to { background-position:0 58px,58px 0; } }
@keyframes grid-scan { 50% { transform:translateX(35%); } }
@keyframes status-pulse { 50% { opacity:.45; transform:scale(.75); } }
@media (max-width:900px) {
    .auth-layout { grid-template-columns:1fr; gap:2.5rem; width:min(100% - 1rem,560px); padding:3rem 0; }
    .auth-intro { text-align:center; }
    .auth-intro h1 { font-size:clamp(3.4rem,13vw,5.3rem); }
    .auth-intro > p { margin-inline:auto; }
    .auth-status { justify-content:center; }
}
@media (max-width:600px) {
    .auth-frame { width:calc(100% - 1.25rem); padding:.55rem 0 1rem; }
    .auth-header { min-height:4rem; padding:0 .8rem; border-radius:1rem; }
    .auth-brand img { height:2rem; }
    .auth-header > span { font-size:.52rem; letter-spacing:.08em; }
    .auth-layout { gap:1.8rem; padding:2rem 0 1.4rem; }
    .auth-intro h1 { margin-top:.9rem; font-size:clamp(3rem,15vw,4.2rem); }
    .auth-intro > p { margin-top:1.25rem; font-size:.9rem; line-height:1.6; }
    .auth-status { display:none; }
    .auth-card { padding:1.35rem; }
    .auth-footer { padding-inline:.2rem; }
}
@media (max-height:760px) and (min-width:901px) {
    .auth-layout { padding:2rem 0; }
    .auth-intro h1 { font-size:clamp(3.5rem,6vw,5.7rem); }
}
@media (prefers-reduced-motion:reduce) {
    .auth-grid,.auth-grid::after,.auth-header > span i { animation:none; }
}
</style>
