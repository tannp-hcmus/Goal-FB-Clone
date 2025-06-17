<script setup lang="ts">
    import { ref, reactive } from 'vue';
    import { Head, useForm, usePage } from '@inertiajs/vue3';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import FieldInput from '@/Components/FieldInput.vue';
    import TextareaInput from '@/Components/TextareaInput.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import Modal from '@/Components/Modal.vue';
    import Toast from '@/Components/Toast.vue';
    import CommentSection from '@/Components/CommentSection.vue';
    import { Form as VeeForm } from 'vee-validate';

    interface NestedComment {
        id: number;
        content: string;
        author_name: string;
        user_id: number;
        created_at: string;
        can_delete: boolean;
    }

    interface Comment {
        id: number;
        content: string;
        author_name: string;
        user_id: number;
        created_at: string;
        can_delete: boolean;
        replies: NestedComment[];
    }

    interface Post {
        id: number;
        title: string;
        content: string;
        author_name: string;
        user_id: number;
        created_at: string;
        updated_at: string;
        likes_count: number;
        is_liked: boolean;
        comments: Comment[];
        can_edit: boolean;
    }

    interface Props {
        posts: Post[];
    }

    defineProps<Props>();
    const page = usePage();

    // Form refs for manual reset
    const createFormRef = ref();
    const editFormRef = ref();

    // Reactive keys to force form re-rendering when reset
    const createFormKey = ref(0);
    const editFormKey = ref(0);

    // Create form
    const createForm = useForm({
        title: '',
        content: '',
    });

    // Edit form
    const editForm = useForm({
        title: '',
        content: '',
    });

    // State
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const editingPost = ref<Post | null>(null);
    const deletingPost = ref<Post | null>(null);
    const toastMessage = ref('');
    const toastType = ref<'success' | 'error'>('success');
    const showToast = ref(false);
    const showComments = reactive<Record<number, boolean>>({});

    // Flash messages from backend
    const flash = reactive({
        success: (page.props.flash as any)?.success || '',
        error: (page.props.flash as any)?.error || '',
    });

    // Show flash messages as toast
    if (flash.success) {
        showToastMessage(flash.success, 'success');
    }
    if (flash.error) {
        showToastMessage(flash.error, 'error');
    }

    function showToastMessage(message: string, type: 'success' | 'error') {
        toastMessage.value = message;
        toastType.value = type;
        showToast.value = true;
    }

    function createPost() {
        createForm.post(route('posts.store'), {
            onSuccess: () => {
                createForm.reset();
                createForm.clearErrors();
                // Reset VeeValidate form and force re-render
                if (createFormRef.value) {
                    createFormRef.value.resetForm();
                }
                createFormKey.value++;
                showToastMessage('Post created successfully!', 'success');
            },
            onError: () => {
                showToastMessage('Failed to create post. Please check your input.', 'error');
            },
        });
    }

    function openEditModal(post: Post) {
        editingPost.value = post;
        editForm.title = post.title;
        editForm.content = post.content;
        showEditModal.value = true;
    }

    function updatePost() {
        if (!editingPost.value) return;

        editForm.put(route('posts.update', editingPost.value.id), {
            onSuccess: () => {
                showEditModal.value = false;
                editingPost.value = null;
                editForm.reset();
                editForm.clearErrors();
                // Reset VeeValidate form and force re-render
                if (editFormRef.value) {
                    editFormRef.value.resetForm();
                }
                editFormKey.value++;
                showToastMessage('Post updated successfully!', 'success');
            },
            onError: () => {
                showToastMessage('Failed to update post. Please check your input.', 'error');
            },
        });
    }

    function openDeleteModal(post: Post) {
        deletingPost.value = post;
        showDeleteModal.value = true;
    }

    function deletePost() {
        if (!deletingPost.value) return;

        useForm({}).delete(route('posts.destroy', deletingPost.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                deletingPost.value = null;
                showToastMessage('Post deleted successfully!', 'success');
            },
            onError: () => {
                showToastMessage('Failed to delete post.', 'error');
            },
        });
    }

    function toggleLike(postId: number) {
        useForm({}).post(route('posts.like', postId), {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                showToastMessage('Failed to update like.', 'error');
            },
        });
    }

    function toggleComments(postId: number) {
        showComments[postId] = !showComments[postId];
    }

    function closeEditModal() {
        showEditModal.value = false;
        editingPost.value = null;
        editForm.reset();
        editForm.clearErrors();
        // Reset VeeValidate form and force re-render
        if (editFormRef.value) {
            editFormRef.value.resetForm();
        }
        editFormKey.value++;
    }

    function closeDeleteModal() {
        showDeleteModal.value = false;
        deletingPost.value = null;
    }

    function closeToast() {
        showToast.value = false;
    }

    function handleToastFromComment(message: string, type: 'success' | 'error') {
        showToastMessage(message, type);
    }
</script>

