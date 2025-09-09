<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/UI/Card.vue';
import Button from '@/Components/UI/Button.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalUrls: 0,
            totalClicks: 0,
            todayClicks: 0
        })
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <Card class="mb-8">
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            Welcome to Q-Short!
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                            Shorten your URLs and track their performance with ease.
                        </p>
                        <Button as="a" :href="route('urls.create')" size="lg">
                            Create Short URL
                        </Button>
                    </div>
                </Card>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <Card>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                                {{ stats.totalUrls }}
                            </div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Total URLs
                            </div>
                        </div>
                    </Card>
                    
                    <Card>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                                {{ stats.totalClicks }}
                            </div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Total Clicks
                            </div>
                        </div>
                    </Card>
                    
                    <Card>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                                {{ stats.todayClicks }}
                            </div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Today's Clicks
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Quick Actions -->
                <Card>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Quick Actions
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Button as="a" :href="route('urls.create')" variant="outline" class="justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create URL
                        </Button>
                        
                        <Button as="a" :href="route('urls.index')" variant="outline" class="justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            View All URLs
                        </Button>
                        
                        <Button as="a" :href="route('profile.edit')" variant="outline" class="justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Edit Profile
                        </Button>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
