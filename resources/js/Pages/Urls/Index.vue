<template>
    <Head title="My URLs" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    My URLs
                </h2>
                <Link :href="route('urls.create')">
                    <Button 
                        label="Create Short URL" 
                        icon="pi pi-plus" 
                        class="p-button-primary"
                    />
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <Message v-if="$page.props.flash?.success" severity="success" class="mb-6">
                    {{ $page.props.flash?.success }}
                </Message>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <Card v-for="stat in statsCards" :key="stat.title" class="shadow-md">
                        <template #title>
                            {{ stat.title }}
                        </template>
                        <template #content>
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i :class="[stat.icon, 'text-' + stat.color + '-500', 'text-3xl']"></i>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stat.value }}</div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- URLs Table -->
                <Card class="mt-6">
                    <template #title>
                        My URLs
                    </template>
                    <template #content>
                        <DataTable 
                            :value="urls.data" 
                            :paginator="true" 
                            :rows="10"
                            :totalRecords="urls.total"
                            :lazy="true"
                            @page="onPageChange"
                            stripedRows
                            responsiveLayout="scroll"
                            class="p-datatable-sm"
                        >
                            <Column field="original_url" header="URL" style="min-width: 200px">
                                <template #body="{ data }">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ data.title || 'Untitled' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                            {{ data.original_url }}
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            
                            <Column field="short_url" header="Short Code" style="min-width: 120px">
                                <template #body="{ data }">
                                    <div class="flex items-center gap-2">
                                        <code class="text-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-200 px-2 py-1 rounded">
                                            {{ data.short_url }}
                                        </code>
                                        <Button 
                                            icon="pi pi-copy" 
                                            size="small" 
                                            text
                                            @click="copyToClipboard(data.short_url)"
                                            v-tooltip="'Copy to clipboard'"
                                        />
                                    </div>
                                </template>
                            </Column>
                            
                            <Column field="click_count" header="Clicks" style="min-width: 80px">
                                <template #body="{ data }">
                                    <Badge :value="data.click_count" severity="info" />
                                </template>
                            </Column>
                            
                            <Column field="is_active" header="Status" style="min-width: 100px">
                                <template #body="{ data }">
                                    <Tag 
                                        :value="data.is_active ? 'Active' : 'Inactive'" 
                                        :severity="data.is_active ? 'success' : 'danger'"
                                    />
                                </template>
                            </Column>
                            
                            <Column field="created_at" header="Created" style="min-width: 120px">
                                <template #body="{ data }">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDate(data.created_at) }}
                                    </span>
                                </template>
                            </Column>
                            
                            <Column header="Actions" style="min-width: 200px">
                                <template #body="{ data }">
                                    <div class="flex gap-2">
                                        <Button 
                                            icon="pi pi-chart-bar" 
                                            size="small" 
                                            severity="info" 
                                            @click="router.visit(route('analytics.show', { shortenedUrl: data.id }))"
                                            v-tooltip="'View Analytics'"
                                        />
                                        <Button 
                                            icon="pi pi-pencil" 
                                            size="small" 
                                            severity="warning" 
                                            @click="router.visit(route('urls.edit', data.id))"
                                            v-tooltip="'Edit URL'"
                                        />
                                        <Button 
                                            icon="pi pi-trash" 
                                            size="small" 
                                            severity="danger" 
                                            @click="deleteUrl(data)"
                                            v-tooltip="'Delete URL'"
                                        />
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed, onMounted, ref } from 'vue'
import { useUrlStore } from '@/stores/url'
import { useToast } from 'primevue/usetoast'
import { useConfirm } from 'primevue/useconfirm'
import Tag from 'primevue/tag'
import Badge from 'primevue/badge'
import Button from 'primevue/button'
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Message from 'primevue/message'

const props = defineProps({
    urls: Object,
})

const urlStore = useUrlStore()
const toast = useToast()
const confirm = useConfirm()

// Initialize store with props data
urlStore.setUrls(props.urls)

const statsCards = computed(() => [
    {
        title: 'Total URLs',
        value: urlStore.totalUrls,
        icon: 'pi pi-link',
        color: 'blue'
    },
    {
        title: 'Total Clicks',
        value: urlStore.totalClicks,
        icon: 'pi pi-chart-line',
        color: 'green'
    },
    {
        title: 'Active URLs',
        value: urlStore.activeUrls,
        icon: 'pi pi-check-circle',
        color: 'yellow'
    },
    {
        title: 'Avg. Clicks',
        value: urlStore.averageClicks,
        icon: 'pi pi-chart-bar',
        color: 'purple'
    }
])

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        toast.add({
            severity: 'success',
            summary: 'Copied!',
            detail: 'URL copied to clipboard',
            life: 3000
        })
    } catch (err) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to copy URL',
            life: 3000
        })
    }
}

const deleteUrl = (url) => {
    confirm.require({
        message: `Are you sure you want to delete URL "${url.title || url.original_url}"? This action cannot be undone.`,
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('urls.destroy', url.id), {
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'URL deleted successfully',
                        life: 3000
                    })
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Failed to delete URL',
                        life: 3000
                    })
                }
            })
        },
        reject: () => {
            // Optional: Show cancelled message
            toast.add({
                severity: 'info',
                summary: 'Cancelled',
                detail: 'URL deletion cancelled',
                life: 2000
            })
        }
    })
}

const onPageChange = (event) => {
    router.get(route('urls.index'), {
        page: event.page + 1
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const toggleStatus = async (url) => {
    try {
        await urlStore.toggleStatus(url.id)
        toast.add({
            severity: 'success',
            summary: 'Updated',
            detail: `URL ${url.is_active ? 'deactivated' : 'activated'} successfully`,
            life: 3000
        })
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to update URL status',
            life: 3000
        })
    }
}

onMounted(() => {
    if (props.urls?.data) {
        urlStore.setUrls(props.urls)
    }
})
</script>