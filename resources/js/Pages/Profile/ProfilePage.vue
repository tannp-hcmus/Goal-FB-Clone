<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProfileEditModal from './ProfileEditModal.vue';
import Toast from '@/Components/Toast.vue';

interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    description?: string;
}

defineProps<{
    user: User;
}>();

const page = usePage();

const showEditModal = ref(false);
const lastFlashTime = ref(0);
const flashDismissed = ref(false);

// Computed properties for flash messages
const flashMessage = computed(() => {
    const flash = page.props.flash as { success?: string; error?: string } | undefined;
    return flash?.success || flash?.error || '';
});

const flashType = computed(() => {
    const flash = page.props.flash as { success?: string; error?: string } | undefined;
    return flash?.success ? 'success' : 'error';
});

// Show toast when there's a flash message and it hasn't been dismissed
const shouldShowToast = computed(() => {
    return Boolean(flashMessage.value) && !flashDismissed.value;
});

// Watch for flash messages and reset dismissed state on any flash (even same message)
watch(() => page.props.flash, (newFlash) => {
    const flash = newFlash as { success?: string; error?: string } | undefined;
    if (flash && (flash.success || flash.error)) {
        // Use current timestamp to detect new flashes
        const currentTime = Date.now();
        if (currentTime > lastFlashTime.value + 1000) { // 1 second buffer
            flashDismissed.value = false;
            lastFlashTime.value = currentTime;
        }
    }
}, { deep: true, immediate: true });

const openEditModal = () => {
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const clearFlash = () => {
    flashDismissed.value = true;
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Profile Header -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center space-x-6">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <img
                                        v-if="user.avatar"
                                        class="h-20 w-20 rounded-full object-cover"
                                        :src="user.avatar"
                                        :alt="user.name"
                                    />
                                    <div
                                        v-else
                                        class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center"
                                    >
                                        <span class="text-xl font-medium text-gray-600">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>

                                <!-- User Info -->
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">
                                        {{ user.name }}
                                    </h1>
                                    <p class="text-gray-600">{{ user.email }}</p>
                                    <p v-if="user.description" class="text-gray-700 mt-2">
                                        {{ user.description }}
                                    </p>
                                    <p v-else class="text-gray-500 mt-2 italic">
                                        No description provided
                                    </p>
                                </div>
                            </div>

                            <!-- Edit Button -->
                            <button
                                @click="openEditModal"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Edit Profile
                            </button>
                        </div>

                        <!-- Profile Details Card -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Profile Information
                            </h3>
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Full Name
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ user.name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">
                                        Email Address
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ user.email }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Description
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ user.description || 'No description provided' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <ProfileEditModal
            :show="showEditModal"
            :user="user"
            @close="closeEditModal"
        />

        <!-- Toast Notification -->
        <div
            class="fixed top-4 right-4 z-50"
        >
            <Toast
                :message="flashMessage"
                :type="flashType"
                :show="shouldShowToast"
                @close="clearFlash"
            />
        </div>
    </AuthenticatedLayout>
</template>
