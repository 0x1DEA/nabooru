<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Link } from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import garf from './../../assets/garf.png'

const props = defineProps({
    stats: Object,
    galleries: Object
})

const canvas = ref(0);

onMounted(() => {
    let image = new Image();
    image.src = garf;
    image.addEventListener("load", (e) => {
        /**
         * @type CanvasRenderingContext2D
         */
        let ctx = canvas.value.getContext('2d')

        canvas.value.height = canvas.value.clientHeight;
        canvas.value.width = canvas.value.clientWidth;

        let media = props.stats.media.toString()

        for (let i = 0; i < media.length; i++) {
            drawGarf(ctx, image, i, media[i]);
        }
    });
})

const drawGarf = (ctx, image, x, digit) => {
    let size = 200
    let offset = x * (size - 50)

    ctx.drawImage(image, offset, 0, size, size);
    ctx.font = "5rem monospace";
    ctx.fillText(digit, offset + 100, 77)
}
</script>
<template>
    <Layout title="Home">
        <h2 class="font-bold text-4xl">Home</h2>
        <p class="rounded bg-neutral-950 shadow-xl px-4 py-2 border border-neutral-700">Haiiii :3</p>
        <h2 class="font-bold text-4xl">Stats</h2>
        <span>{{ stats }}</span>
        <canvas ref="canvas" class="h-64"></canvas>
        <h2 class="font-bold text-4xl">User Galleries</h2>
        <Link v-for="gallery in galleries" :href="`/gallery/${gallery.id}`" class="flex flex-col rounded bg-neutral-950 shadow-xl px-4 py-2 border border-neutral-700">
            <h2 class="font-bold text-2xl">{{ gallery.name }}</h2>
            <p>{{ gallery.description }}</p>
        </Link>
    </Layout>
</template>
