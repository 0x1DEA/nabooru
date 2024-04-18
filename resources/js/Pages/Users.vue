<script setup>
import Layout from '@/Layouts/Layout.vue';
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    users: Object
})
</script>
<template>
    <Layout title="Users">
        <div class="flex justify-center w-full">
            <Pagination :list="users"/>
        </div>
        <div class="flex justify-center">
            <div class="grid grid-cols-2  gap-2 w-full max-w-4xl">
                <div v-for="user in users.data" class="flex items-center justify-between rounded bg-neutral-950 border border-neutral-700 px-2 py-1">
                    <img class="h-10 rounded-full bg-neutral-500 border" :src="'/storage/' + user.avatar_url.replaceAll('normal', 'x96')" alt="">
                    <span class="text-sm">@{{ user.username }}</span>
                </div>
            </div>
        </div>
        <div class="flex justify-center w-full">
            <Pagination :list="users"/>
        </div>
    </Layout>
</template>
