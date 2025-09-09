<script setup>
import { onMounted, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useUrlStore } from '@/stores/url';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Chip from 'primevue/chip';
import Badge from 'primevue/badge';
import Toast from 'primevue/toast';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    }
});

const urlStore = useUrlStore();
const toast = useToast();

// Initialize stats from props
if (props.stats) {
    urlStore.stats = props.stats;
}

const statsCards = computed(() => [
    {
        title: 'Total URLs',
        value: urlStore.totalUrls,
        icon: 'pi pi-link',
        color: 'text-blue-600',
        bgColor: 'bg-blue-50'
    },
    {
        title: 'Total Clicks',
        value: urlStore.totalClicks,
        icon: 'pi pi-chart-line',
        color: 'text-green-600',
        bgColor: 'bg-green-50'
    },
    {
        title: 'Today\'s Clicks',
        value: urlStore.todayClicks,
        icon: 'pi pi-calendar',
        color: 'text-purple-600',
        bgColor: 'bg-purple-50'
    },
    {
        title: 'Active URLs',
        value: urlStore.activeUrls,
        icon: 'pi pi-check-circle',
        color: 'text-emerald-600',
        bgColor: 'bg-emerald-50'
    }
]);

const quickActions = [
    {
        label: 'Create New URL',
        icon: 'pi pi-plus',
        severity: 'primary',
        action: () => router.visit('/urls/create')
    },
    {
        label: 'View Analytics',
        icon: 'pi pi-chart-bar',
        severity: 'secondary',
        action: () => router.visit('/analytics')
    },
    {
        label: 'Manage URLs',
        icon: 'pi pi-list',
        severity: 'info',
        action: () => router.visit('/urls')
    }
];

const refreshStats = async () => {
    try {
        await urlStore.refreshStats();
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Statistics refreshed successfully',
            life: 3000
        });
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to refresh statistics',
            life: 3000
        });
    }
};

onMounted(() => {
    // Dashboard doesn't need to fetch URLs automatically
    // Users can navigate to URLs page manually
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Dashboard
                </h2>
                <Button 
                    icon="pi pi-refresh" 
                    :loading="urlStore.loading"
                    @click="refreshStats"
                    severity="secondary"
                    size="small"
                    label="Refresh"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <Card class="mb-6">
                    <template #title>
                        Welcome back!
                    </template>
                    <template #content>
                        <p class="text-gray-600 dark:text-gray-400">Here's an overview of your URL shortening activity.</p>
                    </template>
                </Card>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-6 gap-6 mb-8">
                    <Card v-for="stat in statsCards" :key="stat.title" class="hover:shadow-lg transition-shadow">
                        <template #title>
                            {{ stat.title }}
                        </template>
                        <template #content>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ (stat.value || 0).toLocaleString() }}</p>
                                </div>
                                <div :class="[stat.bgColor, 'p-3 rounded-full']">
                                    <i :class="[stat.icon, stat.color, 'text-xl']"></i>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Quick Actions -->
                <Card>
                    <template #title>
                        Quick Actions
                    </template>
                    <template #content>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4">
                            <Button 
                                v-for="action in quickActions" 
                                :key="action.label"
                                :label="action.label"
                                :icon="action.icon"
                                :severity="action.severity"
                                @click="action.action"
                                class="w-full"
                            />
                        </div>
                    </template>
                </Card>

                <!-- Recent URLs Section -->
                <Card v-if="urlStore.recentUrls.length > 0" class="mt-6">
                    <template #title>
                        Recent URLs
                    </template>
                    <template #content>
                        <DataTable 
                            :value="urlStore.recentUrls" 
                            :rows="5"
                            :loading="urlStore.loading"
                            responsiveLayout="scroll"
                        >
                            <Column field="short_code" header="Short Code">
                                <template #body="{ data }">
                                    <Chip :label="data.short_code" class="bg-blue-100 text-blue-800" />
                                </template>
                            </Column>
                            <Column field="original_url" header="Original URL">
                                <template #body="{ data }">
                                    <span class="truncate max-w-xs block" :title="data.original_url">
                                        {{ data.original_url }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="clicks" header="Clicks">
                                <template #body="{ data }">
                                    <Badge :value="data.clicks || 0" severity="info" />
                                </template>
                            </Column>
                            <Column field="created_at" header="Created">
                                <template #body="{ data }">
                                    <span class="text-sm text-gray-600">
                                        {{ new Date(data.created_at).toLocaleDateString() }}
                                    </span>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>
            </div>
        </div>

        <!-- Toast for notifications -->
        <Toast />
    </AuthenticatedLayout>
</template>
