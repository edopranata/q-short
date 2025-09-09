<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
        </div>

        <Message
            v-if="status"
            severity="success"
            class="mb-4"
        >
            {{ status }}
        </Message>

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

            <div class="mt-4 flex items-center justify-end">
                <Button
                    :disabled="form.processing"
                    :loading="form.processing"
                >
                    Email Password Reset Link
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
