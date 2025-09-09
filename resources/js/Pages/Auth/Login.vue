<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';
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

const handleButtonClick = (event) => {
    console.log('Login button clicked - form will submit');
    // Button click is working, form submission will be handled by @submit.prevent
};

const submit = () => {
    // Validate form before submission
    if (!form.email || !form.password) {
        console.warn('Please fill in all required fields');
        return;
    }
    
    console.log('Submitting login form...');
    
    form.post(route('login'), {
        onStart: () => {
            console.log('Login request started');
        },
        onFinish: () => {
            console.log('Login request finished');
            form.reset('password');
        },
        onSuccess: (page) => {
            console.log('Login successful, redirecting...');
        },
        onError: (errors) => {
            console.log('Login failed:', errors);
        }
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <Alert v-if="status" type="success" class="mb-4">
            {{ status }}
        </Alert>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <Input
                    id="email"
                    type="email"
                    class="mt-1"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <Input
                    id="password"
                    type="password"
                    class="mt-1"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
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
