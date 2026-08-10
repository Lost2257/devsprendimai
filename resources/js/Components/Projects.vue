<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination, Autoplay } from 'swiper/modules'
import { Link } from '@inertiajs/vue3'
import { useLocale } from '@/composables/useLocale'

import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
defineProps({
    projects: {
        type: Array,
        default: () => []
    }
})
const { isEnglish, localize } = useLocale()

</script>

<template>
    <section
        id="projects"
        class="py-24 md:py-32"
    >
        <div class="max-w-7xl mx-auto px-6">

            <div class="mb-14 md:mb-20 text-center">
                <span
                    class="text-cyan-400 uppercase tracking-[0.3em] font-semibold"
                >
                    02 · {{ isEnglish ? 'Work' : 'Darbai' }}
                </span>

                <h2
                    class="text-4xl md:text-6xl font-bold mt-4"
                >
                    {{ isEnglish ? 'Latest' : 'Naujausi' }}
                <span class="text-cyan-400">
                    {{ isEnglish ? 'projects' : 'projektai' }}
                </span>
                    
                </h2>
            </div>

        <div v-if="projects.length" class="project-slider">

    <Swiper
        :modules="[Navigation, Pagination, Autoplay]"
        :slides-per-view="1"
        :space-between="24"
        :loop="projects.length > 3"
        :autoplay="{
            delay: 4500
        }"
        :breakpoints="{
            768: {
                slidesPerView: 2
            },
            1280: {
                slidesPerView: 3
            }
        }"
        navigation
        pagination
    >

        <SwiperSlide
            v-for="project in projects"
            :key="project.id"
        >

            <Link
                :href="localize(`/projektai/${project.slug}`)"
                :prefetch="['hover', 'click']"
                cache-for="30s"
                view-transition
                class="project-card"
            >

            <div class="project-showcase">

                <img
                    :src="`/storage/${project.thumbnail}`"
                    class="project-desktop"
                    :alt="`${project.title} ${isEnglish ? 'project preview' : 'projekto peržiūra'}`"
                    loading="lazy"
                >

            </div>

                <div class="project-overlay">

                    <span class="project-client">
                        {{ project.services }}
                    </span>

                    <h3>
                        {{ project.title }}
                    </h3>
                    <div class="project-visit">
                        {{ isEnglish ? 'View project' : 'Peržiūrėti' }} →
                    </div>

                </div>

            </Link>

        </SwiperSlide>

    </Swiper>

</div>

        <div v-else class="empty-projects">
            <span>{{ isEnglish ? 'Project gallery is being updated' : 'Projektų galerija pildoma' }}</span>
            <p>{{ isEnglish ? 'Explore our services or tell us about your idea — the next project shown here could be yours.' : 'Kol kas susipažinkite su paslaugomis arba parašykite apie savo idėją — galbūt kitas čia rodomas projektas bus jūsų.' }}</p>
            <a href="#contact">{{ isEnglish ? 'Discuss a project' : 'Aptarti projektą' }} →</a>
        </div>

        </div>
    </section>
</template>

<style scoped>
.project-card{
    display:block;

    background:rgba(255,255,255,.03);

    border:1px solid rgba(255,255,255,.08);

    border-radius:32px;

    overflow:hidden;

    transition:.35s;
    height:100%;
}

.empty-projects {
    max-width: 760px;
    margin: 0 auto;
    padding: 3rem;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 1.5rem;
    background: rgba(255,255,255,.025);
    text-align: center;
}

.empty-projects span { color: #67e8f9; font-size: .75rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
.empty-projects p { margin-top: 1rem; color: #8795aa; line-height: 1.7; }
.empty-projects a { display: inline-flex; margin-top: 1.5rem; color: #67e8f9; font-weight: 800; }

.project-card:hover{
    transform:translateY(-10px);

    border-color:rgba(34,211,238,.35);

    box-shadow:
        0 0 50px rgba(34,211,238,.12);

    transform:translateY(-6px);
}

.project-showcase{
    position:relative;

    aspect-ratio: 16 / 10;

    overflow:hidden;

    background:#111827;
}

.project-desktop{
    width:100%;
    height:100%;

    object-fit:cover;

    display:block;

    transition:.6s;

}

.project-card:hover .project-desktop{
    transform:
        perspective(1200px)
        rotateY(-3deg);
}


.project-overlay{
    padding:24px;

    position:relative;

    background:transparent;
}

.project-client{
    color:#22D3EE;

    font-size:12px;

    text-transform:uppercase;



}

.project-overlay h3{
    margin-top:8px;

    font-size:26px;
    font-weight:700;
    line-height: 1.2;
}

.project-overlay p{
    margin-top:12px;

    color:#CBD5E1;

    line-height:1.6;
}

:deep(.swiper-button-next),
:deep(.swiper-button-prev){
    color:#22D3EE;
}

:deep(.swiper-pagination-bullet){
    background:#22D3EE;
}

.project-visit{
    margin-top:18px;

    color:#22D3EE;

    opacity:0;

    transform:translateY(10px);

    transition:.3s;
}

.project-card:hover .project-visit{
    opacity:1;
    transform:none;
}

.project-card:hover .project-desktop{
    transform:scale(1.05);
}
@media (max-width: 767px) {
    .project-card:hover {
        transform: none;
        box-shadow: none;
    }

    .project-type {
        margin-bottom: 8px;
    }

    .project-title {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .project-desc {
        max-width: none;
        line-height: 1.65;
    }

    .project-arrow {
        font-size: 32px;
        align-self: flex-end;
    }

    .empty-projects { padding: 2rem 1.25rem; }
}

@media (max-width:768px){

    .project-showcase{
        min-height:auto;
        padding:0;
    }

    .project-overlay{
        padding:
            35px
            20px
            20px;
    }

    .project-overlay h3{
        font-size:24px;
    }

    :deep(.swiper-button-next),
    :deep(.swiper-button-prev){
        display:none;
    }

    :deep(.swiper){
        padding-bottom:36px;
    }
}

</style>
