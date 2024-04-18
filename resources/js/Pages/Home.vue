<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    galleries: Object
})
</script>
<template>
    <Layout title="Home">
        <h2 class="font-bold text-4xl">Home</h2>
        <p class="rounded bg-neutral-950 shadow-xl px-4 py-2 border border-neutral-700">Haiiii :3</p>
        <h2 class="font-bold text-4xl">Stats</h2>
        <span>{{ stats }}</span>
        <h2 class="font-bold text-4xl">User Galleries</h2>
        <Link v-for="gallery in galleries" :href="`/gallery/${gallery.id}`" class="flex flex-col rounded bg-neutral-950 shadow-xl px-4 py-2 border border-neutral-700">
            <h2 class="font-bold text-2xl">{{ gallery.name }}</h2>
            <p>{{ gallery.description }}</p>
        </Link>
    </Layout>
</template>
