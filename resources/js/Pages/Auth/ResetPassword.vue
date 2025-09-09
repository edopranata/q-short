<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <form @submit.prevent="submit">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>

                <InputText
                    id="email"
                    type="email"
                    class="mt-1 w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InlineMessage v-if="form.errors.email" severity="error" class="mt-2">
                    {{ form.errors.email }}
                </InlineMessage>
            </div>

            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>

                <InputText
                    id="password"
                    type="password"
                    class="mt-1 w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InlineMessage v-if="form.errors.password" severity="error" class="mt-2">
                    {{ form.errors.password }}
                </InlineMessage>
            </div>

            <div class="mt-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>

                <InputText
                    id="password_confirmation"
                    type="password"
                    class="mt-1 w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InlineMessage v-if="form.errors.password_confirmation" severity="error" class="mt-2">
                    {{ form.errors.password_confirmation }}
                </InlineMessage>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Button
                    :disabled="form.processing"
                    :loading="form.processing"
                >
                    Reset Password
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
