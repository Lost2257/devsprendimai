<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    inquiries: Array
})

const newInquiries = props.inquiries.filter(i => !i.is_read)

const readInquiries = props.inquiries.filter(i => i.is_read)
</script>

<template>

<AdminLayout>

<h1 class="text-4xl font-bold mb-8">
    Užklausos
</h1>

<div
    v-if="!inquiries.length"
    class="bg-white/5 border border-white/10 rounded-2xl p-8 text-center text-slate-400"
>
    Kol kas nėra gautų užklausų.
</div>

<div
    v-else
    class="space-y-5"
>

    <Link
        v-for="inquiry in inquiries"
        :key="inquiry.id"
        :href="`/dashboard/inquiries/${inquiry.id}`"
        class="
        block
        bg-white/5
        border
        border-white/10
        rounded-2xl
        p-6

        hover:border-cyan-400/30
        hover:bg-white/10

        transition
        "
    >

        <div class="flex justify-between items-start">

            <div>

                <div class="flex items-center gap-3">

                    <span
                        v-if="!inquiry.is_read"
                        class="w-3 h-3 rounded-full bg-cyan-400"
                    ></span>

                    <h2 class="text-xl font-semibold">
                        {{ inquiry.name }}
                    </h2>

                </div>

                <p class="mt-2 text-slate-400">
                    {{ inquiry.email }}
                </p>

                <div class="mt-3 flex gap-3">

                    <span
                        class="px-3 py-1 rounded-full bg-cyan-400/10 text-cyan-400 text-sm"
                    >
                        {{ inquiry.service }}
                    </span>

                </div>

            </div>

            <div class="text-right">

                <p class="text-slate-500 text-sm">
                    {{ new Date(inquiry.created_at).toLocaleDateString('lt-LT') }}
                </p>

            </div>

        </div>

    </Link>

</div>

</AdminLayout>

</template>