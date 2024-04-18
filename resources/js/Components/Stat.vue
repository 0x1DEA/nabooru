<script setup>
const props = defineProps({
    name: String,
    value: [String, Number]
})
</script>
<template>
    <div class="leading-none text-neutral-500" :class="{'flex items-center space-x-1': Object.keys($slots).length > 0}">
        <span v-if="Object.keys($slots).length === 0" class="text-neutral-500 text-xs font-bold uppercase">{{ name }}:</span>
        <slot v-else/>
        <p class="text-sm">{{ value }}</p>
    </div>
</template>
