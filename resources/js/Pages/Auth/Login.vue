<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});


const submit = () => {
    // Validate form before submission
    if (!form.email || !form.password) {
        return;
    }
    
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        }
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <Message v-if="status" severity="success" class="mb-4">
            {{ status }}
        </Message>

        <form @submit.prevent="submit">
            <div class="mt-6 space-y-2">
                <FloatLabel>
                    <InputText
                        id="email"
                        type="email"
                        class="w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <label for="email">Email</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.email" severity="error" class="block">
                    {{ form.errors.email }}
                </InlineMessage>
            </div>

            <div class="mt-6 space-y-2">
                <FloatLabel>
                    <InputText
                        id="password"
                        type="password"
                        class="w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        toggleMas
                    />
                    <label for="password">Password</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.password" severity="error" class="block">
                    {{ form.errors.password }}
                </InlineMessage>
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox v-model="form.remember" :binary="true" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 dark:text-gray-400 underline hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <Button
                    class="ms-4"
                    :disabled="form.processing"
                    :loading="form.processing"
                    type="submit"
                    @click="handleButtonClick"
                >
                    Log in
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
