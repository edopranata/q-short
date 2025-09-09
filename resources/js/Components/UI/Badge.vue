<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'primary', 'success', 'warning', 'error', 'info'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    rounded: {
        type: Boolean,
        default: false
    },
    removable: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['remove']);

const badgeClasses = computed(() => {
    const baseClasses = 'inline-flex items-center font-medium';
    
    const sizeClasses = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-0.5 text-sm',
        lg: 'px-3 py-1 text-sm'
    };
    
    const variantClasses = {
        default: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
        primary: 'bg-primary-100 text-primary-800 dark:bg-primary-900/20 dark:text-primary-400',
        success: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        error: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        info: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
    };
    
    const roundedClass = props.rounded ? 'rounded-full' : 'rounded';
    
    return [
        baseClasses,
        sizeClasses[props.size],
        variantClasses[props.variant],
        roundedClass
    ].join(' ');
});

const removeButtonClasses = computed(() => {
    const variantClasses = {
        default: 'text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300',
        primary: 'text-primary-400 hover:text-primary-600 dark:text-primary-500 dark:hover:text-primary-300',
        success: 'text-green-400 hover:text-green-600 dark:text-green-500 dark:hover:text-green-300',
        warning: 'text-yellow-400 hover:text-yellow-600 dark:text-yellow-500 dark:hover:text-yellow-300',
        error: 'text-red-400 hover:text-red-600 dark:text-red-500 dark:hover:text-red-300',
        info: 'text-blue-400 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-300'
    };
    
    return `ml-1 inline-flex items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent ${variantClasses[props.variant]}`;
});
</script>

<template>
    <span :class="badgeClasses">
        <slot />
        
        <button
            v-if="removable"
            @click="$emit('remove')"
            :class="removeButtonClasses"
            type="button"
            aria-label="Remove badge"
        >
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </span>
</template>