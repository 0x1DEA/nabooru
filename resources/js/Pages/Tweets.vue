<script setup>
import Layout from '@/Layouts/Layout.vue';
import Tweet from "@/Components/Tweet.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    tweets: Object
})
</script>
<template>
    <Layout title="Tweets">
        <div class="flex justify-center w-full">
            <Pagination :list="tweets"/>
        </div>
        <div class="flex flex-col w-full max-w-4xl">
            <Tweet :timeline="true" v-for="tweet in tweets.data" :tweet="tweet"/>
        </div>
        <div class="flex justify-center w-full">
            <Pagination :list="tweets"/>
        </div>
    </Layout>
</template>
