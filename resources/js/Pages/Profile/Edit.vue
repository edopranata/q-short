<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout title="Profile">
        <div class="py-8 sm:py-12">
            <div class="container mx-auto max-w-4xl space-y-8 px-4 sm:px-6 lg:px-8">
                <Card class="shadow-sm border-0">
                    <template #title>
                        <div class="flex items-center gap-3">
                            <i class="pi pi-user text-primary"></i>
                            <span>Profile Information</span>
                        </div>
                    </template>
                    <template #content>
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-2xl"
                        />
                    </template>
                </Card>

                <Card class="shadow-sm border-0">
                    <template #title>
                        <div class="flex items-center gap-3">
                            <i class="pi pi-lock text-primary"></i>
                            <span>Update Password</span>
                        </div>
                    </template>
                    <template #content>
                        <UpdatePasswordForm class="max-w-2xl" />
                    </template>
                </Card>

                <Card class="shadow-sm border-0">
                    <template #title>
                        <div class="flex items-center gap-3">
                            <i class="pi pi-trash text-red-500"></i>
                            <span>Delete Account</span>
                        </div>
                    </template>
                    <template #content>
                        <DeleteUserForm class="max-w-2xl" />
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
