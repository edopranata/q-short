<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Update Password
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-8 space-y-8">
            <div class="space-y-2">
                <FloatLabel>
                    <Password
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        autocomplete="current-password"
                        :feedback="false"
                        toggleMask

                    />
                    <label for="current_password">Current Password</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.current_password" severity="error" class="block">
                    {{ form.errors.current_password }}
                </InlineMessage>
            </div>

            <div class="space-y-2">
                <FloatLabel>
                    <Password
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        autocomplete="new-password"
                        :feedback="true"
                        toggleMask

                    />
                    <label for="password">New Password</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.password" severity="error" class="block">
                    {{ form.errors.password }}
                </InlineMessage>
            </div>

            <div class="space-y-2">
                <FloatLabel>
                    <Password
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        autocomplete="new-password"
                        :feedback="false"
                        toggleMask

                    />
                    <label for="password_confirmation">Confirm Password</label>
                </FloatLabel>

                <InlineMessage v-if="form.errors.password_confirmation" severity="error" class="block">
                    {{ form.errors.password_confirmation }}
                </InlineMessage>
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
