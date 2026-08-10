<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    user: Object,
    roles: Array
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.roles[0]?.name ?? 'user',

    website_name: props.user.website_name ?? '',
    website_login: props.user.website_login ?? '',
    website_password: props.user.website_password ?? '',
    services: props.user.services ?? ''
})

const submit = () => {
    form.put(`/dashboard/users/${props.user.id}`)
}
</script>

<template>
    <AdminLayout>

        <div class="max-w-2xl">

            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">
                    Redaguoti vartotoją
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
                <div>
                    <label class="block mb-2 text-slate-400">
                        Vardas
                    </label>

                    <input
                        v-model="form.name"
                        class="input"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-slate-400">
                        El. paštas
                    </label>

                    <input
                        v-model="form.email"
                        class="input"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-slate-400">
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

                <div>
                    <label class="block mb-2 text-slate-400">
                        Svetainės pavadinimas
                    </label>

                    <input
                        v-model="form.website_name"
                        class="input"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-slate-400">
                        Svetainės prisijungimas
                    </label>

                    <input
                        v-model="form.website_login"
                        class="input"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-slate-400">
                        Svetainės slaptažodis
                    </label>

                    <input
                        v-model="form.website_password"
                        class="input"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-slate-400">
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
                    px-6 py-3
                    rounded-xl
                    bg-cyan-400
                    text-black
                    font-semibold
                    "
                >
                    Išsaugoti
                </button>

            </form>

        </div>

    </AdminLayout>
</template>

<style scoped>
.input{
    width:100%;
    padding:14px 16px;

    background:rgba(255,255,255,.04);

    border:1px solid rgba(255,255,255,.08);

    border-radius:12px;

    color:white;
}
</style>