<template>
    <Head title="Social Feed" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Social Feed
                </h2>
                <div class="text-sm text-gray-500">
                    Share your thoughts with the community
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Create Post Form -->
                <div class="mb-8 overflow-hidden bg-white shadow-lg sm:rounded-xl border border-gray-200">
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">What's on your mind?</h3>
                                <p class="text-sm text-gray-600">Share something with your community</p>
                            </div>
                        </div>

                        <VeeForm ref="createFormRef" @submit="createPost" class="space-y-6" :key="`create-post-${createFormKey}`">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div class="lg:col-span-2">
                                    <FieldInput
                                        name="create_post_title"
                                        rules="required"
                                        v-model="createForm.title"
                                        placeholder="What's this about?"
                                        :maxlength="100"
                                        class="text-lg"
                                        :validation-label="'Title'"
                                    />
                                </div>
                                <div class="flex items-end">
                                    <PrimaryButton
                                        type="submit"
                                        :disabled="createForm.processing"
                                        class="w-full lg:w-auto px-8 py-3 text-base font-medium shadow-lg hover:shadow-xl transition-all duration-200"
                                    >
                                        {{ createForm.processing ? 'Posting...' : 'Share Post' }}
                                    </PrimaryButton>
                                </div>
                            </div>

                            <div>
                                <TextareaInput
                                    name="create_post_content"
                                    rules="required"
                                    v-model="createForm.content"
                                    placeholder="Share your thoughts..."
                                    :maxlength="1000"
                                    :rows="4"
                                    class="text-base"
                                    :validation-label="'Content'"
                                />
                            </div>
                        </VeeForm>
                    </div>
                </div>

                <!-- Posts Feed -->
                <div class="space-y-8">
                    <div v-if="posts.length === 0" class="text-center py-16 text-gray-500 bg-white rounded-xl shadow-lg border border-gray-200">
                        <div class="text-6xl mb-4">🌟</div>
                        <p class="text-xl font-medium mb-2">Welcome to the community!</p>
                        <p class="text-gray-400">Be the first to share something amazing.</p>
                    </div>

                    <article
                        v-for="post in posts"
                        :key="post.id"
                        class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow duration-300"
                    >
                        <!-- Post Header -->
                        <header class="p-6 border-b border-gray-100">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                            {{ post.author_name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg">{{ post.author_name }}</h4>
                                            <p class="text-sm text-gray-500">{{ post.created_at }}</p>
                                        </div>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4 leading-tight">{{ post.title }}</h3>
                                    <div class="prose prose-gray max-w-none">
                                        <p class="text-gray-700 whitespace-pre-wrap leading-relaxed text-lg">{{ post.content }}</p>
                                    </div>
                                </div>
                                <div v-if="post.can_edit" class="flex space-x-2 ml-6">
                                    <SecondaryButton @click="openEditModal(post)" class="text-sm px-4 py-2 rounded-lg">
                                        Edit
                                    </SecondaryButton>
                                    <DangerButton @click="openDeleteModal(post)" class="text-sm px-4 py-2 rounded-lg">
                                        Delete
                                    </DangerButton>
                                </div>
                            </div>
                        </header>

                        <!-- Post Actions -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-8">
                                    <!-- Like Button -->
                                    <button
                                        @click="toggleLike(post.id)"
                                        :class="[
                                            'flex items-center space-x-2 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 transform hover:scale-105',
                                            post.is_liked
                                                ? 'text-red-600 bg-red-50 hover:bg-red-100'
                                                : 'text-gray-600 hover:text-red-600 hover:bg-red-50'
                                        ]"
                                    >
                                        <svg
                                            :class="[
                                                'w-5 h-5 transition-all duration-200',
                                                post.is_liked ? 'fill-current text-red-600' : 'stroke-current fill-none'
                                            ]"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
                                            />
                                        </svg>
                                        <span>{{ post.likes_count }}</span>
                                        <span class="hidden sm:inline">{{ post.likes_count === 1 ? 'Like' : 'Likes' }}</span>
                                    </button>
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ post.updated_at !== post.created_at ? 'Edited' : '' }}
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="px-6 py-4">
                            <CommentSection
                                :post-id="post.id"
                                :comments="post.comments"
                                :show-comments="showComments[post.id] || false"
                                @toggle-comments="toggleComments(post.id)"
                                @show-toast="handleToastFromComment"
                            />
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" @close="closeEditModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Post</h3>

                <VeeForm ref="editFormRef" @submit="updatePost" class="space-y-4" :key="`edit-post-${editFormKey}`">
                    <div>
                        <label for="edit_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Title
                        </label>
                        <FieldInput
                            name="edit_post_title"
                            rules="required"
                            v-model="editForm.title"
                            placeholder="Enter post title..."
                            :maxlength="100"
                            :validation-label="'Title'"
                        />
                    </div>

                    <div>
                        <label for="edit_content" class="block text-sm font-medium text-gray-700 mb-1">
                            Content
                        </label>
                        <TextareaInput
                            name="edit_post_content"
                            rules="required"
                            v-model="editForm.content"
                            placeholder="Write your post content..."
                            :maxlength="1000"
                            :validation-label="'Content'"
                        />
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <SecondaryButton @click="closeEditModal">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="editForm.processing"
                        >
                            {{ editForm.processing ? 'Updating...' : 'Update Post' }}
                        </PrimaryButton>
                    </div>
                </VeeForm>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Post</h3>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete "{{ deletingPost?.title }}"? This action cannot be undone.
                </p>

                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="closeDeleteModal">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="deletePost">
                        Delete Post
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Toast Notifications -->
        <div class="fixed top-4 right-4 z-50">
            <Toast
                :show="showToast"
                :message="toastMessage"
                :type="toastType"
                @close="closeToast"
            />
        </div>
    </AuthenticatedLayout>
</template>
