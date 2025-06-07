<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useForm as useVeeForm } from 'vee-validate';
import * as yup from 'yup';
import Modal from '@/Components/Modal.vue';
import FieldInput from '@/Components/FieldInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    description?: string;
}

interface Props {
    show: boolean;
    user: User;
}

const props = defineProps<Props>();
const emit = defineEmits(['close']);

const avatarInput = ref<HTMLInputElement>();
const avatarPreview = ref<string | null>(null);

// Form validation schema
const schema = yup.object({
    name: yup.string().required('Name is required').max(50, 'Name cannot exceed 50 characters'),
    email: yup.string().required('Email is required').email('Email must be valid'),
    description: yup.string().max(255, 'Description cannot exceed 255 characters'),
});

// Vee-validate form
const { defineField, handleSubmit, resetForm, errors } = useVeeForm({
    validationSchema: schema,
    initialValues: {
        name: props.user.name,
        email: props.user.email,
        description: props.user.description || '',
    },
});

const [name] = defineField('name');
const [email] = defineField('email');
const [description] = defineField('description');

// Inertia form for submission
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    description: props.user.description || '',
    avatar: null as File | null,
});

// Watch vee-validate fields and sync with Inertia form
watch(name, (value) => { form.name = value; });
watch(email, (value) => { form.email = value; });
watch(description, (value) => { form.description = value; });

// Reset form when modal opens/closes
watch(() => props.show, (show) => {
    if (show) {
        resetFormData();
    } else {
        clearAvatarPreview();
    }
});

const resetFormData = () => {
    resetForm({
        values: {
            name: props.user.name,
            email: props.user.email,
            description: props.user.description || '',
        }
    });
    form.name = props.user.name;
    form.email = props.user.email;
    form.description = props.user.description || '';
    form.avatar = null;
    clearAvatarPreview();
};

const selectNewAvatar = () => {
    avatarInput.value?.click();
};

const updateAvatarPreview = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    form.avatar = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        avatarPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
};

const clearAvatarPreview = () => {
    avatarPreview.value = null;
    form.avatar = null;
    if (avatarInput.value) {
        avatarInput.value.value = '';
    }
};

const onSubmit = handleSubmit(() => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
        onError: () => {
            // Handle server validation errors if needed
        },
    });
});

const closeModal = () => {
    if (!form.processing) {
        emit('close');
    }
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Edit Profile
            </h2>

            <form @submit.prevent="onSubmit" class="space-y-6">
                <!-- Avatar Upload -->
                <div>
                    <InputLabel for="avatar" value="Profile Photo" />
                    <div class="mt-2 flex items-center space-x-4">
                        <!-- Current/Preview Avatar -->
                        <div class="flex-shrink-0">
                            <img
                                v-if="avatarPreview"
                                class="h-16 w-16 rounded-full object-cover"
                                :src="avatarPreview"
                                alt="Avatar preview"
                            />
                            <img
                                v-else-if="user.avatar"
                                class="h-16 w-16 rounded-full object-cover"
                                :src="user.avatar"
                                :alt="user.name"
                            />
                            <div
                                v-else
                                class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center"
                            >
                                <span class="text-lg font-medium text-gray-600">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <!-- Upload Button -->
                        <div>
                            <button
                                type="button"
                                @click="selectNewAvatar"
                                class="text-sm text-indigo-600 hover:text-indigo-500"
                            >
                                Change Photo
                            </button>
                            <input
                                ref="avatarInput"
                                type="file"
                                class="hidden"
                                accept="image/*"
                                @change="updateAvatarPreview"
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                JPG, PNG, GIF up to 2MB
                            </p>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.avatar" />
                </div>

                <!-- Name Field -->
                <div>
                    <InputLabel for="name" value="Name" />
                    <FieldInput
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="name"
                        rules="required|max:50"
                        placeholder="Enter your full name"
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email Field -->
                <div>
                    <InputLabel for="email" value="Email" />
                    <FieldInput
                        id="email"
                        name="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="email"
                        rules="required|email"
                        placeholder="Enter your email address"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Description Field -->
                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea
                        id="description"
                        v-model="description"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        rows="3"
                        placeholder="Tell us about yourself..."
                        maxlength="255"
                    ></textarea>
                    <InputError class="mt-2" :message="errors.description || form.errors.description" />
                    <p class="text-xs text-gray-500 mt-1">
                        {{ description.length }}/255 characters
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4">
                    <SecondaryButton @click="closeModal" :disabled="form.processing">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton type="submit" :disabled="form.processing">
                        <span v-if="form.processing">Updating...</span>
                        <span v-else>Update Profile</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
