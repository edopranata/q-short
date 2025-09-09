<template>
    <Head title="Edit URL" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit URL
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('analytics.show', url.id)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Analytics
                    </Link>
                    <Link
                        :href="route('urls.index')"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
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
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Edit Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-6">Edit URL Details</h3>
                                
                                <form @submit.prevent="submit" class="space-y-6">
                                    <!-- Title -->
                                    <div>
                                        <InputLabel for="title" value="Title" />
                                        <TextInput
                                            id="title"
                                            v-model="form.title"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="My awesome link"
                                        />
                                        <InputError class="mt-2" :message="form.errors.title" />
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <textarea
                                            id="description"
                                            v-model="form.description"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            rows="3"
                                            placeholder="Brief description of what this link leads to..."
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <InputLabel for="is_active" value="Status" />
                                        <div class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input
                                                    id="is_active"
                                                    v-model="form.is_active"
                                                    type="checkbox"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                >
                                                <span class="ml-2 text-sm text-gray-600">
                                                    Active (URL can be accessed)
                                                </span>
                                            </label>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.is_active" />
                                    </div>

                                    <!-- Expiration Date -->
                                    <div>
                                        <InputLabel for="expires_at" value="Expiration Date" />
                                        <TextInput
                                            id="expires_at"
                                            v-model="form.expires_at"
                                            type="datetime-local"
                                            class="mt-1 block w-full"
                                            :min="minDateTime"
                                        />
                                        <InputError class="mt-2" :message="form.errors.expires_at" />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Leave empty for no expiration
                                        </p>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="flex items-center justify-end gap-4">
                                        <Link
                                            :href="route('urls.index')"
                                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                                        >
                                            Cancel
                                        </Link>
                                        <PrimaryButton
                                            :class="{ 'opacity-25': form.processing }"
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
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- URL Info Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">URL Information</h3>
                                
                                <div class="space-y-4">
                                    <!-- Original URL -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Original URL</dt>
                                        <dd class="mt-1 text-sm text-gray-900 break-all">
                                            <a :href="url.original_url" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                {{ url.original_url }}
                                            </a>
                                        </dd>
                                    </div>

                                    <!-- Short URL -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Short URL</dt>
                                        <dd class="mt-1">
                                            <div class="flex items-center gap-2">
                                                <code class="text-sm bg-gray-100 px-2 py-1 rounded flex-1">
                                                    {{ url.short_url }}
                                                </code>
                                                <button
                                                    @click="copyToClipboard(url.short_url)"
                                                    class="text-gray-400 hover:text-gray-600 transition-colors"
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
                                        <dt class="text-sm font-medium text-gray-500">Total Clicks</dt>
                                        <dd class="mt-1 text-2xl font-bold text-gray-900">{{ url.click_count }}</dd>
                                    </div>

                                    <!-- Created Date -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(url.created_at) }}</dd>
                                    </div>

                                    <!-- Last Updated -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(url.updated_at) }}</dd>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                                        <dd class="mt-1">
                                            <span
                                                :class="{
                                                    'bg-green-100 text-green-800': url.is_active,
                                                    'bg-red-100 text-red-800': !url.is_active
                                                }"
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                            >
                                                {{ url.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </dd>
                                    </div>

                                    <!-- Expiration -->
                                    <div v-if="url.expires_at">
                                        <dt class="text-sm font-medium text-gray-500">Expires</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(url.expires_at) }}</dd>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h4>
                                    <div class="space-y-2">
                                        <a
                                            :href="url.short_url"
                                            target="_blank"
                                            class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 7l10 10M17 7v4" />
                                            </svg>
                                            Test Link
                                        </a>
                                        <Link
                                            :href="route('analytics.show', url.id)"
                                            class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            View Analytics
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { computed } from 'vue'

const props = defineProps({
    url: Object,
})

const form = useForm({
    title: props.url.title || '',
    description: props.url.description || '',
    is_active: props.url.is_active,
    expires_at: props.url.expires_at ? new Date(props.url.expires_at).toISOString().slice(0, 16) : '',
})

const minDateTime = computed(() => {
    const now = new Date()
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
    return now.toISOString().slice(0, 16)
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
    form.put(route('urls.update', props.url.id), {
        onFinish: () => {
            // Keep form data on validation errors
        },
    })
}
</script>