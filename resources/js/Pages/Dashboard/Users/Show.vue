<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    user: Object
})
</script>

<template>
    <AdminLayout>

        <h1 class="text-3xl font-bold mb-8">
            {{ user.name }}
        </h1>

        <div
            class="
            bg-white/5
            border border-white/10
            rounded-2xl
            p-8
            space-y-6
            "
        >

            <div>
                <p class="text-slate-400">Email</p>
                <p>{{ user.email }}</p>
            </div>

            <div>
                <p class="text-slate-400">Rolė</p>
                <p>{{ user.roles[0]?.name }}</p>
            </div>

            <div>
                <p class="text-slate-400">Svetainė</p>
                <p>{{ user.website_name }}</p>
            </div>

            <div>
                <p class="text-slate-400">Prisijungimas</p>
                <p>{{ user.website_login }}</p>
            </div>

            <div>
                <p class="text-slate-400">Slaptažodis</p>
                <p>{{ user.website_password }}</p>
            </div>

            <div>
                <p class="text-slate-400">Paslaugos</p>
                <p>{{ user.services }}</p>
            </div>

        </div>

    </AdminLayout>
</template>