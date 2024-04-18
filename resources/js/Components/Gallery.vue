<script setup>
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    gallery: Object
})

const open = ref(false)

const addToGallery = useForm({
    media_id: props.media.id
})

const submit = () => {

}
</script>
<template>
    <div @click="open = true">
        <div class="relative hover:scale-105 cursor-pointer transition-transform font-mono">
            <span v-if="media.type === 'video'" class="absolute top-1 left-1 text-sm text-mono px-1 py-0.5 rounded bg-neutral-800">VIDEO</span>
            <span v-if="media.type === 'animated_gif'" class="absolute top-1 left-1 text-sm text-mono px-1 py-0.5 rounded bg-neutral-800">GIF</span>
            <img loading="lazy" class="aspect-square w-full object-cover rounded" :src="'/storage/' + media.image_url" alt=""/>
        </div>

        <teleport to="body">
            <div ref="container" @click="open = false" class="cursor-pointer items-center justify-center z-[100] fixed inset-0 bg-black/75" :class="open ? 'flex' : 'hidden'">
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform opacity-0 scale-50"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-50">
                    <div v-if="open" class="rounded overflow-hidden relative h-screen p-12" ref="card">
                        <div class="absolute top-1 left-1 text-white px-2 py-1 rounded bg-neutral-900 border border-neutral-700">
                            <span></span>
                        </div>
                        <img v-if="media.type === 'photo'" class="max-h-full max-w-full" alt="" :src="'/storage/' + media.image_url"/>
                        <video v-else autoplay loop controls>
                            <source :src="'/storage/' + media.media_url" type="video/mp4">
                        </video>
                    </div>
                </transition>
            </div>
        </teleport>
    </div>
</template>
