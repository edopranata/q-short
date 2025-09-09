<template>
    <Head :title="`Analytics - ${url.title || 'Untitled'}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        URL Analytics
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
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
                        :href="route('analytics.index')"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Analytics
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- URL Info Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-8">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">URL Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Original URL</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 break-all">
                                            <a :href="url.original_url" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ url.original_url }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Short URL</dt>
                                        <dd class="mt-1">
                                            <div class="flex items-center gap-2">
                                                <code class="text-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-200 px-2 py-1 rounded">
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
                                    <div v-if="url.description">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ url.description }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Quick Stats</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clicks</dt>
                                        <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ total_clicks.toLocaleString() }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(url.created_at) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
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
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Clicks Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daily Clicks (Last 30 Days)</h3>
                        <div class="h-64">
                            <canvas 
                                ref="dailyChart"
                                role="img"
                                :aria-label="`Daily clicks chart showing ${chartData.length} days of data with maximum ${maxClicks.value} clicks`"
                                tabindex="0"
                            ></canvas>
                            <!-- Screen reader accessible data table -->
                            <div class="sr-only">
                                <table>
                                    <caption>Daily clicks data</caption>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Clicks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="point in chartData" :key="point.date">
                                            <td>{{ new Date(point.date).toLocaleDateString() }}</td>
                                            <td>{{ point.clicks }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Browser Stats -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Browsers</h3>
                            <div class="space-y-3">
                                <div v-for="(count, browser) in analytics.browser_stats" :key="browser" class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ browser || 'Unknown' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ count }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ({{ Math.round((count / total_clicks) * 100) }}%)
                                        </span>
                                    </div>
                                </div>
                                <div v-if="Object.keys(analytics.browser_stats).length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    No browser data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Stats -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Platforms</h3>
                            <div class="space-y-3">
                                <div v-for="(count, platform) in analytics.platform_stats" :key="platform" class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ platform || 'Unknown' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ count }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ({{ Math.round((count / total_clicks) * 100) }}%)
                                        </span>
                                    </div>
                                </div>
                                <div v-if="Object.keys(analytics.platform_stats).length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    No platform data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Device Stats -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Device Types</h3>
                            <div class="space-y-3">
                                <div v-for="(count, device) in analytics.device_stats" :key="device" class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                        <span class="text-sm text-gray-900 dark:text-gray-100 capitalize">{{ device || 'Unknown' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ count }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ({{ Math.round((count / total_clicks) * 100) }}%)
                                        </span>
                                    </div>
                                </div>
                                <div v-if="Object.keys(analytics.device_stats).length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    No device data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Country Stats -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Top Countries</h3>
                            <div class="space-y-3">
                                <div v-for="stat in countryStatsWithPercentage" :key="stat.country" class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ getCountryName(stat.country) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ stat.count }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ({{ stat.percentage }}%)
                                        </span>
                                    </div>
                                </div>
                                <div v-if="countryStatsWithPercentage.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    No country data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referrer Stats -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Top Referrers</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Referrer
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Clicks
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Percentage
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="stat in referrerStatsWithPercentage" :key="stat.referrer">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <a v-if="stat.referrer && stat.referrer !== 'null'" :href="stat.referrer" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 break-all">
                                                {{ stat.referrer }}
                                            </a>
                                            <span v-else class="text-gray-500 dark:text-gray-400">Direct</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ stat.count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ stat.percentage }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="referrerStatsWithPercentage.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                No referrer data available
                            </div>
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
import { onMounted, onUnmounted, ref, computed, nextTick } from 'vue'

const props = defineProps({
    url: Object,
    analytics: Object,
    total_clicks: Number,
})

const dailyChart = ref(null)

// Computed properties for performance optimization
const chartData = computed(() => {
    if (!props.analytics?.daily_clicks) return []
    
    return Object.entries(props.analytics.daily_clicks)
        .map(([date, clicks]) => ({ date, clicks }))
        .sort((a, b) => new Date(a.date) - new Date(b.date))
})

const maxClicks = computed(() => {
    return Math.max(...chartData.value.map(d => d.clicks), 1)
})

const countryStatsWithPercentage = computed(() => {
    if (!props.analytics?.country_stats || props.total_clicks === 0) return []
    
    return Object.entries(props.analytics.country_stats).map(([country, count]) => ({
        country,
        count,
        percentage: Math.round((count / props.total_clicks) * 100)
    }))
})

