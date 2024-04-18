<script setup>
import { Link, Head } from "@inertiajs/vue3";

const props = defineProps({
    title: String,
})
</script>
<template>
    <div class="dotgrid flex flex-col items-center text-neutral-300 h-screen overflow-y-auto bg-neutral-900">
        <header class="flex justify-center bg-neutral-950 w-full">
            <div class="flex items-center text-white font-mono space-x-1 px-4 py-2 w-full xl:max-w-6xl 2xl:max-w-7xl">
                <Link href="/" class="flex items-center space-x-1 block">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                    </svg>
                    <h1 class="font-bold text-3xl bg-black px-1 py-0 w-fit rounded">Nabooru</h1>
                </Link>
                <div class="flex items-center space-x-4 px-4">
                    <Link href="/tweets">Tweets</Link>
                    <Link href="/gallery">Gallery</Link>
                    <Link href="/users">Users</Link>
                    <span>Settings</span>
                </div>
            </div>
        </header>
        <main class="flex flex-col space-y-4 p-4 w-full xl:max-w-6xl 2xl:max-w-7xl"><slot/></main>
        <footer class="flex justify-center py-4">
            <span class="px-1 font-mono bg-black text-white">&copy; Funny Fish Foftware 2024</span>
        </footer>
    </div>
</template>
<style>
.dotgrid {
    background-image: radial-gradient(#2c2c2c 1.1px, transparent 0);
    background-size: 17px 17px;
    background-position: -16px -20px;
}
</style>
