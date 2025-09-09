<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
        validator: (value) => ['left', 'right'].includes(value)
    },
    width: {
        type: String,
        default: '48',
        validator: (value) => ['48', '56', '64', '72', '80', '96'].includes(value)
    },
    contentClasses: {
        type: String,
        default: 'py-2 bg-white dark:bg-gray-800'
    }
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

const open = ref(false);
const trigger = ref(null);
const dropdown = ref(null);

const closeOnClick = (e) => {
    if (!dropdown.value?.contains(e.target)) {
        open.value = false;
    }
};

const widthClass = computed(() => {
    const widths = {
        '48': 'w-48',
        '56': 'w-56', 
        '64': 'w-64',
        '72': 'w-72',
        '80': 'w-80',
        '96': 'w-96'
    };
    return widths[props.width];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    }
    
    if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    }
    
    return 'origin-top';
});

const toggle = () => {
    open.value = !open.value;
    
    if (open.value) {
        nextTick(() => {
            // Focus first focusable element in dropdown
            const focusableElement = dropdown.value?.querySelector(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            focusableElement?.focus();
        });
    }
};

const handleKeydown = (e) => {
    if (!open.value) return;
    
    const focusableElements = dropdown.value?.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    if (!focusableElements?.length) return;
    
    const currentIndex = Array.from(focusableElements).indexOf(document.activeElement);
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextIndex = currentIndex < focusableElements.length - 1 ? currentIndex + 1 : 0;
        focusableElements[nextIndex].focus();
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevIndex = currentIndex > 0 ? currentIndex - 1 : focusableElements.length - 1;
        focusableElements[prevIndex].focus();
    } else if (e.key === 'Home') {
        e.preventDefault();
        focusableElements[0].focus();
    } else if (e.key === 'End') {
        e.preventDefault();
        focusableElements[focusableElements.length - 1].focus();
    }
};

onMounted(() => {
    document.addEventListener('click', closeOnClick);
    document.addEventListener('keydown', closeOnEscape);
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeOnClick);
    document.removeEventListener('keydown', closeOnEscape);
    document.removeEventListener('keydown', handleKeydown);
});

defineExpose({ open, toggle });
</script>

<template>
    <div ref="dropdown" class="relative">
        <!-- Trigger -->
        <div ref="trigger" @click="toggle">
            <slot name="trigger" :open="open" :toggle="toggle" />
        </div>
        
        <!-- Backdrop -->
        <Transition
            enter-active-class="transition-opacity ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="open"
                class="fixed inset-0 z-[45] bg-black/5 backdrop-blur-sm"
                @click="open = false"
            ></div>
        </Transition>

        <!-- Dropdown Content -->
        <Transition
            enter-active-class="transition-all ease-out duration-300"
            enter-from-class="opacity-0 scale-95 translate-y-[-10px]"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all ease-in duration-200"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-[-10px]"
        >
            <div
                v-show="open"
                :class="[
                    'absolute z-[55] mt-3 rounded-xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 focus:outline-none backdrop-blur-md',
                    'border border-white/20 dark:border-gray-700/50',
                    widthClass,
                    alignmentClasses
                ]"
                role="menu"
                aria-orientation="vertical"
                :aria-labelledby="trigger?.id"
            >
                <div 
                    :class="contentClasses" 
                    class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700"
                >
                    <slot :close="() => open = false" />
                </div>
            </div>
        </Transition>
    </div>
</template>