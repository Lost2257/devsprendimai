<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    users: Array
})

import { router } from '@inertiajs/vue3'

const destroyUser = (id) => {

    if (!confirm('Ar tikrai ištrinti vartotoją?')) {
        return
    }

    router.delete(`/dashboard/users/${id}`)
}

</script>

<template>
    <AdminLayout>

        <div class="flex justify-between items-center mb-8">

            <h1 class="text-3xl font-bold">
                Vartotojai
            </h1>

            <Link
                href="/dashboard/users/create"
                class="
                px-5 py-3
                rounded-xl
                bg-cyan-400
                text-black
                font-semibold
                "
            >
                Naujas vartotojas
            </Link>

        </div>

        <div
            class="
            bg-white/5
            border border-white/10
            rounded-2xl
            overflow-hidden
            "
        >

            <table class="w-full">

                <thead>
                    <tr class="border-b border-white/10">
                        <th class="p-4 text-left">
                            Vardas
                        </th>

                        <th class="p-4 text-left">
                            El. paštas
                        </th>
                        <th class="p-4 text-left">
                            Role
                        </th>
                        <th class="p-4 text-left">
                            Veiksmai
                        </th>
                        <th class="p-4 text-left">
                            Svetainės pav.
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <tr
                        v-for="user in users"
                        :key="user.id"
                    >
                        <td class="p-4">
                            <Link
                                :href="`/dashboard/users/${user.id}`"
                            >
                                {{ user.name }}
                            </Link>
                        </td>

                        <td class="p-4">
                            {{ user.email }}
                        </td>
                        <td class="p-4">
                            {{ user.roles[0]?.name ?? '-' }}
                        </td>
                        <td class="p-4">
                            <Link
                                :href="`/dashboard/users/${user.id}/edit`"
                                class="text-cyan-400 hover:underline"
                            >
                                Redaguoti
                            </Link>
                            <button
                                @click="destroyUser(user.id)"
                                class="text-red-400 ml-4"
                            >
                                Trinti
                            </button>
                        </td>
                        <td class="p-4">
                            {{ user.website_name }}
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </AdminLayout>
</template>