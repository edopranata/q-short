<script setup>
import { ref, computed } from 'vue';
import ThemeToggle from '@/Components/UI/ThemeToggle.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import Menubar from 'primevue/menubar';
import Badge from 'primevue/badge';
import Avatar from 'primevue/avatar';
import Menu from 'primevue/menu';

defineProps({
    title: {
        type: String,
        default: ''
    }
});

const user = computed(() => usePage().props.auth.user);

// User initials for avatar
const userInitials = computed(() => {
    return user.value.name.split(' ').map(n => n[0]).join('').toUpperCase();
});

// User menu items
const userMenuItems = ref([
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => {
            router.visit(route('profile.edit'));
        }
    },
    {
        separator: true
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: () => {
            router.post(route('logout'));
        }
    }
]);

const userMenuOpen = ref(false);

// Menu items for MenuBar
const items = ref([
    {
        label: 'Dashboard',
        template: () => {
            return `<div class="flex items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 13H11V3H3V13ZM3 21H11V15H3V21ZM13 21H21V11H13V21ZM13 3V9H21V3H13Z" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
                <span>Dashboard</span>
            </div>`;
        },
        route: route('dashboard')
    },
    {
        label: 'URLs',
        template: () => {
            return `<div class="flex items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 13C10.4295 13.5741 10.9774 14.0281 11.6066 14.3284C12.2357 14.6287 12.9315 14.7679 13.6344 14.7344C14.3373 14.701 15.0203 14.4957 15.6134 14.1387C16.2065 13.7818 16.6887 13.2848 17.01 12.69L19.07 9.31C19.6441 8.32556 19.8648 7.16353 19.6926 6.02859C19.5204 4.89365 18.9668 3.85482 18.1317 3.10446C17.2966 2.3541 16.2378 1.93617 15.1402 1.92229C14.0426 1.90841 12.9739 2.29934 12.12 3.03L10.91 4.24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 11C13.5705 10.4259 13.0226 9.97186 12.3934 9.67157C11.7643 9.37128 11.0685 9.23206 10.3656 9.26558C9.66272 9.2991 8.97965 9.50437 8.38659 9.86126C7.79354 10.2182 7.31132 10.7152 6.99 11.31L4.93 14.69C4.35589 15.6744 4.13517 16.8365 4.30736 17.9714C4.47955 19.1064 5.03315 20.1452 5.86825 20.8955C6.70335 21.6459 7.76221 22.0638 8.85977 22.0777C9.95734 22.0916 11.0261 21.7007 11.88 20.97L13.09 19.76" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>URLs</span>
            </div>`;
        },
        items: [
            {
                    label: 'All URLs',
                    template: () => {
                        return `<div class="flex items-center gap-2">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 6H3.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 12H3.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 18H3.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>All URLs</span>
                        </div>`;
                    },
                route: route('urls.index')
            },
            {
                    label: 'Create URL',
                    template: () => {
                        return `<div class="flex items-center gap-2">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M8 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span>Create URL</span>
                        </div>`;
                    },
                route: route('urls.create')
            }
        ]
    },
    {
        label: 'Analytics',
        template: () => {
            return `<div class="flex items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 20V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 20V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Analytics</span>
            </div>`;
        },
        route: route('analytics.index')
    }
]);

const userMenu = ref();

const toggleUserMenu = (event) => {
    userMenuOpen.value = !userMenuOpen.value;
    if (userMenu.value) {
        userMenu.value.toggle(event);
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- MenuBar Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <Menubar :model="items" class="border-none bg-transparent px-4 py-3">
                <template #start>
                    <div class="flex items-center">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3">
                            <!-- Modern Link/Chain Icon Outline -->
                            <path d="M9 12L15 12" stroke="var(--p-primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11 6C13.7614 6 16 8.23858 16 11V13C16 15.7614 13.7614 18 11 18H9C6.23858 18 4 15.7614 4 13V11C4 8.23858 6.23858 6 9 6Z" stroke="var(--p-primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13 6C15.7614 6 18 8.23858 18 11V13C18 15.7614 15.7614 18 13 18H15C17.7614 18 20 15.7614 20 13V11C20 8.23858 17.7614 6 15 6Z" stroke="var(--p-primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Q-Shorten</span>
                    </div>
                </template>
                <template #item="{ item, props, hasSubmenu, root }">
                    <Link v-if="item.route" :href="item.route" v-ripple class="flex items-center p-menuitem-link px-3 py-2 rounded-md transition-colors duration-200">
                        <span class="font-medium">{{ item.label }}</span>
                        <Badge v-if="item.badge" :class="{ 'ml-auto': !root, 'ml-3': root }" :value="item.badge" />
                        <span v-if="item.shortcut" class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs px-2 py-1">{{ item.shortcut }}</span>
                        <i v-if="hasSubmenu" :class="['pi ml-2', { 'pi-angle-down': root, 'pi-angle-right': !root }]"></i>
                    </Link>
                    <a v-else v-ripple class="flex items-center p-menuitem-link px-3 py-2 rounded-md transition-colors duration-200" v-bind="props.action">
                        <span class="font-medium">{{ item.label }}</span>
                        <Badge v-if="item.badge" :class="{ 'ml-auto': !root, 'ml-3': root }" :value="item.badge" />
                        <span v-if="item.shortcut" class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs px-2 py-1">{{ item.shortcut }}</span>
                        <i v-if="hasSubmenu" :class="['pi ml-2', { 'pi-angle-down': root, 'pi-angle-right': !root }]"></i>
                    </a>
                </template>
                <template #end>
                    <div class="flex items-center gap-3">
                        <ThemeToggle class="p-2" />
                        <div class="flex items-center gap-2 pl-2 border-l border-gray-200 dark:border-gray-600">
                             <Avatar :label="userInitials" shape="circle" class="w-8 h-8 cursor-pointer" @click="toggleUserMenu" />
                             <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:block">{{ user.name }}</span>
                         </div>
                    </div>
                </template>
            </Menubar>
            <Menu ref="userMenu" :model="userMenuItems" :popup="true" />
        </div>

        <!-- Page Heading -->
        <div v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </div>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
            <slot />
        </main>
    </div>
</template>
