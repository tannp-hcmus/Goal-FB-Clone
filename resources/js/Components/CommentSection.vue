<script setup lang="ts">
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import type { InertiaForm } from '@inertiajs/vue3';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
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

interface Props {
    postId: number;
    comments: Comment[];
    showComments: boolean;
}

type ReplyFormData = {
    content: string;
    parent_comment_id: number;
};

const props = defineProps<Props>();
const emit = defineEmits(['toggle-comments', 'show-toast']);
const page = usePage();

// Form refs for manual reset
const mainCommentFormRef = ref();
const replyFormRefs = ref<Record<number, any>>({});

// Main comment form
const mainCommentForm = useForm({ content: '' });

// Reply forms - using ref with proper typing
const replyForms = ref<Record<number, InertiaForm<ReplyFormData>>>({});

// State for showing reply forms
const showReplyForm = ref<Record<number, boolean>>({});

// Reactive keys to force form re-rendering when reset
const mainFormKey = ref(0);
const replyFormKeys = ref<Record<number, number>>({});

function getReplyForm(commentId: number): InertiaForm<ReplyFormData> {
    if (!replyForms.value[commentId]) {
        replyForms.value[commentId] = useForm({
            content: '',
            parent_comment_id: commentId,
        });
    }
    return replyForms.value[commentId];
}

function getReplyFormKey(commentId: number): number {
    if (replyFormKeys.value[commentId] === undefined) {
        replyFormKeys.value[commentId] = 0;
    }
    return replyFormKeys.value[commentId];
}

function addMainComment() {
    mainCommentForm.post(route('posts.comments.store', props.postId), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            mainCommentForm.reset();
            mainCommentForm.clearErrors();
            // Reset VeeValidate form and force re-render
            if (mainCommentFormRef.value) {
                mainCommentFormRef.value.resetForm();
            }
            mainFormKey.value++;
            emit('show-toast', 'Comment added successfully!', 'success');
        },
        onError: () => {
            emit('show-toast', 'Failed to add comment.', 'error');
        },
    });
}

function addReply(parentCommentId: number) {
    const form = getReplyForm(parentCommentId);

    form.post(route('posts.comments.store', props.postId), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            showReplyForm.value[parentCommentId] = false;
            // Reset VeeValidate form and force re-render
            if (replyFormRefs.value[parentCommentId]) {
                replyFormRefs.value[parentCommentId].resetForm();
            }
            replyFormKeys.value[parentCommentId] = (replyFormKeys.value[parentCommentId] || 0) + 1;
            emit('show-toast', 'Reply added successfully!', 'success');
        },
        onError: () => {
            emit('show-toast', 'Failed to add reply.', 'error');
        },
    });
}

function deleteComment(commentId: number) {
    useForm({}).delete(route('comments.destroy', commentId), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            emit('show-toast', 'Comment deleted successfully!', 'success');
        },
        onError: () => {
            emit('show-toast', 'Failed to delete comment.', 'error');
        },
    });
}

function toggleReplyForm(commentId: number) {
    showReplyForm.value[commentId] = !showReplyForm.value[commentId];
    // Reset form when closing
    if (!showReplyForm.value[commentId] && replyForms.value[commentId]) {
        replyForms.value[commentId].reset();
        replyForms.value[commentId].clearErrors();
        if (replyFormRefs.value[commentId]) {
            replyFormRefs.value[commentId].resetForm();
        }
        replyFormKeys.value[commentId] = (replyFormKeys.value[commentId] || 0) + 1;
    }
}

function toggleComments() {
    emit('toggle-comments');
}

// Helper functions for template
function isReplyFormShown(commentId: number): boolean {
    return showReplyForm.value[commentId] || false;
}

function isReplyFormProcessing(commentId: number): boolean {
    return getReplyForm(commentId).processing;
}
</script>

