<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: {
        type: String,
        default: null
    },
    dismissible: {
        type: Boolean,
        default: false
    },
    icon: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['dismiss']);

const alertClasses = computed(() => {
    const baseClasses = 'rounded-lg p-4 border';
    
    const typeClasses = {
        success: 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
        error: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400',
        info: 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400'
    };
    
    return [baseClasses, typeClasses[props.type]].join(' ');
});

const iconClasses = computed(() => {
    const typeClasses = {
        success: 'text-green-400 dark:text-green-500',
        error: 'text-red-400 dark:text-red-500',
        warning: 'text-yellow-400 dark:text-yellow-500',
        info: 'text-blue-400 dark:text-blue-500'
    };
    
    return typeClasses[props.type];
});

const getIcon = computed(() => {
    const icons = {
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        error: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z',
        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    
    return icons[props.type];
});
</script>

<template>
    <div :class="alertClasses" role="alert">
        <div class="flex">
            <!-- Icon -->
            <div v-if="icon" class="flex-shrink-0">
                <svg
                    :class="['h-5 w-5', iconClasses]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        :d="getIcon"
                    />
                </svg>
            </div>
            
            <!-- Content -->
            <div :class="icon ? 'ml-3' : ''" class="flex-1">
                <h3 v-if="title" class="text-sm font-medium">
                    {{ title }}
                </h3>
                <div :class="title ? 'mt-2 text-sm' : 'text-sm'">
                    <slot />
                </div>
            </div>
            
            <!-- Dismiss Button -->
            <div v-if="dismissible" class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button
                        @click="$emit('dismiss')"
                        type="button"
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:opacity-75"
                        :class="{
                            'text-green-500 focus:ring-green-600 focus:ring-offset-green-50 dark:focus:ring-offset-green-900/20': type === 'success',
                            'text-red-500 focus:ring-red-600 focus:ring-offset-red-50 dark:focus:ring-offset-red-900/20': type === 'error',
                            'text-yellow-500 focus:ring-yellow-600 focus:ring-offset-yellow-50 dark:focus:ring-offset-yellow-900/20': type === 'warning',
                            'text-blue-500 focus:ring-blue-600 focus:ring-offset-blue-50 dark:focus:ring-offset-blue-900/20': type === 'info'
                        }"
                        aria-label="Dismiss alert"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>