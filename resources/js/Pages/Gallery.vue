<script setup>
import Layout from '@/Layouts/Layout.vue';
import Lightbox from "@/Components/Lightbox.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    gallery: Object,
    galleries: Object,
    open: Number,
});
</script>
<template>
    <Layout title="Gallery">
        <div class="flex justify-center w-full">
            <Pagination :list="gallery"/>
        </div>
        <Lightbox :gallery="gallery" :galleries="galleries" :open="!!open" :index="open == 2 ? gallery.data.length - 1 : 0"/>
        <div class="flex justify-center w-full">
            <Pagination :list="gallery"/>
        </div>
    </Layout>
</template>
