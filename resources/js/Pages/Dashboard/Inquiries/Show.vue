<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    inquiry: Object
})

function destroy() {

    if (!confirm('Ištrinti užklausą?')) {
        return
    }

    router.delete(`/dashboard/inquiries/${props.inquiry.id}`)
}
</script>

<template>

<AdminLayout>

<div class="flex items-center justify-between mb-10">

    <div>

        <Link
            href="/dashboard/inquiries"
            class="text-cyan-400"
        >
            ← Grįžti
        </Link>

        <h1 class="text-4xl font-bold mt-4">
            {{ inquiry.name }}
        </h1>

    </div>

    <button
        @click="destroy"
        class="
        px-5
        py-3
        rounded-xl

        bg-red-500
        hover:bg-red-600

        transition
        "
    >
        Ištrinti
    </button>

</div>

<div
    class="
    bg-white/5
    border
    border-white/10
    rounded-3xl
    p-8
    space-y-8
    "
>

    <div>

        <p class="text-slate-500">
            El. paštas
        </p>

        <p class="text-xl mt-2">
            {{ inquiry.email }}
        </p>

    </div>

    <div
        v-if="inquiry.phone"
    >

        <p class="text-slate-500">
            Telefonas
        </p>

        <p class="text-xl mt-2">
            {{ inquiry.phone }}
        </p>

    </div>

    <div
        v-if="inquiry.company"
    >

        <p class="text-slate-500">
            Įmonė
        </p>

        <p class="text-xl mt-2">
            {{ inquiry.company }}
        </p>

    </div>

    <div>

        <p class="text-slate-500">
            Paslauga
        </p>

        <p class="text-xl mt-2">
            {{ inquiry.service }}
        </p>

    </div>

    <div>

        <p class="text-slate-500">
            Projekto aprašymas
        </p>

        <div
            class="
            mt-4
            rounded-2xl
            bg-black/20
            p-6
            whitespace-pre-line
            leading-8
            "
        >
            {{ inquiry.message }}
        </div>

    </div>

    <div>

        <p class="text-slate-500">
            Gauta
        </p>

        <p class="mt-2">
            {{ new Date(inquiry.created_at).toLocaleString('lt-LT') }}
        </p>

    </div>

</div>

</AdminLayout>

</template>