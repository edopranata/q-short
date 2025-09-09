<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2 scale-95"
        enter-to-class="opacity-100 transform translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0 scale-100"
        leave-to-class="opacity-0 transform translate-y-2 scale-95"
    >
        <div
            v-if="visible"
            class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button
                                @click="close"
                                class="inline-flex rounded-md p-1.5 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
    message: {
        type: String,
        default: 'Success!'
    },
    duration: {
        type: Number,
        default: 4000 // 4 seconds
    },
    show: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close'])

const visible = ref(false)
let timeoutId = null

const close = () => {
    visible.value = false
    if (timeoutId) {
        clearTimeout(timeoutId)
        timeoutId = null
    }
    emit('close')
}

const show = () => {
    visible.value = true
    
    // Auto close after duration
    if (timeoutId) {
        clearTimeout(timeoutId)
    }
    
    timeoutId = setTimeout(() => {
        close()
    }, props.duration)
}

// Watch for show prop changes
watch(() => props.show, (newValue) => {
    if (newValue) {
        show()
    } else {
        close()
    }
})

// Expose show method for parent components
defineExpose({
    show,
    close
})
</script>