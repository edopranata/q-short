import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export const useUrlStore = defineStore('url', () => {
    const urls = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const stats = ref({
        total_urls: 0,
        total_clicks: 0,
        today_clicks: 0,
        active_urls: 0
    });

    // Computed properties
    const totalUrls = computed(() => stats.value.total_urls);
    const totalClicks = computed(() => stats.value.total_clicks);
    const todayClicks = computed(() => stats.value.today_clicks);
    const activeUrls = computed(() => stats.value.active_urls);
    const averageClicks = computed(() => {
        const total = stats.value.total_urls;
        const clicks = stats.value.total_clicks;
        return total > 0 ? Math.round(clicks / total) : 0;
    });
    const recentUrls = computed(() => urls.value.slice(0, 5));

    // Actions
    const fetchUrls = async () => {
        loading.value = true;
        error.value = null;
        
        try {
            // Use Inertia to visit the URLs page to get data
            router.get('/urls', {}, {
                preserveState: true,
                preserveScroll: true,
                only: ['urls', 'stats'],
                onSuccess: (page) => {
                    if (page.props.urls) {
                        urls.value = page.props.urls.data || page.props.urls || [];
                    }
                    if (page.props.stats) {
                        stats.value = page.props.stats;
                    }
                },
                onError: (errors) => {
                    error.value = 'Failed to fetch URLs';
                    console.error('Error fetching URLs:', errors);
                }
            });
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching URLs:', err);
        } finally {
            loading.value = false;
        }
    };

    const createUrl = async (urlData) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch('/api/urls', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(urlData)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to create URL');
            }
            
            const newUrl = await response.json();
            urls.value.unshift(newUrl);
            stats.value.total_urls += 1;
            stats.value.active_urls += 1;
            
            return newUrl;
        } catch (err) {
            error.value = err.message;
            console.error('Error creating URL:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateUrl = async (id, urlData) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch(`/api/urls/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(urlData)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to update URL');
            }
            
            const updatedUrl = await response.json();
            const index = urls.value.findIndex(url => url.id === id);
            if (index !== -1) {
                urls.value[index] = updatedUrl;
            }
            
            return updatedUrl;
        } catch (err) {
            error.value = err.message;
            console.error('Error updating URL:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const deleteUrl = async (id) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch(`/api/urls/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to delete URL');
            }
            
            urls.value = urls.value.filter(url => url.id !== id);
            stats.value.total_urls -= 1;
            stats.value.active_urls -= 1;
            
        } catch (err) {
            error.value = err.message;
            console.error('Error deleting URL:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const toggleUrlStatus = async (id) => {
        const url = urls.value.find(u => u.id === id);
        if (!url) return;
        
        try {
            await updateUrl(id, { is_active: !url.is_active });
            
            if (url.is_active) {
                stats.value.active_urls -= 1;
            } else {
                stats.value.active_urls += 1;
            }
        } catch (err) {
            console.error('Error toggling URL status:', err);
        }
    };

    const clearError = () => {
        error.value = null;
    };

    const refreshStats = async () => {
        try {
            const response = await fetch('/api/stats');
            if (!response.ok) throw new Error('Failed to fetch stats');
            
            const data = await response.json();
            stats.value = data;
        } catch (err) {
            console.error('Error refreshing stats:', err);
        }
    };

    const setUrls = (urlsData) => {
        if (urlsData) {
            urls.value = urlsData.data || urlsData || [];
            if (urlsData.stats) {
                stats.value = urlsData.stats;
            }
        }
    };

    const fetchUrlsForDashboard = async () => {
        loading.value = true;
        error.value = null;
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('/api/urls-dashboard', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }
            
            const data = await response.json();
            urls.value = data.urls || [];
            if (data.stats) {
                stats.value = data.stats;
            }
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching URLs for dashboard:', err);
        } finally {
            loading.value = false;
        }
    };

    return {
        // State
        urls,
        loading,
        error,
        stats,
        
        // Computed
        totalUrls,
        totalClicks,
        todayClicks,
        activeUrls,
        averageClicks,
        recentUrls,
        
        // Actions
        fetchUrls,
        fetchUrlsForDashboard,
        createUrl,
        updateUrl,
        deleteUrl,
        toggleUrlStatus,
        clearError,
        refreshStats,
        setUrls
    };
});