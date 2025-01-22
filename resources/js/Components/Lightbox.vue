<script setup>
import {computed, nextTick, onMounted, ref, watch} from "vue";
import {onKeyStroke} from "@vueuse/core";
import {router, useForm} from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";

const props = defineProps({
    gallery: Object,
    galleries: Object,
    open: Boolean,
    index: Number
})

const open = ref(props.open);
const index = ref(props.index ?? 0);
const transitioning = ref(false);

const indexBack = () => {
    if(!open.value) return;
    if (index.value > 0) {
        index.value = index.value - 1;
    } else {
        if (props.gallery.prev_page_url && !transitioning.value) {
            transitioning.value = true;
            router.get(props.gallery.prev_page_url + '&open=2')
        }
    }
}

const indexNext = () => {
    if(!open.value) return;
    if (index.value < props.gallery.data.length - 1) {
        index.value = index.value + 1;
    } else {
        if (props.gallery.next_page_url && !transitioning.value) {
            transitioning.value = true;
            router.get(props.gallery.next_page_url + '&open=1')
        }
    }
}

onKeyStroke('ArrowLeft', (e) => {
    indexBack()
    e.preventDefault()
})
onKeyStroke('ArrowRight', (e) => {
    indexNext()
    e.preventDefault()
})
onKeyStroke('Escape', (e) => {
    open.value = false;
    e.preventDefault()
})

const showThumbnails = ref(true);
const addToGalleryForm = useForm({
    media_id: null
})

const addToGallery = () => {
    addToGalleryForm.media_id = props.gallery.data[index.value].id;

    addToGalleryForm.post('/gallery-item/new', {
        onSuccess: () => {

        },
        onError: (e) => {
            console.log(e);
        }
    })
}

const videoPlayer = ref(null);

const m = computed(() => {
    let media = props.gallery.data[index.value];

    if (media) setSource(media.media_url)

    return media;
})

onMounted(() => {
    nextTick(() => {
        if (index.value < props.gallery.data.length) setSource(props.gallery.data[index.value].media_url);
    })
})

const setSource = (url) => {
    if (!videoPlayer.value) return;

    videoPlayer.value.pause();
    videoPlayer.value.innerHTML = '';

    if (!url) return;

    let source = document.createElement('source');
    source.src = '/storage/' + url;
    source.type = 'video/mp4';

    videoPlayer.value.appendChild(source);
    videoPlayer.value.load();
}

watch(open, (v, old) => {
    if (!videoPlayer.value) return;

    if (v) {
        //videoPlayer.value.play()
    } else {
        videoPlayer.value.pause()
    }
})