const referrerStatsWithPercentage = computed(() => {
    if (!props.analytics?.referrer_stats || props.total_clicks === 0) return []
    
    return Object.entries(props.analytics.referrer_stats).map(([referrer, count]) => ({
        referrer,
        count,
        percentage: Math.round((count / props.total_clicks) * 100)
    }))
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

const getCountryName = (countryCode) => {
    if (!countryCode || countryCode === 'null') return 'Unknown'
    
    // Simple country code to name mapping (you could expand this)
    const countries = {
        'US': 'United States',
        'GB': 'United Kingdom',
        'CA': 'Canada',
        'AU': 'Australia',
        'DE': 'Germany',
        'FR': 'France',
        'JP': 'Japan',
        'CN': 'China',
        'IN': 'India',
        'BR': 'Brazil',
    }
    
    return countries[countryCode] || countryCode
}

const initChart = async () => {
    if (!dailyChart.value) return
    
    const canvas = dailyChart.value
    const ctx = canvas.getContext('2d')
    
    // Set canvas size with device pixel ratio for crisp rendering
    const dpr = window.devicePixelRatio || 1
    const rect = canvas.getBoundingClientRect()
    
    canvas.width = rect.width * dpr
    canvas.height = rect.height * dpr
    canvas.style.width = rect.width + 'px'
    canvas.style.height = rect.height + 'px'
    
    ctx.scale(dpr, dpr)
    
    // Use computed data for better performance
    const data = chartData.value
    const maxClicksValue = maxClicks.value
    
    // Chart dimensions
    const padding = 40
    const chartWidth = rect.width - (padding * 2)
    const chartHeight = rect.height - (padding * 2)
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    
    if (data.length === 0) {
        // Show "No data" message
        ctx.fillStyle = '#9CA3AF'
        ctx.font = '14px sans-serif'
        ctx.textAlign = 'center'
        ctx.fillText('No data available', rect.width / 2, rect.height / 2)
        return
    }
    
    // Draw grid lines
    ctx.strokeStyle = '#E5E7EB'
    ctx.lineWidth = 1
    
    // Horizontal grid lines
    for (let i = 0; i <= 5; i++) {
        const y = padding + (chartHeight / 5) * i
        ctx.beginPath()
        ctx.moveTo(padding, y)
        ctx.lineTo(rect.width - padding, y)
        ctx.stroke()
    }
    
    // Draw line chart
    if (data.length > 1) {
        ctx.strokeStyle = '#3B82F6'
        ctx.lineWidth = 2
        ctx.beginPath()
        
        data.forEach((point, index) => {
            const x = padding + (chartWidth / (data.length - 1)) * index
            const y = rect.height - padding - (point.clicks / maxClicksValue) * chartHeight
            
            if (index === 0) {
                ctx.moveTo(x, y)
            } else {
                ctx.lineTo(x, y)
            }
        })
        
        ctx.stroke()
        
        // Draw points
        ctx.fillStyle = '#3B82F6'
        data.forEach((point, index) => {
            const x = padding + (chartWidth / (data.length - 1)) * index
            const y = rect.height - padding - (point.clicks / maxClicksValue) * chartHeight
            
            ctx.beginPath()
            ctx.arc(x, y, 3, 0, 2 * Math.PI)
            ctx.fill()
        })
    }
    
    // Draw labels
    ctx.fillStyle = '#6B7280'
    ctx.font = '12px sans-serif'
    ctx.textAlign = 'center'
    
    // X-axis labels
    const labelStep = Math.max(1, Math.floor(data.length / 7))
    data.forEach((point, index) => {
        if (index % labelStep === 0) {
            const x = padding + (chartWidth / (data.length - 1)) * index
            const date = new Date(point.date)
            const label = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
            ctx.fillText(label, x, rect.height - 10)
        }
    })
    
    // Y-axis labels
    ctx.textAlign = 'right'
    for (let i = 0; i <= 5; i++) {
        const y = padding + (chartHeight / 5) * i
        const value = Math.round(maxClicksValue * (1 - i / 5))
        ctx.fillText(value.toString(), padding - 10, y + 4)
    }
}

// Debounced resize handler for better performance
let resizeTimeout
const handleResize = () => {
    clearTimeout(resizeTimeout)
    resizeTimeout = setTimeout(() => {
        initChart()
    }, 150)
}

onMounted(() => {
    nextTick(() => {
        initChart()
        window.addEventListener('resize', handleResize)
    })
})
 
 // Cleanup on unmount
 onUnmounted(() => {
     window.removeEventListener('resize', handleResize)
     clearTimeout(resizeTimeout)
 })
</script>

<style scoped>
canvas {
    width: 100%;
    height: 100%;
}
</style>