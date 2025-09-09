<template>
    <Head title="Edit URL" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit URL
                </h2>
                <div class="flex gap-2">
                    <Link
                        v-if="url.id"
                        :href="route('analytics.show', { shortenedUrl: url.id })"
                        class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Analytics
                    </Link>
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
            </div>
        </template>

        <div class="py-12">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Edit Form -->
                    <div class="lg:col-span-2">
                        <Card>
                            <template #title>
                                Edit URL Details
                            </template>
                            <template #content>
                                <form @submit.prevent="submit" class="space-y-6">
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
                                            <label for="title">Title</label>
                                        </FloatLabel>
                                        <InlineMessage v-if="form.errors.title" severity="error" class="block">
                                            {{ form.errors.title }}
                                        </InlineMessage>
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
                                            <label for="description">Description</label>
                                        </FloatLabel>
                                        <InlineMessage v-if="form.errors.description" severity="error" class="block">
                                            {{ form.errors.description }}
                                        </InlineMessage>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <div class="mt-2">
                                            <label class="inline-flex items-center">
                                                <Checkbox
                                                    id="is_active"
                                                    v-model="form.is_active"
                                                    :binary="true"
                                                />
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                    Active (URL can be accessed)
                                                </span>
                                            </label>
                                        </div>
                                        <InlineMessage v-if="form.errors.is_active" severity="error" class="mt-2">
                                            {{ form.errors.is_active }}
                                        </InlineMessage>
                                    </div>

                                    <!-- Expiration Date -->
                                    <div class="space-y-2">
                                        <FloatLabel>
                                            <DatePicker
                                                id="expires_at"
                                                v-model="form.expires_at"
                                                showTime
                                                hourFormat="24"
                                                dateFormat="yy-mm-dd"
                                                :minDate="minDate"
                                                class="w-full"
                                                placeholder="Select expiration date and time"
                                                :showIcon="true"
                                                iconDisplay="input"
                                            />
                                            <label for="expires_at">Expiration Date</label>
                                        </FloatLabel>
                                        <InlineMessage v-if="form.errors.expires_at" severity="error" class="block">
                                            {{ form.errors.expires_at }}
                                        </InlineMessage>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Leave empty for no expiration
                                        </p>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="flex items-center justify-end gap-4">
                                        <Link
                                            :href="route('urls.index')"
                                            class="bg-white dark:bg-gray-700 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                                        >
                                            Cancel
                                        </Link>
                                        <Button
                                            type="submit"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                            class="flex items-center gap-2"
                                        >
                                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ form.processing ? 'Updating...' : 'Update URL' }}
                                        </Button>
                                    </div>
                                </form>
                            </template>
                        </Card>
                    </div>

                    <!-- URL Info Sidebar -->
                    <div class="lg:col-span-1">
                        <Card>
                            <template #title>
                                URL Information
                            </template>
                            <template #content>
                                <div class="space-y-4">
                                    <!-- Original URL -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Original URL</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 break-all">
                                            <a :href="url.original_url" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ url.original_url }}
                                            </a>
                                        </dd>
                                    </div>

                                    <!-- Short URL -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Short URL</dt>
                                        <dd class="mt-1">
                                            <div class="flex items-center gap-2">
                                                <code class="text-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-100 px-2 py-1 rounded flex-1">
                                                    {{ url.short_url }}
                                                </code>
                                                <button
                                                    @click="copyToClipboard(url.short_url)"
                                                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                                                    title="Copy to clipboard"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </dd>
                                    </div>

                                    <!-- Click Count -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clicks</dt>
                                        <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ url.click_count }}</dd>
                                    </div>

                                    <!-- Created Date -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(url.created_at) }}</dd>
                                    </div>

                                    <!-- Last Updated -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(url.updated_at) }}</dd>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Status</dt>
                                        <dd class="mt-1">
                                            <span
                                                :class="{
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': url.is_active,
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': !url.is_active
                                                }"
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                            >
                                                {{ url.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </dd>
                                    </div>

                                    <!-- Expiration -->
                                    <div v-if="url.expires_at">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expires</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(url.expires_at) }}</dd>
                                    </div>

                                    <!-- QR Code -->
                                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 text-center mb-3">QR Code</dt>
                                        <div class="flex justify-center">
                                            <QRCode 
                                                :url="url.short_url" 
                                                :size="150"
                                                
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Quick Actions</h4>
                                    <div class="space-y-2">
                                        <a
                                            :href="url.short_url"
                                            target="_blank"
                                            class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 7l10 10M17 7v4" />
                                            </svg>
                                            Test Link
                                        </a>
                                        <Link
                                            v-if="url.id"
                            :href="route('analytics.show', { shortenedUrl: url.id })"
                            class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            View Analytics
                                        </Link>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useConfirm } from 'primevue/useconfirm'
import QRCode from '@/Components/QRCode.vue'

const props = defineProps({
    url: Object,
})

const toast = useToast()
const confirm = useConfirm()

const form = useForm({
    original_url: props.url.original_url,
    title: props.url.title || '',
    description: props.url.description || '',
    is_active: props.url.is_active,
    expires_at: props.url.expires_at ? new Date(props.url.expires_at) : null,
})

const minDate = computed(() => {
    return new Date()
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        // You could add a toast notification here
    } catch (err) {
        console.error('Failed to copy: ', err)
    }
}

const submit = () => {
    confirm.require({
        message: `Are you sure you want to update URL "${props.url.title || props.url.original_url}"?`,
        header: 'Update URL Confirmation',
        icon: 'pi pi-question-circle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Update URL',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-primary',
        accept: () => {
            form.put(route('urls.update', props.url.id), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'URL updated successfully',
                        life: 3000
                    })
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to update URL',
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
                detail: 'URL update cancelled',
                life: 2000
            })
        }
    })
}
</script>