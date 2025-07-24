<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head } from '@inertiajs/vue3';
import Stat from "@/Components/Stat.vue";
import Tweet from "@/Components/Tweet.vue";
import { useDateFormat } from '@vueuse/core'

const props = defineProps({
    tweet: Object,
    quotes: Object,
})

</script>
<template>
    <Head title="Tweet" />

    <Layout>
        <div class="flex space-x-4">
            <div class="flex w-2/7 h-fit flex-col space-y-2 rounded bg-neutral-950 shadow-xl px-4 py-2 border border-neutral-700">
                <div class="flex justify-between">
                    <span class="text-xs font-mono text-neutral-100">User ID: {{ tweet.author.id }}</span>
                    <a :href="'https://twitter.com/i/user/' + tweet.author.id" class="text-xs font-mono underline text-neutral-100">Twitter</a>
                </div>
                <div class="flex flex-col space-y-4">
                    <div class="flex items-center space-x-4">
                        <img class="h-20 w-20 rounded-full bg-neutral-500 border" :src="'/storage/' + tweet.author.avatar_url.replaceAll('normal', 'x96')" alt="">
                        <div class="flex flex-col font-mono">
                            <span class="font-bold text-3xl">{{ tweet.author.name }}</span>
                            <span class="text-neutral-400">@{{ tweet.author.username }}</span>
                        </div>
                    </div>
                    <Stat v-if="tweet.author.bio" name="about" :value="tweet.author.bio"/>
                    <Stat v-if="tweet.author.location" name="location" :value="tweet.author.location"/>
                    <Stat v-if="tweet.author.url" name="url" :value="tweet.author.url"/>
                    <div class="flex space-x-4">
                        <Stat name="following" :value="tweet.author.following_count"/>
                        <Stat name="followers" :value="tweet.author.followers_count"/>
                    </div>
                    <div class="flex space-x-4">
                        <Stat name="likes" :value="tweet.author.likes_count"/>
                        <Stat name="tweets" :value="tweet.author.tweets_count"/>
                        <Stat name="media" :value="tweet.author.media_count"/>
                        <Stat name="lists" :value="tweet.author.listed_count"/>
                    </div>
                </div>
            </div>
            <div class="flex flex-col space-y-4">
                <div class="flex w-5/7 w-fit flex-col space-y-2 rounded bg-neutral-950 shadow-xl px-4 py-2 border border-neutral-700">
                    <div class="flex justify-between">
                        <span class="text-xs font-mono text-neutral-100">Tweet ID: {{ tweet.id }}</span>
                        <a :href="'https://twitter.com/i/status/' + tweet.id" class="text-xs font-mono underline text-neutral-100">Original</a>
                    </div>
                    <p class="text-lg">{{ tweet.text }}</p>
                    <div class="flex space-x-4 w-full overflow-x-auto">
                        <img class="rounded-md w-[32rem]" v-for="media in tweet.media" :src="'/storage/' + media.image_url" alt="">
                    </div>
                    <span v-if="tweet.quote" class="font-bold text-neutral-500 ml-2">QUOTING:</span>
                    <div v-if="tweet.quote" class="border !mt-0 border-neutral-700 bg-neutral-900 rounded-md">
                        <div class="flex items-start space-x-2 border-b border-neutral-700 px-4 py-2">
                            <img class="rounded-full" :src="'/storage/' + tweet.quote.author.avatar_url.replaceAll('normal', 'x96')" alt=""/>
                            <div class="flex font-mono flex-col">
                                <span class="text-lg font-bold">{{ tweet.quote.author.name }}</span>
                                <span class="text-neutral-500 text-sm">@{{ tweet.quote.author.username }}</span>
                            </div>
                        </div>
                        <p class="px-4 py-2">{{ tweet.quote.text }}</p>
                    </div>
                    <details v-if="false" class="text-xs border border-neutral-800 rounded">
                        <summary class="px-2 py-1">Raw Data</summary>
                        <textarea class="w-full rounded bg-black border-none text-xs" readonly>{{ tweet }}</textarea>
                    </details>
                    <div class="flex space-x-2 text-neutral-500">
                        <span>{{ useDateFormat(tweet.created_at, 'h:mm A MMM DD, YYYY').value }}</span>
                        <span>&bull;</span>
                        <div class="text-white" v-html="tweet.device"/>
                    </div>
                    <div class="flex space-x-4">
                        <Stat name="likes" :value="tweet.likes_count"/>
                        <Stat name="retweets" :value="tweet.retweets_count"/>
                        <Stat name="bookmarks" :value="tweet.bookmarks_count"/>
                        <Stat name="replies" :value="tweet.replies_count"/>
                        <Stat name="quotes" :value="tweet.quotes_count"/>
                        <Stat name="views" :value="tweet.views_count"/>
                    </div>
                </div>
                <div class="flex flex-col space-y-2">
                    <Tweet v-for="tweet in quotes" :tweet="tweet"/>
                </div>
            </div>
        </div>
    </Layout>
</template>
