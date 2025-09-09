<template>
    <Head :title="'URL Details - ' + (url.title || 'Untitled')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        URL Details
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ url.title || 'Untitled' }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('urls.edit', url.id)"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit URL
                    </Link>
                    <Link
                        :href="route('analytics.show', { shortenedUrl: url.id })"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        View Analytics
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
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- URL Information Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">URL Information</h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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

                                <!-- Title -->
                                <div v-if="url.title">
                                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ url.title }}</dd>
                                </div>

                                <!-- Description -->
                                <div v-if="url.description">
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ url.description }}</dd>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Click Count -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Clicks</dt>
                                    <dd class="mt-1 text-3xl font-bold text-gray-900">{{ url.click_count.toLocaleString() }}</dd>
                                </div>

                                <!-- Status -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
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

                                <!-- Expiration -->
                                <div v-if="url.expires_at">
                                    <dt class="text-sm font-medium text-gray-500">Expires</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(url.expires_at) }}</dd>
                                </div>
                            </div>

                            <!-- QR Code Section -->
                            <div class="flex flex-col items-center justify-start">
                                <div class="w-full">
                                    <dt class="text-sm font-medium text-gray-500 text-center mb-4">QR Code</dt>
                                    <QRCode 
                                        :url="url.short_url" 
                                        :size="180"
                                        
                                        class="w-full"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a
                                :href="url.short_url"
                                target="_blank"
                                class="flex items-center justify-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M7 7l10 10M17 7v4" />
                                </svg>
                                Test Link
                            </a>
                            <Link
                                :href="route('analytics.show', { shortenedUrl: url.id })"
                                class="flex items-center justify-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                View Analytics
                            </Link>
                            <Link
                                :href="route('urls.edit', url.id)"
                                class="flex items-center justify-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit URL
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Analytics Summary -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg" v-if="dailyAnalytics && dailyAnalytics.length > 0">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity (Last 30 Days)</h3>
                        <div class="space-y-3">
                            <div v-for="day in dailyAnalytics.slice(0, 10)" :key="day.date" class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ formatDate(day.date) }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ day.clicks }} clicks</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <Link
                                :href="route('analytics.show', { shortenedUrl: url.id })"
                                class="text-sm text-blue-600 hover:text-blue-800"
                            >
                                View detailed analytics →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import QRCode from '@/Components/QRCode.vue'

const props = defineProps({
    url: Object,
    dailyAnalytics: Array,
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
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
</script>