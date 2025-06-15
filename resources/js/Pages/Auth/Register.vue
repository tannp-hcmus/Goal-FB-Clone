<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FieldInput from '@/Components/FieldInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    firstname: '',
    lastname: '',
    gender: '',
    birthday: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const isLoading = ref(false);
const failedAttempts = ref(0);
const lastAttemptTime = ref<number | null>(null);
const generalError = ref<string | null>(null);

const calculateAge = (birthday: string) => {
    const today = new Date();
    const birthDate = new Date(birthday);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    return age;
};

const passwordStrength = computed(() => {
    if (!form.password) return { score: 0, label: '' };

    let score = 0;
    if (form.password.length >= 8) score++;
    if (/[A-Z]/.test(form.password)) score++;
    if (/[a-z]/.test(form.password)) score++;
    if (/[0-9]/.test(form.password)) score++;
    if (/[^A-Za-z0-9]/.test(form.password)) score++;

    const labels = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
    return {
        score,
        label: labels[score - 1] || '',
        color: ['red', 'orange', 'yellow', 'lightgreen', 'green'][score - 1] || 'gray'
    };
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
    // Only validate age since it's not covered by vee-validate
    if (form.birthday) {
        const age = calculateAge(form.birthday);
        if (age < 13) {
            form.errors.birthday = 'You must be at least 13 years old to register';
            return;
        }
    }

    if (!canSubmit.value) {
        generalError.value = 'Too many failed attempts. Please try again in 5 minutes.';
        return;
    }

    isLoading.value = true;
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
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
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="firstname" value="First Name" />

                <FieldInput
                    id="firstname"
                    name="firstname"
                    type="text"
                    rules="required"
                    placeholder="First Name"
                    v-model="form.firstname"
                    :class="{ 'border-red-500': form.errors.firstname }"
                    aria-label="First name"
                    :aria-invalid="!!form.errors.firstname"
                    aria-describedby="firstname-error"
                />
                <p v-if="form.errors.firstname" id="firstname-error" class="mt-1 text-sm text-red-600">
                    {{ form.errors.firstname }}
                </p>
            </div>

            <div>
                <InputLabel for="lastname" value="Last Name" />

                <FieldInput
                    id="lastname"
                    name="lastname"
                    type="text"
                    rules="required"
                    placeholder="Last Name"
                    v-model="form.lastname"
                    :class="{ 'border-red-500': form.errors.lastname }"
                    aria-label="Last name"
                    :aria-invalid="!!form.errors.lastname"
                    aria-describedby="lastname-error"
                />
                <p v-if="form.errors.lastname" id="lastname-error" class="mt-1 text-sm text-red-600">
                    {{ form.errors.lastname }}
                </p>
            </div>

            <div>
                <InputLabel value="Gender" />
                <div class="grid grid-cols-3 gap-3">
                    <label
                        class="flex items-center justify-between border border-gray-300 rounded-md p-2.5 cursor-pointer hover:bg-gray-50"
                        :class="{ 'border-red-500': form.errors.gender }"
                    >
                        <span>Female</span>
                        <input
                            type="radio"
                            name="gender"
                            value="female"
                            v-model="form.gender"
                            class="w-4 h-4 text-facebook-blue"
                            required
                            aria-label="Female"
                        />
                    </label>
                    <label
                        class="flex items-center justify-between border border-gray-300 rounded-md p-2.5 cursor-pointer hover:bg-gray-50"
                        :class="{ 'border-red-500': form.errors.gender }"
                    >
                        <span>Male</span>
                        <input
                            type="radio"
                            name="gender"
                            value="male"
                            v-model="form.gender"
                            class="w-4 h-4 text-facebook-blue"
                            aria-label="Male"
                        />
                    </label>
                    <label
                        class="flex items-center justify-between border border-gray-300 rounded-md p-2.5 cursor-pointer hover:bg-gray-50"
                        :class="{ 'border-red-500': form.errors.gender }"
                    >
                        <span>Custom</span>
                        <input
                            type="radio"
                            name="gender"
                            value="custom"
                            v-model="form.gender"
                            class="w-4 h-4 text-facebook-blue"
                            aria-label="Custom"
                        />
                    </label>
                </div>
                <p v-if="form.errors.gender" class="mt-1 text-sm text-red-600">
                    {{ form.errors.gender }}
                </p>
            </div>

            <div>
                <InputLabel for="birthday" value="Birthday" />

                <FieldInput
                    id="birthday"
                    name="birthday"
                    type="date"
                    rules="required"
                    v-model="form.birthday"
                    :class="{ 'border-red-500': form.errors.birthday }"
                    aria-label="Birthday"
                    :aria-invalid="!!form.errors.birthday"
                    aria-describedby="birthday-error"
                />
                <p v-if="form.errors.birthday" id="birthday-error" class="mt-1 text-sm text-red-600">
                    {{ form.errors.birthday }}
                </p>
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <FieldInput
                    id="email"
                    name="email"
                    type="email"
                    rules="required|email"
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
                        v-model="form.password"
                        :class="{ 'border-red-500': form.errors.password }"
                        aria-label="Password"
                        :aria-invalid="!!form.errors.password"
                        aria-describedby="password-error password-strength"
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
                <div v-if="form.password" class="mt-1">
                    <div class="h-1 w-full bg-gray-200 rounded-full">
                        <div
                            class="h-1 rounded-full transition-all duration-300"
                            :style="{
                                width: `${(passwordStrength.score / 5) * 100}%`,
                                backgroundColor: passwordStrength.color
                            }"
                        ></div>
                    </div>
                    <p id="password-strength" class="text-sm" :class="`text-${passwordStrength.color}-600`">
                        Password strength: {{ passwordStrength.label }}
                    </p>
                </div>
                <p v-if="form.errors.password" id="password-error" class="mt-1 text-sm text-red-600">
                    {{ form.errors.password }}
                </p>
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <FieldInput
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    rules="required|min:8"
                    v-model="form.password_confirmation"
                    :class="{ 'border-red-500': form.errors.password_confirmation }"
                    aria-label="Confirm password"
                    :aria-invalid="!!form.errors.password_confirmation"
                    aria-describedby="password-confirmation-error"
                />
                <p v-if="form.errors.password_confirmation" id="password-confirmation-error" class="mt-1 text-sm text-red-600">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <div class="flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                    aria-label="Already have an account"
                >
                    Already registered?
                </Link>

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
                    Register
                </PrimaryButton>
            </div>

            <p v-if="generalError" class="mt-2 text-sm text-red-600">
                {{ generalError }}
            </p>
        </form>
    </GuestLayout>
</template>
