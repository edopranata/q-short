<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    href: {
        type: String,
        default: null
    },
    method: {
        type: String,
        default: 'get'
    },
    as: {
        type: String,
        default: 'a'
    },
    preserveScroll: {
        type: Boolean,
        default: false
    },
    preserveState: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String,
        default: null
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'danger'].includes(value)
    }
});

const emit = defineEmits(['click']);

const classes = computed(() => {
    const baseClasses = 'block w-full px-4 py-2 text-left text-sm leading-5 transition duration-150 ease-in-out focus:outline-none';
    
    const variantClasses = {
        default: 'text-gray-700 hover:bg-gray-100 focus:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:bg-gray-700',
        danger: 'text-red-600 hover:bg-red-50 focus:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 dark:focus:bg-red-900/20'
    };
    
    const disabledClasses = props.disabled 
        ? 'opacity-50 cursor-not-allowed' 
        : 'cursor-pointer';
    
    return [baseClasses, variantClasses[props.variant], disabledClasses].join(' ');
});

const handleClick = (event) => {
    if (props.disabled) {
        event.preventDefault();
        return;
    }
    emit('click', event);
};
</script>

<template>
    <component
        :is="href ? Link : 'button'"
        v-bind="href ? {
            href,
            method,
            as,
            preserveScroll,
            preserveState
        } : {
            type: 'button'
        }"
        :class="classes"
        :disabled="disabled"
        @click="handleClick"
        role="menuitem"
    >
        <div class="flex items-center">
            <!-- Icon -->
            <i v-if="icon" :class="[icon, 'mr-3 h-4 w-4']" />
            
            <!-- Content -->
            <span class="flex-1">
                <slot />
            </span>
        </div>
    </component>
</template>