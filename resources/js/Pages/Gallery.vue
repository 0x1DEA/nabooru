<script setup>
import Layout from '@/Layouts/Layout.vue';
import Lightbox from "@/Components/Lightbox.vue";
import Pagination from "@/Components/Pagination.vue";
import {ref, watch} from "vue";

const props = defineProps({
    gallery: Object,
    galleries: Object,
    open: Number,
});

const filters = ref({
    filetypes: {
        photo: true,
        animated_gif: true,
        video: true,
    }
})

watch(filters, (val, old) => {

})

</script>
<template>
    <Layout title="Gallery">
        <div class="flex-col space=y=2 p-2 rounded-md bg-neutral-800 border border-neutral-700">
            <h2 class="font-bold text-2xl">Search Filters</h2>
            <div class="flex space-x-2">
                <div class="flex flex-col space-y-2">
                    <label>
                        <input v-model="filters.filetypes.animated_gif" type="checkbox" value="animated_gif"/>
                        GIF
                    </label>
                    <label>
                        <input v-model="filters.filetypes.video" type="checkbox" value="video"/>
                        Video
                    </label>
                    <label>
                        <input v-model="filters.filetypes.photo" type="checkbox" value="photo"/>
                        Image
                    </label>
                </div>
            </div>
        </div>
        <div class="flex justify-center w-full">
            <Pagination :list="gallery"/>
        </div>
        <Lightbox :gallery="gallery" :galleries="galleries" :open="!!open" :index="open == 2 ? gallery.data.length - 1 : 0"/>
        <div class="flex justify-center w-full">
            <Pagination :list="gallery"/>
        </div>
    </Layout>
</template>
