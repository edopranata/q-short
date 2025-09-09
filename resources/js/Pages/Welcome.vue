<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Button from '@/Components/UI/Button.vue';
import Card from '@/Components/UI/Card.vue';
import Badge from '@/Components/UI/Badge.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const isVisible = ref(false);
const stats = ref([
    { value: '10K+', label: 'URLs Shortened', icon: '🔗' },
    { value: '5K+', label: 'Active Users', icon: '👥' },
    { value: '99.9%', label: 'Uptime', icon: '⚡' },
    { value: '24/7', label: 'Support', icon: '🛠️' }
]);

const features = ref([
    {
        title: 'Fast & Reliable',
        description: 'Lightning-fast URL shortening with 99.9% uptime guarantee.',
        icon: '⚡',
        color: 'bg-gradient-to-r from-yellow-500 to-orange-500'
    },
    {
        title: 'Analytics Dashboard',
        description: 'Track clicks, analyze traffic, and monitor performance in real-time.',
        icon: '📊',
        color: 'bg-gradient-to-r from-blue-500 to-indigo-500'
    },
    {
        title: 'Custom Domains',
        description: 'Use your own domain for branded short links and better trust.',
        icon: '🌐',
        color: 'bg-gradient-to-r from-green-500 to-teal-500'
    },
    {
        title: 'Secure & Private',
        description: 'Enterprise-grade security with SSL encryption and privacy protection.',
        icon: '🔒',
        color: 'bg-gradient-to-r from-purple-500 to-pink-500'
    }
]);

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Q-Shorten - Smart URL Shortener" />
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5 dark:opacity-10"></div>
        
        <!-- Header -->
        <header class="relative z-10 px-6 py-6">
            <div class="mx-auto max-w-7xl flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Q-Shorten</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Smart URL Shortener</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav v-if="canLogin" class="flex items-center space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-md"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
                        >
                            Log in
                        </Link>

                        <!-- Register button hidden - registration disabled -->
                    </template>
                 </nav>
             </div>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10 px-6 py-12">
            <div class="mx-auto max-w-7xl">
                <!-- Hero Content -->
                <div class="text-center mb-16" :class="{ 'animate-fade-in': isVisible }">
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                        Shorten URLs
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Smartly
                        </span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                        Transform long, complex URLs into short, memorable links. Track clicks, analyze performance, and manage your links with our powerful URL shortening platform.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('login')"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login to Get Started
                        </Link>
                        <Link
                            v-else
                            :href="route('dashboard')"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Go to Dashboard
                        </Link>
                        <button class="inline-flex items-center px-6 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            View Demo
                        </button>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16" :class="{ 'animate-slide-up': isVisible }">
                    <div v-for="stat in stats" :key="stat.label" class="text-center p-6 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-gray-700">
                        <div class="text-3xl mb-2">{{ stat.icon }}</div>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ stat.value }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ stat.label }}</div>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" :class="{ 'animate-fade-in-up': isVisible }">
                    <div v-for="feature in features" :key="feature.title" class="group p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-700">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl mb-6 group-hover:scale-110 transition-transform duration-200" :class="feature.color">
                            <span class="text-2xl">{{ feature.icon }}</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ feature.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 py-12 px-6 border-t border-gray-200 dark:border-gray-700">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-3 mb-4 md:mb-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Q-Shorten</span>
                    </div>
                    
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>Powered by Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Background Grid Pattern */
.bg-grid-pattern {
    background-image: 
        linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(60px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}

.animate-slide-up {
    animation: slideUp 1s ease-out 0.2s both;
}

.animate-fade-in-up {
    animation: fadeInUp 1.2s ease-out 0.4s both;
}

/* Hover Effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Custom Gradient Text */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Backdrop Blur Support */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Smooth Transitions */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
