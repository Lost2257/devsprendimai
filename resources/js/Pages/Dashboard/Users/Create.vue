<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    roles: Array
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',

    website_name: '',
    website_login: '',
    website_password: '',
    services: ''
})

const submit = () => {
    form.post('/dashboard/users')
}
</script>

<template>
    <AdminLayout>

        <div class="max-w-2xl">

            <div class="flex items-center justify-between mb-8">

                <h1 class="text-3xl font-bold">
                    Naujas vartotojas
                </h1>

                <Link
                    href="/dashboard/users"
                    class="text-slate-400 hover:text-cyan-400"
                >
                    ← Atgal
                </Link>

            </div>

            <form
                @submit.prevent="submit"
                class="
                bg-white/5
                border border-white/10
                rounded-2xl
                p-8
                space-y-6
                "
            >

                <!-- Name -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        Vardas
                    </label>

                    <input
                        v-model="form.name"
                        type="text"
                        class="input"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        El. paštas
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        class="input"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        Slaptažodis
                    </label>

                    <input
                        v-model="form.password"
                        type="password"
                        class="input"
                    >
                </div>

                <!-- Role -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        Rolė
                    </label>

                    <select
                        v-model="form.role"
                        class="input"
                    >
                        <option
                            v-for="role in roles"
                            :key="role.id"
                            :value="role.name"
                        >
                            {{ role.name }}
                        </option>
                    </select>
                </div>
                <!-- Website Name -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        Svetainės pavadinimas
                    </label>

                    <input
                        v-model="form.website_name"
                        type="text"
                        class="input"
                    >
                </div>

                <!-- Website Login -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        Svetainės prisijungimas
                    </label>

                    <input
                        v-model="form.website_login"
                        type="text"
                        class="input"
                    >
                </div>

                <!-- Website Password -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        Svetainės slaptažodis
                    </label>

                    <input
                        v-model="form.website_password"
                        type="password"
                        autocomplete="new-password"
                        class="input"
                    >
                </div>

                <!-- Services -->
                <div>
                    <label class="block mb-2 text-sm text-slate-400">
                        Paslaugos
                    </label>

                    <textarea
                        v-model="form.services"
                        rows="4"
                        class="input"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="
                    px-6
                    py-3

                    rounded-xl

                    bg-cyan-400
                    text-black

                    font-semibold
                    "
                >
                    Sukurti vartotoją
                </button>

            </form>

        </div>

    </AdminLayout>
</template>

<style scoped>
.input{
    width:100%;

    background:rgba(255,255,255,.04);

    border:1px solid rgba(255,255,255,.08);

    border-radius:12px;

    padding:14px 16px;

    color:white;
}

.input:focus{
    outline:none;

    border-color:#22D3EE;
}
</style>
