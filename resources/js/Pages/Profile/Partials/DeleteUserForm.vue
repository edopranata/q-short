<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <Button 
            severity="danger" 
            @click="confirmUserDeletion"
            label="Delete Account"
            icon="pi pi-trash"
        />

        <Dialog 
            v-model:visible="confirmingUserDeletion" 
            @hide="closeModal" 
            header="Delete Account"
            :modal="true"
            :closable="true"
            class="w-full max-w-lg mx-4"
        >
            <template #default>
                <div class="space-y-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Once your account is deleted, all of its resources and data
                        will be permanently deleted. Please enter your password to
                        confirm you would like to permanently delete your account.
                    </p>

                    <div class="space-y-2">
                        <FloatLabel>
                            <Password
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                :feedback="false"
                                toggleMask

                                @keyup.enter="deleteUser"
                            />
                            <label for="password">Password</label>
                        </FloatLabel>

                        <InlineMessage v-if="form.errors.password" severity="error" class="block">
                            {{ form.errors.password }}
                        </InlineMessage>
                    </div>
                </div>
            </template>
            
            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button 
                        severity="secondary" 
                        @click="closeModal"
                        label="Cancel"
                    />

                    <Button
                        severity="danger"
                        :disabled="form.processing"
                        :loading="form.processing"
                        @click="deleteUser"
                        label="Delete Account"
                        icon="pi pi-trash"
                    />
                </div>
            </template>
        </Dialog>
    </section>
</template>
