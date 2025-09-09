<template>
    <Head title="Create Short URL" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create Short URL
                </h2>
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
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Original URL -->
                            <div>
                                <InputLabel for="original_url" value="Original URL *" />
                                <TextInput
                                    id="original_url"
                                    v-model="form.original_url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    placeholder="https://example.com/very-long-url"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.original_url" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Enter the long URL you want to shorten
                                </p>
                            </div>

                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Title (Optional)" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="My awesome link"
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Give your short URL a memorable title
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Description (Optional)" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                    placeholder="Brief description of what this link leads to..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Optional description to help you remember what this link is for
                                </p>
                            </div>

                            <!-- Expiration Date -->
                            <div>
                                <InputLabel for="expires_at" value="Expiration Date (Optional)" />
                                <TextInput
                                    id="expires_at"
                                    v-model="form.expires_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    :min="minDateTime"
                                />
                                <InputError class="mt-2" :message="form.errors.expires_at" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Set when this short URL should stop working (leave empty for no expiration)
                                </p>
                            </div>

                            <!-- Preview Section -->
                            <div v-if="form.original_url" class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Preview</h3>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-sm text-gray-500">Original URL:</span>
                                        <p class="text-sm text-gray-900 break-all">{{ form.original_url }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Short URL will be:</span>
                                        <p class="text-sm text-blue-600 font-mono">{{ baseUrl }}/s/[random-code]</p>
                                    </div>
                                    <div v-if="form.title">
                                        <span class="text-sm text-gray-500">Title:</span>
                                        <p class="text-sm text-gray-900">{{ form.title }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    {{ form.processing ? 'Creating...' : 'Create Short URL' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Section -->
                <div class="mt-8 bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">💡 Tips for Better URL Management</h3>
                    <ul class="space-y-2 text-sm text-blue-800">
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
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { computed } from 'vue'

const form = useForm({
    original_url: '',
    title: '',
    description: '',
    expires_at: '',
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
    form.post(route('urls.store'), {
        onFinish: () => {
            // Keep form data on validation errors
        },
    })
}
</script>