<script setup>
import { computed } from 'vue';

const props = defineProps({
    padding: {
        type: String,
        default: 'p-6'
    },
    shadow: {
        type: String,
        default: 'shadow-sm'
    },
    hover: {
        type: Boolean,
        default: false
    },
    clickable: {
        type: Boolean,
        default: false
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'bordered', 'elevated'].includes(value)
    }
});

defineEmits(['click']);

const cardClasses = computed(() => {
    const baseClasses = 'bg-white dark:bg-gray-800 rounded-lg transition-all duration-200';
    
    const variantClasses = {
        default: 'border border-gray-200 dark:border-gray-700',
        bordered: 'border-2 border-gray-200 dark:border-gray-700',
        elevated: 'border-0'
    };
    
    const hoverClasses = props.hover ? 'hover:shadow-md dark:hover:shadow-gray-900/20' : '';
    const clickableClasses = props.clickable ? 'cursor-pointer hover:scale-[1.02] active:scale-[0.98]' : '';
    
    return [
        baseClasses,
        variantClasses[props.variant],
        props.shadow,
        hoverClasses,
        clickableClasses
    ].filter(Boolean).join(' ');
});
</script>



<template>
    <div
        :class="[cardClasses, padding]"
        @click="clickable ? $emit('click') : null"
        :role="clickable ? 'button' : undefined"
        :tabindex="clickable ? '0' : undefined"
        @keydown.enter="clickable ? $emit('click') : null"
        @keydown.space.prevent="clickable ? $emit('click') : null"
    >
        <slot />
    </div>
</template>