<template>
    <div class="space-y-4">
        <!-- Comment Toggle Button -->
        <div class="flex items-center justify-between">
            <button
                @click="toggleComments"
                :class="[
                    'flex items-center space-x-3 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 transform hover:scale-105',
                    showComments
                        ? 'text-blue-600 bg-blue-50 hover:bg-blue-100'
                        : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50'
                ]"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>{{ comments.length }}</span>
                <span class="hidden sm:inline">{{ comments.length === 1 ? 'Comment' : 'Comments' }}</span>
                <svg
                    :class="[
                        'w-4 h-4 transition-transform duration-200',
                        showComments ? 'rotate-180' : ''
                    ]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <!-- Comments Section -->
        <div v-if="showComments" class="space-y-6 bg-gray-50 rounded-xl p-6 border border-gray-200">
            <!-- Add Main Comment Form -->
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <div class="flex space-x-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg flex-shrink-0">
                        {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1">
                        <VeeForm ref="mainCommentFormRef" @submit="addMainComment" class="space-y-4" :key="`main-comment-${props.postId}-${mainFormKey}`">
                            <TextareaInput
                                :name="`main_comment_${props.postId}`"
                                rules="required"
                                v-model="mainCommentForm.content"
                                placeholder="What do you think about this post?"
                                :rows="1"
                                class="resize-none"
                                :validation-label="'Comment'"
                            />
                            <div class="flex justify-end">
                                <PrimaryButton
                                    type="submit"
                                    :disabled="mainCommentForm.processing"
                                    class="px-6 py-2 text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
                                >
                                    {{ mainCommentForm.processing ? 'Posting...' : 'Post Comment' }}
                                </PrimaryButton>
                            </div>
                        </VeeForm>
                    </div>
                </div>
            </div>

            <!-- Comments Thread -->
            <div class="space-y-6">
                <div
                    v-for="comment in comments"
                    :key="comment.id"
                    class="comment-thread bg-white rounded-lg p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200"
                >
                    <!-- Main Comment -->
                    <div class="flex space-x-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg flex-shrink-0">
                            {{ comment.author_name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1">
                            <!-- Comment Content -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <h5 class="font-bold text-gray-900 text-sm">{{ comment.author_name }}</h5>
                                        <span class="text-xs text-gray-500">{{ comment.created_at }}</span>
                                    </div>
                                    <button
                                        v-if="comment.can_delete"
                                        @click="deleteComment(comment.id)"
                                        class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all duration-200"
                                        title="Delete comment"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-gray-700 leading-relaxed">{{ comment.content }}</p>
                            </div>

                            <!-- Comment Actions -->
                            <div class="flex items-center justify-between mb-4">
                                <button
                                    @click="toggleReplyForm(comment.id)"
                                    :class="[
                                        'flex items-center space-x-2 px-3 py-1.5 rounded-full text-sm font-semibold transition-all duration-200 transform hover:scale-105',
                                        isReplyFormShown(comment.id)
                                            ? 'text-blue-600 bg-blue-100 hover:bg-blue-200'
                                            : 'text-blue-600 bg-blue-50 hover:bg-blue-100'
                                    ]"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    <span>{{ isReplyFormShown(comment.id) ? 'Cancel Reply' : 'Reply' }}</span>
                                </button>

                                <div v-if="comment.replies.length > 0" class="flex items-center space-x-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                    </svg>
                                    <span class="font-medium">{{ comment.replies.length }} {{ comment.replies.length === 1 ? 'reply' : 'replies' }}</span>
                                </div>
                            </div>

                            <!-- Reply Form -->
                            <div v-if="isReplyFormShown(comment.id)" class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-l-4 border-blue-400 shadow-sm">
                                <div class="flex space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-md flex-shrink-0">
                                        {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="flex-1">
                                        <VeeForm :ref="(el) => replyFormRefs[comment.id] = el" @submit="() => addReply(comment.id)" class="space-y-4" :key="`reply-form-${comment.id}-${getReplyFormKey(comment.id)}`">
                                            <TextareaInput
                                                :name="`reply_comment_${comment.id}`"
                                                rules="required"
                                                v-model="getReplyForm(comment.id).content"
                                                :placeholder="`Reply to ${comment.author_name}...`"
                                                :rows="1"
                                                class="resize-none"
                                                :validation-label="'Reply'"
                                            />
                                            <div class="flex justify-end space-x-3">
                                                <SecondaryButton
                                                    type="button"
                                                    @click="toggleReplyForm(comment.id)"
                                                    class="px-4 py-2 text-sm rounded-lg"
                                                >
                                                    Cancel
                                                </SecondaryButton>
                                                <PrimaryButton
                                                    type="submit"
                                                    :disabled="isReplyFormProcessing(comment.id)"
                                                    class="px-4 py-2 text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
                                                >
                                                    {{ isReplyFormProcessing(comment.id) ? 'Replying...' : 'Post Reply' }}
                                                </PrimaryButton>
                                            </div>
                                        </VeeForm>
                                    </div>
                                </div>
                            </div>

                            <!-- Nested Replies -->
                            <div v-if="comment.replies.length > 0" class="space-y-4">
                                <div class="border-l-2 border-gray-200 pl-6 space-y-4">
                                    <div
                                        v-for="reply in comment.replies"
                                        :key="reply.id"
                                        class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:bg-white transition-colors duration-200"
                                    >
                                        <div class="flex space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-md flex-shrink-0">
                                                {{ reply.author_name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center space-x-2">
                                                        <h6 class="font-bold text-gray-900 text-sm">{{ reply.author_name }}</h6>
                                                        <span class="text-xs text-gray-500">{{ reply.created_at }}</span>
                                                    </div>
                                                    <button
                                                        v-if="reply.can_delete"
                                                        @click="deleteComment(reply.id)"
                                                        class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all duration-200"
                                                        title="Delete reply"
                                                    >
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <p class="text-gray-700 text-sm leading-relaxed">{{ reply.content }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.comment-thread {
    transition: all 0.2s ease-in-out;
}

.comment-thread:hover {
    transform: translateY(-1px);
}

/* Custom scrollbar for textareas */
textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
