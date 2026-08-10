<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Prisijungimas | DevSprendimai" />

        <div class="login-heading">
            <span>ADMIN / LOGIN</span>
            <h2>Sveiki sugrįžę.</h2>
            <p>Prisijunkite prie DevSprendimai valdymo centro.</p>
        </div>

        <div v-if="status" class="login-status" role="status">{{ status }}</div>

        <form class="login-form" @submit.prevent="submit">
            <div class="login-field">
                <label for="email">El. paštas</label>
                <div class="input-wrap">
                    <span aria-hidden="true">@</span>
                    <input id="email" v-model="form.email" type="email" required autofocus autocomplete="username" placeholder="vardas@imone.lt" :aria-invalid="!!form.errors.email">
                </div>
                <small v-if="form.errors.email">{{ form.errors.email }}</small>
            </div>

            <div class="login-field">
                <label for="password">Slaptažodis</label>
                <div class="input-wrap">
                    <span aria-hidden="true">••</span>
                    <input id="password" v-model="form.password" type="password" required autocomplete="current-password" placeholder="Įveskite slaptažodį" :aria-invalid="!!form.errors.password">
                </div>
                <small v-if="form.errors.password">{{ form.errors.password }}</small>
            </div>

            <div class="login-options">
                <label class="remember-option">
                    <input v-model="form.remember" type="checkbox" name="remember">
                    <span aria-hidden="true"></span>
                    Prisiminti mane
                </label>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                >
                    Pamiršote slaptažodį?
                </Link>
            </div>

            <button type="submit" :disabled="form.processing">
                <span>{{ form.processing ? 'Jungiamasi...' : 'Prisijungti' }}</span>
                <i aria-hidden="true">→</i>
            </button>
        </form>
    </GuestLayout>
</template>

<style scoped>
.login-heading,.login-form,.login-status { position:relative; z-index:2; }
.login-heading > span { color:#22d3ee; font-size:.62rem; font-weight:900; letter-spacing:.19em; }
.login-heading h2 { margin-top:.8rem; color:#f5f9ff; font-size:clamp(2rem,4vw,2.7rem); font-weight:850; letter-spacing:-.055em; line-height:1; }
.login-heading p { margin-top:.75rem; color:#718097; font-size:.88rem; line-height:1.6; }
.login-status { margin-top:1.2rem; padding:.75rem .9rem; border:1px solid rgba(52,211,153,.25); border-radius:.75rem; color:#6ee7b7; background:rgba(52,211,153,.07); font-size:.78rem; }
.login-form { display:grid; gap:1.1rem; margin-top:2rem; }
.login-field label { display:block; margin-bottom:.45rem; color:#9eacc0; font-size:.7rem; font-weight:800; letter-spacing:.04em; }
.input-wrap { position:relative; }
.input-wrap > span { position:absolute; z-index:2; left:1rem; top:50%; min-width:1.1rem; color:#53637a; font-size:.72rem; font-weight:900; text-align:center; transform:translateY(-50%); }
.input-wrap input { width:100%; min-height:3.45rem; padding:0 1rem 0 3rem; border:1px solid rgba(255,255,255,.1); border-radius:.85rem; outline:none; color:#eef7ff; background:rgba(2,7,14,.72); transition:border-color .2s ease,box-shadow .2s ease,background .2s ease; }
.input-wrap input::placeholder { color:#46546a; }
.input-wrap input:focus { border-color:rgba(34,211,238,.7); background:rgba(3,10,19,.92); box-shadow:0 0 0 3px rgba(34,211,238,.07),0 0 25px rgba(34,211,238,.05); }
.input-wrap input[aria-invalid="true"] { border-color:rgba(251,113,133,.65); }
.login-field small { display:block; margin-top:.4rem; color:#fda4af; font-size:.7rem; }
.login-options { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
.login-options > a { color:#78879b; font-size:.7rem; font-weight:700; transition:color .2s ease; }
.login-options > a:hover { color:#67e8f9; }
.remember-option { display:flex; align-items:center; gap:.55rem; color:#78879b; cursor:pointer; font-size:.7rem; font-weight:700; }
.remember-option input { position:absolute; width:1px; height:1px; opacity:0; }
.remember-option > span { position:relative; width:1.05rem; height:1.05rem; border:1px solid rgba(255,255,255,.18); border-radius:.3rem; background:#050c16; transition:.2s ease; }
.remember-option input:checked + span { border-color:#22d3ee; background:#22d3ee; box-shadow:0 0 14px rgba(34,211,238,.25); }
.remember-option input:checked + span::after { content:''; position:absolute; left:.31rem; top:.12rem; width:.27rem; height:.5rem; border-right:2px solid #041017; border-bottom:2px solid #041017; transform:rotate(42deg); }
.remember-option input:focus-visible + span { outline:2px solid #67e8f9; outline-offset:3px; }
.login-form button { display:flex; align-items:center; justify-content:space-between; min-height:3.65rem; margin-top:.25rem; padding:0 1.2rem; border:0; border-radius:.9rem; color:#031017; background:linear-gradient(100deg,#22d3ee,#67e8f9); font-size:.8rem; font-weight:900; cursor:pointer; box-shadow:0 14px 35px rgba(34,211,238,.15); transition:transform .2s ease,box-shadow .2s ease,opacity .2s ease; }
.login-form button:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 18px 40px rgba(34,211,238,.25); }
.login-form button:disabled { cursor:wait; opacity:.6; }
.login-form button i { font-size:1.15rem; font-style:normal; }
@media (max-width:600px) {
    .login-heading h2 { font-size:2rem; }
    .login-form { margin-top:1.5rem; }
    .login-options { align-items:flex-start; flex-direction:column; gap:.8rem; }
    .input-wrap input { min-height:3.6rem; font-size:1rem; }
}
</style>
