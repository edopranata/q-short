<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-8 space-y-8"
        >
            <div class="space-y-2">
                <FloatLabel>
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="w-full"
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

            <div class="space-y-2">
                <FloatLabel>
                    <InputText
                        id="email"
                        type="email"
                        v-model="form.email"
                        class="w-full"
                        required
                        autocomplete="username"
                    />
                    <label for="email">Email</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.email" severity="error" class="block">
                    {{ form.errors.email }}
                </InlineMessage>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <Message severity="warn" class="mt-4">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="ml-1 text-orange-700 dark:text-orange-300 underline hover:text-orange-800 dark:hover:text-orange-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </Message>

                <Message
                    v-show="status === 'verification-link-sent'"
                    severity="success"
                    class="mt-4"
                >
                    A new verification link has been sent to your email address.
                </Message>
            </div>

            <div class="flex items-center gap-4">
                <Button 
                    type="submit"
                    :disabled="form.processing" 
                    :loading="form.processing"
                    label="Save"
                    icon="pi pi-check"
                />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
