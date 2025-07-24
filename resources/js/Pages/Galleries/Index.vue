<script setup>
import Layout from "@/Layouts/Layout.vue";

const props = defineProps({
    galleries: Object
})
</script>
<template>
    <Layout title="Create Gallery">
        <div class="flex flex-col space-y-4">
            <div v-for="g in galleries" class="pane">
                <h1>{{ g.name }}</h1>
                <p v-if="g.description">{{ g.description }}</p>
            </div>
        </div>
    </Layout>
</template>
