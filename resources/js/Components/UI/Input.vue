<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: null
    },
    label: {
        type: String,
        default: null
    },
    hint: {
        type: String,
        default: null
    },
    required: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    icon: {
        type: String,
        default: null
    },
    iconPosition: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'right'].includes(value)
    }
});

const emit = defineEmits(['update:modelValue', 'focus', 'blur', 'input']);

const inputRef = ref(null);
const isFocused = ref(false);

const inputClasses = computed(() => {
    const baseClasses = 'block w-full rounded-lg border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-4 py-2.5 text-sm',
        lg: 'px-4 py-3 text-base'
    };
    
    const stateClasses = {
        default: 'border-gray-300 bg-white text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-primary-500',
        error: 'border-red-300 bg-white text-gray-900 placeholder-gray-500 focus:border-red-500 focus:ring-red-500 dark:border-red-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400',
        disabled: 'border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400'
    };
    
    let state = 'default';
    if (props.error) state = 'error';
    if (props.disabled || props.readonly) state = 'disabled';
    
    const iconPadding = props.icon ? 
        (props.iconPosition === 'left' ? 'pl-10' : 'pr-10') : '';
    
    return [
        baseClasses,
        sizeClasses[props.size],
        stateClasses[state],
        iconPadding
    ].filter(Boolean).join(' ');
});

const labelClasses = computed(() => {
    return 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2';
});

const handleInput = (event) => {
    emit('update:modelValue', event.target.value);
    emit('input', event);
};

const handleFocus = (event) => {
    isFocused.value = true;
    emit('focus', event);
};

const handleBlur = (event) => {
    isFocused.value = false;
    emit('blur', event);
};

const focus = () => {
    inputRef.value?.focus();
};

defineExpose({ focus });
</script>

<template>
    <div class="w-full">
        <!-- Label -->
        <label v-if="label" :class="labelClasses">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>
        
        <!-- Input Container -->
        <div class="relative">
            <!-- Left Icon -->
            <div
                v-if="icon && iconPosition === 'left'"
                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
            >
                <i :class="icon" class="h-5 w-5 text-gray-400 dark:text-gray-500" />
            </div>
            
            <!-- Input Field -->
            <input
                ref="inputRef"
                :class="inputClasses"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                @input="handleInput"
                @focus="handleFocus"
                @blur="handleBlur"
            />
            
            <!-- Right Icon -->
            <div
                v-if="icon && iconPosition === 'right'"
                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
            >
                <i :class="icon" class="h-5 w-5 text-gray-400 dark:text-gray-500" />
            </div>
        </div>
        
        <!-- Error Message -->
        <p v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
        
        <!-- Hint Text -->
        <p v-else-if="hint" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ hint }}
        </p>
    </div>
</template>