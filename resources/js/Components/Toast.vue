<script setup lang="ts">
import { ref, watch } from 'vue';

interface Props {
    message: string;
    type?: 'success' | 'error';
    show: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'success',
});

const emit = defineEmits(['close']);

const visible = ref(false);

watch(() => props.show, (newShow) => {
    if (newShow) {
        visible.value = true;
        // Auto-hide after 3 seconds
        setTimeout(() => {
            hide();
        }, 3000);
    }
}, { immediate: true });

const hide = () => {
    visible.value = false;
    setTimeout(() => {
        emit('close');
    }, 300);
};
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible"
            :class="[
                'pointer-events-auto w-full max-w-md overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5',
                type === 'success'
                    ? 'bg-white border-l-4 border-green-400'
                    : 'bg-white border-l-4 border-red-400'
            ]"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg
                            v-if="type === 'success'"
                            class="h-6 w-6 text-green-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <!-- Error Icon -->
                        <svg
                            v-else
                            class="h-6 w-6 text-red-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                            />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p :class="[
                            'text-sm font-medium leading-5',
                            type === 'success' ? 'text-gray-900' : 'text-gray-900'
                        ]">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            type="button"
                            @click="hide"
                            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <span class="sr-only">Close</span>
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
