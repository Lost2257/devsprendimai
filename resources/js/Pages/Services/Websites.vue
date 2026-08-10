<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ServicePage from '@/Components/Services/Shared/ServicePage.vue'
import { websites } from '@/data/services'
</script>

<template>
    <PublicLayout>
        <ServicePage :config="websites" />
    </PublicLayout>
</template>
