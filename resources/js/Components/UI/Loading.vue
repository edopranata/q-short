<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    variant: {
        type: String,
        default: 'spinner',
        validator: (value) => ['spinner', 'dots', 'pulse'].includes(value)
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'white', 'gray'].includes(value)
    },
    text: {
        type: String,
        default: null
    },
    overlay: {
        type: Boolean,
        default: false
    }
});

const sizeClasses = computed(() => {
    const sizes = {
        xs: 'h-3 w-3',
        sm: 'h-4 w-4',
        md: 'h-6 w-6',
        lg: 'h-8 w-8',
        xl: 'h-12 w-12'
    };
    return sizes[props.size];
});

const colorClasses = computed(() => {
    const colors = {
        primary: 'text-primary-600 dark:text-primary-400',
        white: 'text-white',
        gray: 'text-gray-600 dark:text-gray-400'
    };
    return colors[props.color];
});

const textSizeClasses = computed(() => {
    const sizes = {
        xs: 'text-xs',
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg',
        xl: 'text-xl'
    };
    return sizes[props.size];
});
</script>

<template>
    <div
        v-if="overlay"
        class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm"
        role="status"
        aria-label="Loading"
    >
        <div class="flex flex-col items-center space-y-4">
            <!-- Spinner Variant -->
            <svg
                v-if="variant === 'spinner'"
                :class="[sizeClasses, colorClasses, 'animate-spin']"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
            </svg>
            
            <!-- Dots Variant -->
            <div v-else-if="variant === 'dots'" class="flex space-x-1">
                <div
                    v-for="i in 3"
                    :key="i"
                    :class="[sizeClasses, colorClasses, 'rounded-full animate-pulse']"
                    :style="{ animationDelay: `${(i - 1) * 0.2}s` }"
                />
            </div>
            
            <!-- Pulse Variant -->
            <div
                v-else-if="variant === 'pulse'"
                :class="[sizeClasses, colorClasses, 'rounded-full animate-ping']"
            />
            
            <p v-if="text" :class="[textSizeClasses, colorClasses, 'font-medium']">
                {{ text }}
            </p>
        </div>
    </div>
    
    <div v-else class="flex items-center space-x-3">
        <!-- Spinner Variant -->
        <svg
            v-if="variant === 'spinner'"
            :class="[sizeClasses, colorClasses, 'animate-spin']"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            role="status"
            aria-label="Loading"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
        </svg>
        
        <!-- Dots Variant -->
        <div v-else-if="variant === 'dots'" class="flex space-x-1" role="status" aria-label="Loading">
            <div
                v-for="i in 3"
                :key="i"
                :class="[sizeClasses, colorClasses, 'rounded-full animate-pulse']"
                :style="{ animationDelay: `${(i - 1) * 0.2}s` }"
            />
        </div>
        
        <!-- Pulse Variant -->
        <div
            v-else-if="variant === 'pulse'"
            :class="[sizeClasses, colorClasses, 'rounded-full animate-ping']"
            role="status"
            aria-label="Loading"
        />
        
        <span v-if="text" :class="[textSizeClasses, colorClasses, 'font-medium']">
            {{ text }}
        </span>
    </div>
</template>