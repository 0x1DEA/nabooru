<script setup>
import {Link, router} from '@inertiajs/vue3';
//import route from "ziggy-js";

const props = defineProps({
    list: Object
})

const promptPage = () => {
    let page = prompt('Jump to page number:')
    if (!page) return;
    let first = props.list.first_page_url.toString();
    let url = first.substring(0, first.length - 1) + page;
    router.get(url);
}
</script>
<template>
    <div class="flex justify-center">
        <div class="flex items-center bg-neutral-950 overflow-hidden rounded-md w-fit border border-neutral-700">
            <template v-if="list.current_page !== 1">
                <Link v-if="list.current_page - 1 !== 1" :href="list.first_page_url" class="hidden sm:flex px-1 hover:text-neutral-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M15.79 14.77a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L11.832 10l3.938 3.71a.75.75 0 01.02 1.06zm-6 0a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L5.832 10l3.938 3.71a.75.75 0 01.02 1.06z" clip-rule="evenodd" />
                    </svg>
                </Link>
                <Link :href="list.prev_page_url" class="hidden sm:flex px-1 hover:text-neutral-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                </Link>
            </template>
            <template v-for="(link, index) in list.links.slice(1, -1)">
                <span v-if="link.label === '...'" @click="promptPage" class="cursor-pointer hover:text-neutral-500" title="Jump to specific page">{{ link.label }}</span>
                <template v-else>
                    <Link v-if="list.links[index + 2].label === '...' || list.links[index].label === '...'" :href="link.url" class="hidden sm:inline px-2 transition-colors hover:text-neutral-500" :class="{'bg-neutral-700': link.active}">
                        {{ link.label }}
                    </Link>
                    <Link v-else :href="link.url" class="px-2 transition-colors hover:text-neutral-500" :class="{'bg-neutral-700': link.active}">
                        {{ link.label }}
                    </Link>
                </template>
            </template>
            <template v-if="list.current_page !== list.last_page">
                <Link :href="list.next_page_url" class="hidden sm:flex px-1 hover:text-neutral-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                </Link>
                <Link v-if="list.current_page + 1 !== list.links.length - 2" :href="list.last_page_url" class="hidden sm:flex px-1 hover:text-neutral-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M10.21 14.77a.75.75 0 01.02-1.06L14.168 10 10.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M4.21 14.77a.75.75 0 01.02-1.06L8.168 10 4.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                </Link>
            </template>
        </div>
    </div>
</template>
