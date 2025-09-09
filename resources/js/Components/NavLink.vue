<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'group relative inline-flex items-center px-4 py-3 text-sm font-semibold text-blue-600 dark:text-blue-400 transition-all duration-300 ease-out'
        : 'group relative inline-flex items-center px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-all duration-300 ease-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <!-- Content -->
        <span class="relative z-10">
            <slot />
        </span>
        
        <!-- Active indicator -->
        <div 
            v-if="active" 
            class="absolute bottom-0 left-1/2 h-0.5 w-8 -translate-x-1/2 rounded-full bg-blue-600 dark:bg-blue-400 transition-all duration-300"
        ></div>
        
        <!-- Hover indicator -->
        <div 
            v-else
            class="absolute bottom-0 left-1/2 h-0.5 w-0 -translate-x-1/2 rounded-full bg-gray-400 dark:bg-gray-500 transition-all duration-300 group-hover:w-6"
        ></div>
        
        <!-- Background hover effect -->
        <div 
            class="absolute inset-0 rounded-lg bg-gray-50 dark:bg-gray-800/50 opacity-0 transition-all duration-300 group-hover:opacity-100"
            :class="{ 'opacity-100 bg-blue-50 dark:bg-blue-900/20': active }"
        ></div>
    </Link>
</template>