const markSensitive = () => {
    m.value.nsfw = !m.value.nsfw;

    router.post('/media/' + m.value.id + '/mark-sensitive')
}
</script>
<template>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-2">
        <div v-for="(media, key) in gallery.data" @click="index = key; open = true" class="select-none relative hover:scale-105 cursor-pointer transition-transform font-mono rounded bg-black overflow-hidden">
            <span v-if="media.type === 'video'" class="absolute z-20 top-1 left-1 text-sm text-mono px-1 py-0.5 rounded bg-neutral-800/75">VIDEO</span>
            <span v-if="media.type === 'animated_gif'" class="absolute z-20 top-1 left-1 text-sm text-mono px-1 py-0.5 rounded bg-neutral-800/75">GIF</span>
            <div v-if="media.nsfw" class="flex flex-col text-white bg-black/25 backdrop-blur-md justify-center space-y-1 absolute items-center text-xs text-center p-4 z-10 inset-0">
                <span style="text-shadow: black 0 0 5px, black 1px 1px 3px">This media may contain sensitive content</span>
                <div class="flex items-center space-x-1 rounded-md px-2 py-1.5 w-fit bg-black/25 hover:bg-black/50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                    </svg>
                    <span style="text-shadow: black 0 0 5px">View</span>
                </div>
            </div>
            <img :style="media.nsfw ? 'filter: blur(0px)' : ''" loading="lazy" class="aspect-square w-full object-cover" :src="'/storage/' + media.image_url" alt=""/>
        </div>
    </div>
    <div v-if="m" v-show="open" class="flex flex-col text-white absolute !m-0 overflow-y-auto items-center justify-center z-[100] fixed inset-0 bg-black/75" :class="open ? 'flex' : 'hidden'">
        <div class="relative z-10 flex items-center space-x-2 justify-between w-full px-6 py-3">
            <div>{{ index + 1 }}/{{ gallery.data.length }}</div>
            <div class="flex items-center space-x-6">
                <div @click="markSensitive" class="group cursor-pointer" :class="m.nsfw ? 'text-red-500' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8" :class="!m.nsfw ? 'group-hover:hidden' : 'hidden group-hover:block'">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8" :class="m.nsfw ? 'group-hover:hidden' : 'hidden group-hover:block'">
                        <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                        <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                        <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                    </svg>
                </div>
                <Dropdown content-classes="bg-neutral-800">
                    <template #trigger>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 cursor-pointer">
                            <path fill-rule="evenodd" d="M19.5 21a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-5.379a.75.75 0 0 1-.53-.22L11.47 3.66A2.25 2.25 0 0 0 9.879 3H4.5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h15Zm-6.75-10.5a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V10.5Z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <template #content>
                        <div @click.stop class="flex flex-col space-y-2 p-2">
                            <label class="flex items-center space-x-1" v-for="g in galleries">
                                <input type="checkbox" class="rounded bg-neutral-950"/>
                                <span>{{ g.name }}</span>
                            </label>
                            <span @click="addToGallery"></span>
                        </div>
                    </template>
                </Dropdown>
                <a :href="'/storage/' + (m.type === 'photo' ? m.image_url : m.media_url)" target="_blank" class="block">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                        <path fill-rule="evenodd" d="M15.75 2.25H21a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 1-1.5 0V4.81L8.03 17.03a.75.75 0 0 1-1.06-1.06L19.19 3.75h-3.44a.75.75 0 0 1 0-1.5Zm-10.5 4.5a1.5 1.5 0 0 0-1.5 1.5v10.5a1.5 1.5 0 0 0 1.5 1.5h10.5a1.5 1.5 0 0 0 1.5-1.5V10.5a.75.75 0 0 1 1.5 0v8.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V8.25a3 3 0 0 1 3-3h8.25a.75.75 0 0 1 0 1.5H5.25Z" clip-rule="evenodd" />
                    </svg>
                </a>
                <svg @click="showThumbnails = !showThumbnails" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 cursor-pointer">
                    <path fill-rule="evenodd" d="M2.625 6.75a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875 0A.75.75 0 0 1 8.25 6h12a.75.75 0 0 1 0 1.5h-12a.75.75 0 0 1-.75-.75ZM2.625 12a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0ZM7.5 12a.75.75 0 0 1 .75-.75h12a.75.75 0 0 1 0 1.5h-12A.75.75 0 0 1 7.5 12Zm-4.875 5.25a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875 0a.75.75 0 0 1 .75-.75h12a.75.75 0 0 1 0 1.5h-12a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                </svg>
                <svg @click="open = false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 cursor-pointer">
                    <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
        <div @click="open = false" class="flex relative justify-center items-center overflow-x-auto h-full w-screen grow p-8">
            <img @click.stop alt="" class="max-w-full max-h-full" :src="'/storage/' + m.image_url"/>
            <div v-show="m.type !== 'photo'" class="flex justify-center bg-blue-500/50 z-10 items-center absolute h-full inset-0 pointer-events-none">
                <video ref="videoPlayer" loop controls class="pointer-events-auto" :autoplay="m.type === 'animated_gif'"></video>
            </div>
        </div>
        <div class="py-4 px-8 w-full max-w-4xl lg:max-w-6xl xl:max-w-7xl">
            <p class="px-2 py-1 rounded bg-neutral-950 border border-neutral-700">{{ m.tweet.text }}<a :href="`https://twitter.com/i/status/${m.tweet.id}`" class="px-2 underline" target="_blank">Original Tweet</a></p>
            <p>{{ m.tweet.updated_at }}</p>
        </div>
        <div v-show="showThumbnails" class="relative z-10 flex space-x-2 w-screen overflow-x-auto h-32">
            <div v-for="(media, key) in gallery.data" @click="index = key" class="relative cursor-pointer w-24 overflow-hidden shrink-0">
                <div class="absolute inset-0 bg-black/25" :class="{'backdrop-blur-md': media.nsfw }"></div>
                <img alt="" class="aspect-square h-24 object-cover" :src="'/storage/' + media.image_url"/>
            </div>
        </div>
        <div v-if="false" @click="indexBack" class="cursor-crosshair absolute left-0 w-1/3 h-full"></div>
        <div v-if="false" @click="indexNext" class="cursor-crosshair absolute right-0 w-1/3 h-full"></div>
        <div class="flex justify-center items-center absolute inset-0 pointer-events-none" v-show="addToGalleryForm.recentlySuccessful">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-32 h-32 text-green-500">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>
    <div v-else>
        nothing here :3
    </div>
</template>
