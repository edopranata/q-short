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
        ? 'group relative block w-full text-start text-base font-semibold text-blue-600 dark:text-blue-400 transition-all duration-300 ease-out'
        : 'group relative block w-full text-start text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-all duration-300 ease-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <div class="relative overflow-hidden">
            <!-- Content -->
            <div class="relative z-10 px-6 py-4 flex items-center">
                <!-- Active indicator dot -->
                <div 
                    v-if="active"
                    class="w-2 h-2 rounded-full bg-blue-600 dark:bg-blue-400 mr-4 transition-all duration-300"
                ></div>
                <div 
                    v-else
                    class="w-2 h-2 rounded-full bg-transparent mr-4 transition-all duration-300 group-hover:bg-gray-400 dark:group-hover:bg-gray-500"
                ></div>
                
                <!-- Link text -->
                <span class="flex-1">
                    <slot />
                </span>
                
                <!-- Arrow indicator -->
                <svg 
                    class="w-4 h-4 ml-2 transform transition-transform duration-300 group-hover:translate-x-1"
                    :class="{ 'text-blue-600 dark:text-blue-400': active, 'text-gray-400 dark:text-gray-500': !active }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
            
            <!-- Background effects -->
            <div 
                class="absolute inset-0 bg-gradient-to-r transition-all duration-300"
                :class="{
                    'from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/30 opacity-100': active,
                    'from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 opacity-0 group-hover:opacity-100': !active
                }"
            ></div>
            
            <!-- Left border indicator -->
            <div 
                class="absolute left-0 top-0 h-full transition-all duration-300 rounded-r-full"
                :class="{
                    'w-1 bg-blue-600 dark:bg-blue-400': active,
                    'w-0 bg-gray-400 dark:bg-gray-500 group-hover:w-1': !active
                }"
            ></div>
            
            <!-- Slide-in effect -->
            <div 
                class="absolute inset-0 bg-white/10 dark:bg-white/5 transform transition-transform duration-300 -translate-x-full group-hover:translate-x-0"
                style="background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%)"
            ></div>
        </div>
    </Link>
</template>
