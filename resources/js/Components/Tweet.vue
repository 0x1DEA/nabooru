<script setup>
import {useDateFormat} from "@vueuse/core";
import {Link} from "@inertiajs/vue3";
import Stat from "@/Components/Stat.vue";

const props = defineProps({
    tweet: Object,
    timeline: Boolean
})
</script>
<template>
    <div class="bg-neutral-950 shadow-xl px-4 py-2 border border-neutral-700" :class="{'rounded': !timeline}">
        <div class="flex items-center space-x-2">
            <img loading="lazy" class="h-10 rounded-full" :src="'/storage/' + tweet.author.avatar_url.replaceAll('normal', 'x96')" alt=""/>
            <div class="flex font-mono flex-col">
                <span class="text-lg font-bold">{{ tweet.author.name }}</span>
                <span class="text-neutral-500 text-sm">@{{ tweet.author.username }}</span>
            </div>
        </div>
        <p class="text-lg">{{ tweet.text }}</p>
        <div class="flex space-x-4 w-full overflow-x-auto">
            <img loading="lazy" class="rounded-md h-[10rem]" v-for="media in tweet.media" :src="'/storage/' + media.image_url" alt="">
        </div>
        <span v-if="tweet.quote" class="font-bold text-neutral-500 ml-2">QUOTING:</span>
        <div v-if="tweet.quote" class="border !mt-0 border-neutral-700 bg-neutral-900 rounded-md">
            <div class="flex items-start space-x-2 border-b border-neutral-700 px-4 py-2">
                <img loading="lazy" class="h-10 rounded-full" :src="'/storage/' + tweet.quote.author.avatar_url.replaceAll('normal', 'x96')" alt=""/>
                <div class="flex font-mono flex-col">
                    <span class="text-lg font-bold">{{ tweet.quote.author.name }}</span>
                    <span class="text-neutral-500 text-sm">@{{ tweet.quote.author.username }}</span>
                </div>
            </div>
            <p class="px-4 py-2">{{ tweet.quote.text }}</p>
            <div class="flex space-x-4 w-full overflow-x-auto">
                <img loading="lazy" class="rounded-md h-[10rem]" v-for="media in tweet.quote.media" :src="'/storage/' + media.image_url" alt="">
            </div>
        </div>
        <details v-if="false" class="text-xs border border-neutral-800 rounded">
            <summary class="px-2 py-1">Raw Data</summary>
            <textarea class="w-full rounded bg-black border-none text-xs" readonly>{{ tweet }}</textarea>
        </details>
        <div v-if="false" class="flex text-sm space-x-2 text-neutral-500">
            <span>{{ useDateFormat(tweet.created_at, 'h:mm A MMM DD, YYYY').value }}</span>
            <span>&bull;</span>
            <div class="text-white" v-html="tweet.device"/>
        </div>
        <div class="flex items-center justify-between space-x-2">
            <div class="flex space-x-3 items-end">
                <span class="text-sm text-neutral-500">{{ useDateFormat(tweet.created_at, 'h:mm A MMM DD, YYYY').value }}</span>
                <span>&bull;</span>
                <Stat name="likes" :value="tweet.likes_count">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path d="m9.653 16.915-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
                    </svg>
                </Stat>
                <Stat name="retweets" :value="`${tweet.retweets_count} (${tweet.quotes_count})`">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M10 4.5c1.215 0 2.417.055 3.604.162a.68.68 0 0 1 .615.597c.124 1.038.208 2.088.25 3.15l-1.689-1.69a.75.75 0 0 0-1.06 1.061l2.999 3a.75.75 0 0 0 1.06 0l3.001-3a.75.75 0 1 0-1.06-1.06l-1.748 1.747a41.31 41.31 0 0 0-.264-3.386 2.18 2.18 0 0 0-1.97-1.913 41.512 41.512 0 0 0-7.477 0 2.18 2.18 0 0 0-1.969 1.913 41.16 41.16 0 0 0-.16 1.61.75.75 0 1 0 1.495.12c.041-.52.093-1.038.154-1.552a.68.68 0 0 1 .615-.597A40.012 40.012 0 0 1 10 4.5ZM5.281 9.22a.75.75 0 0 0-1.06 0l-3.001 3a.75.75 0 1 0 1.06 1.06l1.748-1.747c.042 1.141.13 2.27.264 3.386a2.18 2.18 0 0 0 1.97 1.913 41.533 41.533 0 0 0 7.477 0 2.18 2.18 0 0 0 1.969-1.913c.064-.534.117-1.071.16-1.61a.75.75 0 1 0-1.495-.12c-.041.52-.093 1.037-.154 1.552a.68.68 0 0 1-.615.597 40.013 40.013 0 0 1-7.208 0 .68.68 0 0 1-.615-.597 39.785 39.785 0 0 1-.25-3.15l1.689 1.69a.75.75 0 0 0 1.06-1.061l-2.999-3Z" clip-rule="evenodd" />
                    </svg>
                </Stat>
                <Stat name="bookmarks" :value="tweet.bookmarks_count">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M10 2c-1.716 0-3.408.106-5.07.31C3.806 2.45 3 3.414 3 4.517V17.25a.75.75 0 0 0 1.075.676L10 15.082l5.925 2.844A.75.75 0 0 0 17 17.25V4.517c0-1.103-.806-2.068-1.93-2.207A41.403 41.403 0 0 0 10 2Z" clip-rule="evenodd" />
                    </svg>
                </Stat>
                <Stat name="replies" :value="tweet.replies_count">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 0 1-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 0 1 0 10.75H10.75a.75.75 0 0 1 0-1.5h2.875a3.875 3.875 0 0 0 0-7.75H3.622l4.146 3.957a.75.75 0 0 1-1.036 1.085l-5.5-5.25a.75.75 0 0 1 0-1.085l5.5-5.25a.75.75 0 0 1 1.06.025Z" clip-rule="evenodd" />
                    </svg>
                </Stat>
                <Stat v-if="tweet.views_count" name="views" :value="tweet.views_count">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                    </svg>
                </Stat>
            </div>
            <div class="flex space-x-2">
                <a :href="'https://twitter.com/i/status/' + tweet.id" class="text-xs font-mono underline text-neutral-600">{{ tweet.id }}</a>
                <Link :href="'/tweet/' + tweet.id" class="text-xs font-mono underline text-neutral-600">Page</Link>
            </div>
        </div>
    </div>
</template>
