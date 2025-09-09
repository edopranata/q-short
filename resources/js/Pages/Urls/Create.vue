<template>
    <Head title="Create Short URL" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Create Short URL
                </h2>
                <Link
                    :href="route('urls.index')"
                    class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to URLs
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Card>
                    <template #title>
                        Create Short URL
                    </template>
                    <template #content>
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Original URL -->
                            <div class="space-y-2">
                                <FloatLabel>
                                    <InputText
                                        id="original_url"
                                        v-model="form.original_url"
                                        type="url"
                                        class="w-full"
                                        placeholder="https://example.com/very-long-url"
                                        required
                                        autofocus
                                    />
                                    <label for="original_url">Original URL *</label>
                                </FloatLabel>
                                <InlineMessage v-if="form.errors.original_url" severity="error" class="block">
                                    {{ form.errors.original_url }}
                                </InlineMessage>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Enter the long URL you want to shorten
                                </p>
                            </div>

                            <!-- Title -->
                            <div class="space-y-2">
                                <FloatLabel>
                                    <InputText
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="w-full"
                                        placeholder="My awesome link"
                                    />
                                    <label for="title">Title (Optional)</label>
                                </FloatLabel>
                                <InlineMessage v-if="form.errors.title" severity="error" class="block">
                                    {{ form.errors.title }}
                                </InlineMessage>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Give your short URL a memorable title
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <FloatLabel>
                                    <Textarea
                                        id="description"
                                        v-model="form.description"
                                        class="block w-full"
                                        rows="3"
                                        placeholder="Brief description of what this link leads to..."
                                    />
                                    <label for="description">Description (Optional)</label>
                                </FloatLabel>
                                <InlineMessage v-if="form.errors.description" severity="error" class="block">
                                    {{ form.errors.description }}
                                </InlineMessage>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Optional description to help you remember what this link is for
                                </p>
                            </div>

                            <!-- Custom Slug -->
                            <div class="space-y-2">
                                <FloatLabel>
                                    <InputText
                                        id="custom_slug"
                                        v-model="form.custom_slug"
                                        type="text"
                                        class="w-full"
                                        placeholder="my-custom-link"
                                        pattern="[a-zA-Z0-9-_]+"
                                    />
                                    <label for="custom_slug">Custom Slug (Optional)</label>
                                </FloatLabel>
                                
                                <!-- Validation Messages -->
                                <div v-if="slugValidation.isChecking" class="flex items-center gap-2 text-sm text-blue-600">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Checking availability...
                                </div>
                                
                                <InlineMessage 
                                    v-else-if="slugValidation.isValid === true" 
                                    severity="success" 
                                    class="block"
                                >
                                    {{ slugValidation.message }}
                                </InlineMessage>
                                
                                <InlineMessage 
                                    v-else-if="slugValidation.isValid === false" 
                                    severity="error" 
                                    class="block"
                                >
                                    {{ slugValidation.message }}
                                </InlineMessage>
                                
                                <InlineMessage v-if="form.errors.custom_slug" severity="error" class="block">
                                    {{ form.errors.custom_slug }}
                                </InlineMessage>
                                
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Create a custom short URL (3-50 characters, letters, numbers, hyphens, and underscores only)
                                </p>
                            </div>

                            <!-- Expiration Date -->
                            <div class="space-y-2">
                                <FloatLabel>
                                    <InputText
                                        id="expires_at"
                                        v-model="form.expires_at"
                                        type="datetime-local"
                                        class="w-full"
                                        :min="minDateTime"
                                    />
                                    <label for="expires_at">Expiration Date (Optional)</label>
                                </FloatLabel>
                                <InlineMessage v-if="form.errors.expires_at" severity="error" class="block">
                                    {{ form.errors.expires_at }}
                                </InlineMessage>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Set when this short URL should stop working (leave empty for no expiration)
                                </p>
                            </div>

                            <!-- Preview Section -->
                            <div v-if="form.original_url" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Preview</h3>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Original URL:</span>
                                        <p class="text-sm text-gray-900 dark:text-gray-100 break-all">{{ form.original_url }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Short URL will be:</span>
                                        <p class="text-sm text-blue-600 dark:text-blue-400 font-mono">
                                            {{ baseUrl }}/s/{{ form.custom_slug || '[random-code]' }}
                                        </p>
                                    </div>
                                    <div v-if="form.title">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Title:</span>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ form.title }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('urls.index')"
                                    class="bg-white dark:bg-gray-700 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200"
                                >
                                    Cancel
                                </Link>
                                <Button
                                type="submit"
                                    :disabled="form.processing"
                                    :loading="form.processing"
                                    class="flex items-center gap-2"
                                >
                                    <svg v-if="!form.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    {{ form.processing ? 'Creating...' : 'Create Short URL' }}
                                </Button>
                            </div>
                        </form>
                    </template>
                </Card>

                <!-- Tips Section -->
                <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">💡 Tips for Better URL Management</h3>
                    <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Use descriptive titles to easily identify your links later</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Set expiration dates for temporary campaigns or time-sensitive content</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Track your link performance with built-in analytics</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>All shortened URLs are automatically secured and monitored</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed, ref, watch } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'
import { useConfirm } from 'primevue/useconfirm'

const toast = useToast()
const confirm = useConfirm()

const form = useForm({
    original_url: '',
    title: '',
    description: '',
    expires_at: '',
    custom_slug: '',
})

const slugValidation = ref({
    isChecking: false,
    isValid: null,
    message: ''
})

const checkSlugAvailability = async (slug) => {
    if (!slug || slug.length < 3) {
        slugValidation.value = {
            isChecking: false,
            isValid: null,
            message: ''
        }
        return
    }

    slugValidation.value.isChecking = true
    
    try {
        const response = await axios.post('/api/check-slug', {
            custom_slug: slug
        })
        
        slugValidation.value = {
            isChecking: false,
            isValid: response.data.available,
            message: response.data.message
        }
    } catch (error) {
        slugValidation.value = {
            isChecking: false,
            isValid: false,
            message: error.response?.data?.message || 'Error checking slug availability'
        }
    }
}

// Watch for changes in custom_slug and validate
watch(() => form.custom_slug, (newSlug) => {
    if (newSlug) {
        // Debounce the API call
        clearTimeout(window.slugCheckTimeout)
        window.slugCheckTimeout = setTimeout(() => {
            checkSlugAvailability(newSlug)
        }, 500)
    } else {
        slugValidation.value = {
            isChecking: false,
            isValid: null,
            message: ''
        }
    }
})

const baseUrl = computed(() => {
    return window.location.origin
})

const minDateTime = computed(() => {
    const now = new Date()
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
    return now.toISOString().slice(0, 16)
})

const submit = () => {
    confirm.require({
        message: `Are you sure you want to create a short URL for "${form.original_url}"?`,
        header: 'Create URL Confirmation',
        icon: 'pi pi-question-circle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Create URL',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-primary',
        accept: () => {
            form.post(route('urls.store'), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Short URL created successfully',
                        life: 3000
                    })
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to create short URL',
                        life: 3000
                    })
                },
                onFinish: () => {
                    // Keep form data on validation errors
                },
            })
        },
        reject: () => {
            toast.add({
                severity: 'info',
                summary: 'Cancelled',
                detail: 'URL creation cancelled',
                life: 2000
            })
        }
    })
}
</script>