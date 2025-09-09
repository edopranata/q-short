import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAnalyticsStore = defineStore('analytics', () => {
    const clickData = ref([]);
    const geographicData = ref([]);
    const deviceData = ref([]);
    const referrerData = ref([]);
    const timeSeriesData = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const dateRange = ref({
        start: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000), // 30 days ago
        end: new Date()
    });

    // Computed properties
    const totalClicks = computed(() => {
        return clickData.value.reduce((sum, item) => sum + (item.clicks || 0), 0);
    });

    const topCountries = computed(() => {
        return geographicData.value
            .sort((a, b) => b.clicks - a.clicks)
            .slice(0, 10);
    });

    const topDevices = computed(() => {
        return deviceData.value
            .sort((a, b) => b.clicks - a.clicks)
            .slice(0, 5);
    });

    const topReferrers = computed(() => {
        return referrerData.value
            .filter(item => item.referrer !== 'Direct')
            .sort((a, b) => b.clicks - a.clicks)
            .slice(0, 10);
    });

    const clicksToday = computed(() => {
        const today = new Date().toISOString().split('T')[0];
        const todayData = timeSeriesData.value.find(item => 
            item.date === today
        );
        return todayData?.clicks || 0;
    });

    const clicksThisWeek = computed(() => {
        const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
        return timeSeriesData.value
            .filter(item => new Date(item.date) >= weekAgo)
            .reduce((sum, item) => sum + (item.clicks || 0), 0);
    });

    const clicksThisMonth = computed(() => {
        const monthAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000);
        return timeSeriesData.value
            .filter(item => new Date(item.date) >= monthAgo)
            .reduce((sum, item) => sum + (item.clicks || 0), 0);
    });

    const chartData = computed(() => {
        return {
            labels: timeSeriesData.value.map(item => item.date),
            datasets: [{
                label: 'Clicks',
                data: timeSeriesData.value.map(item => item.clicks),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        };
    });

    // Actions
    const fetchAnalytics = async (urlId = null, customDateRange = null) => {
        loading.value = true;
        error.value = null;
        
        try {
            const range = customDateRange || dateRange.value;
            const params = new URLSearchParams({
                start_date: range.start.toISOString().split('T')[0],
                end_date: range.end.toISOString().split('T')[0]
            });
            
            if (urlId) {
                params.append('url_id', urlId);
            }
            
            const response = await fetch(`/api/analytics?${params}`);
            if (!response.ok) throw new Error('Failed to fetch analytics');
            
            const data = await response.json();
            
            clickData.value = data.clicks || [];
            geographicData.value = data.geographic || [];
            deviceData.value = data.devices || [];
            referrerData.value = data.referrers || [];
            timeSeriesData.value = data.timeSeries || [];
            
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching analytics:', err);
        } finally {
            loading.value = false;
        }
    };

    const fetchUrlAnalytics = async (urlId) => {
        loading.value = true;
        error.value = null;
        
        try {
            const range = dateRange.value;
            const params = new URLSearchParams({
                start_date: range.start.toISOString().split('T')[0],
                end_date: range.end.toISOString().split('T')[0]
            });
            
            const response = await fetch(`/api/urls/${urlId}/analytics?${params}`);
            if (!response.ok) throw new Error('Failed to fetch URL analytics');
            
            const data = await response.json();
            
            return {
                clicks: data.clicks || [],
                geographic: data.geographic || [],
                devices: data.devices || [],
                referrers: data.referrers || [],
                timeSeries: data.timeSeries || []
            };
            
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching URL analytics:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const setDateRange = (start, end) => {
        dateRange.value = { start, end };
    };

    const setPresetDateRange = (preset) => {
        const now = new Date();
        let start;
        
        switch (preset) {
            case 'today':
                start = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                break;
            case 'yesterday':
                start = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
                dateRange.value.end = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
                break;
            case 'last7days':
                start = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                break;
            case 'last30days':
                start = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                break;
            case 'last90days':
                start = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000);
                break;
            default:
                start = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
        }
        
        dateRange.value.start = start;
        if (preset !== 'yesterday') {
            dateRange.value.end = now;
        }
    };

    const exportData = async (format = 'csv') => {
        loading.value = true;
        error.value = null;
        
        try {
            const range = dateRange.value;
            const params = new URLSearchParams({
                start_date: range.start.toISOString().split('T')[0],
                end_date: range.end.toISOString().split('T')[0],
                format
            });
            
            const response = await fetch(`/api/analytics/export?${params}`);
            if (!response.ok) throw new Error('Failed to export data');
            
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `analytics-${range.start.toISOString().split('T')[0]}-to-${range.end.toISOString().split('T')[0]}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
        } catch (err) {
            error.value = err.message;
            console.error('Error exporting data:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const clearError = () => {
        error.value = null;
    };

    const refreshData = async () => {
        await fetchAnalytics();
    };

    return {
        // State
        clickData,
        geographicData,
        deviceData,
        referrerData,
        timeSeriesData,
        loading,
        error,
        dateRange,
        
        // Computed
        totalClicks,
        topCountries,
        topDevices,
        topReferrers,
        clicksToday,
        clicksThisWeek,
        clicksThisMonth,
        chartData,
        
        // Actions
        fetchAnalytics,
        fetchUrlAnalytics,
        setDateRange,
        setPresetDateRange,
        exportData,
        clearError,
        refreshData
    };
});