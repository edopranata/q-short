<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div class="space-y-2">
                <FloatLabel>
                    <InputText
                        id="name"
                        type="text"
                        class="w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <label for="name">Name</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.name" severity="error" class="block">
                    {{ form.errors.name }}
                </InlineMessage>
            </div>

            <div class="mt-4 space-y-2">
                <FloatLabel>
                    <InputText
                        id="email"
                        type="email"
                        class="w-full"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />
                    <label for="email">Email</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.email" severity="error" class="block">
                    {{ form.errors.email }}
                </InlineMessage>
            </div>

            <div class="mt-4 space-y-2">
                <FloatLabel>
                    <InputText
                        id="password"
                        type="password"
                        class="w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <label for="password">Password</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.password" severity="error" class="block">
                    {{ form.errors.password }}
                </InlineMessage>
            </div>

            <div class="mt-4 space-y-2">
                <FloatLabel>
                    <InputText
                        id="password_confirmation"
                        type="password"
                        class="w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <label for="password_confirmation">Confirm Password</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.password_confirmation" severity="error" class="block">
                    {{ form.errors.password_confirmation }}
                </InlineMessage>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 dark:text-gray-400 underline hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <Button
                    class="ms-4"
                    :disabled="form.processing"
                    :loading="form.processing"
                >
                    Register
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
