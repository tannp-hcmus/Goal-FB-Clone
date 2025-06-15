<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FieldInput from '@/Components/FieldInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps<{
    status?: string;
}>();

const showPassword = ref(false);
const isLoading = ref(false);
const failedAttempts = ref(0);
const lastAttemptTime = ref<number | null>(null);
const generalError = ref<string | null>(null);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const canSubmit = computed(() => {
    if (failedAttempts.value >= 5) {
        const now = Date.now();
        if (lastAttemptTime.value && now - lastAttemptTime.value < 300000) {
            return false;
        }
        failedAttempts.value = 0;
    }
    return true;
});

const submit = () => {
    if (!canSubmit.value) {
        generalError.value = 'Too many failed attempts. Please try again in 5 minutes.';
        return;
    }

    isLoading.value = true;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            isLoading.value = false;
            generalError.value = null;
        },
        onError: () => {
            failedAttempts.value++;
            lastAttemptTime.value = Date.now();
            isLoading.value = false;
        }
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />

                <FieldInput
                    id="email"
                    name="email"
                    type="email"
                    rules="required|email"
                    placeholder="Email"
                    autofocus
                    autocomplete="username"
                    v-model="form.email"
                    :class="{ 'border-red-500': form.errors.email }"
                    aria-label="Email address"
                    :aria-invalid="!!form.errors.email"
                    aria-describedby="email-error"
                />
                <p v-if="form.errors.email" id="email-error" class="mt-1 text-sm text-red-600">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <div class="relative">
                    <FieldInput
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        rules="required|min:8"
                        placeholder="Password"
                        v-model="form.password"
                        :class="{ 'border-red-500': form.errors.password }"
                        aria-label="Password"
                        :aria-invalid="!!form.errors.password"
                        aria-describedby="password-error"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    >
                        <span v-if="showPassword">👁️</span>
                        <span v-else>👁️‍🗨️</span>
                    </button>
                </div>
                <p v-if="form.errors.password" id="password-error" class="mt-1 text-sm text-red-600">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="block">
                <label class="flex items-center">
                    <Checkbox
                        name="remember"
                        v-model:checked="form.remember"
                        aria-label="Remember me"
                    />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <Link
                    :href="route('register')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                    aria-label="Create new account"
                >
                    Don't have an account?
                </Link>
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing || isLoading }"
                    :disabled="form.processing || isLoading || !canSubmit"
                >
                    <span v-if="isLoading" class="mr-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    Log in
                </PrimaryButton>
            </div>

            <p v-if="generalError" class="mt-2 text-sm text-red-600">
                {{ generalError }}
            </p>
        </form>
    </GuestLayout>
</template>
