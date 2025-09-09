<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].includes(value)
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    title: {
        type: String,
        default: null
    },
    persistent: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'opened', 'closed']);

const dialog = ref(null);
const isVisible = ref(false);
const isAnimating = ref(false);

const maxWidthClass = computed(() => {
    const widths = {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
    };
    return widths[props.maxWidth];
});

const close = () => {
    if (props.closeable && !isAnimating.value) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show && props.closeable) {
        close();
    }
};

const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget && !props.persistent) {
        close();
    }
};

const trapFocus = (e) => {
    if (!props.show) return;
    
    const focusableElements = dialog.value?.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    if (!focusableElements?.length) return;
    
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    if (e.key === 'Tab') {
        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            }
        } else {
            if (document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
};

watch(
    () => props.show,
    async (show) => {
        if (show) {
            isVisible.value = true;
            isAnimating.value = true;
            document.body.style.overflow = 'hidden';
            
            await nextTick();
            
            // Focus first focusable element
            const focusableElement = dialog.value?.querySelector(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            focusableElement?.focus();
            
            setTimeout(() => {
                isAnimating.value = false;
                emit('opened');
            }, 300);
        } else {
            isAnimating.value = true;
            document.body.style.overflow = '';
            
            setTimeout(() => {
                isVisible.value = false;
                isAnimating.value = false;
                emit('closed');
            }, 300);
        }
    }
);

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    document.addEventListener('keydown', trapFocus);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.removeEventListener('keydown', trapFocus);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="show"
                class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
                scroll-region
            >
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity"
                    @click="handleBackdropClick"
                />
                
                <!-- Modal Container -->
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center sm:p-0"
                    @click="handleBackdropClick"
                >
                    <Transition
                        enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <div
                            v-show="show"
                            ref="dialog"
                            :class="[
                                'relative w-full transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all',
                                maxWidthClass
                            ]"
                            role="dialog"
                            aria-modal="true"
                            :aria-labelledby="title ? 'modal-title' : undefined"
                        >
                            <!-- Header -->
                            <div v-if="title || $slots.header" class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <h3 v-if="title" id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ title }}
                                    </h3>
                                    <slot name="header" />
                                    
                                    <button
                                        v-if="closeable"
                                        @click="close"
                                        class="rounded-md p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-500 dark:hover:text-gray-400"
                                        aria-label="Close modal"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Body -->
                            <div class="px-6 py-4">
                                <slot />
                            </div>
                            
                            <!-- Footer -->
                            <div v-if="$slots.footer" class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                                <slot name="footer" />
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>