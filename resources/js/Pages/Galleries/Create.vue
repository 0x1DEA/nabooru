<script setup>
import Layout from "@/Layouts/Layout.vue";
import {useForm} from "@inertiajs/vue3";
import {route} from "ziggy-js";

const props = defineProps({
    galleries: Object
});

const gallery = useForm({
    name: '',
    description: ''
});

const submit = () => {
    gallery.post(route('galleries.store'))
}
</script>
<template>
    <Layout title="Create Gallery">
        <div class="flex flex-col space-y-2 pane">
            <h1 class="font-bold text-4xl">Create Gallery</h1>
            <label class="flex flex-col">
                <span class="text-sm">Name</span>
                <input v-model="gallery.name" type="text" placeholder="Gallery Name" class="rounded border-neutral-700 bg-neutral-900 placeholder-neutral-500"/>
            </label>
            <label class="flex flex-col">
                <span class="text-sm">Description</span>
                <input v-model="gallery.description" type="text" placeholder="Gallery Description" class="rounded border-neutral-700 bg-neutral-900 placeholder-neutral-500"/>
            </label>
            <button @click="submit" class="py-1 px-2 bg-neutral-800 rounded">Create</button>
        </div>
    </Layout>
</template>
