<script setup lang="ts">
    import { ref, computed, onMounted, onUnmounted } from 'vue';
    import { router } from '@inertiajs/vue3';
    import axios from 'axios';

    interface User {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    }

    interface SearchResponse {
    data: User[];
    }

    const query = ref('');
    const results = ref<User[]>([]);
    const isLoading = ref(false);
    const isOpen = ref(false);
    const selectedIndex = ref(-1);
    const searchTimeout = ref<number | null>(null);
    const searchInput = ref<HTMLInputElement | null>(null);
    const dropdown = ref<HTMLElement | null>(null);

    const hasResults = computed(() => results.value.length > 0);
    const showViewAll = computed(() => results.value.length >= 5);

    const searchUsers = async () => {
    if (query.value.length < 2) {
        results.value = [];
        isOpen.value = false;
        return;
    }

    isLoading.value = true;

    try {
        const response = await axios.get<SearchResponse>('/api/search-users', {
            params: { q: query.value }
    });

    console.log('API Response:', response);
    console.log('Response data:', response.data);
    console.log('Users data:', response.data.data);

    results.value = response.data.data;
    console.log('Results after assignment:', results.value);
    console.log('Results length:', results.value.length);
    console.log('hasResults computed:', results.value.length > 0);

    isOpen.value = true;
    console.log('isOpen set to:', isOpen.value);
    selectedIndex.value = -1;
  } catch (error) {
    console.error('Search error:', error);
    results.value = [];
    isOpen.value = false;
  } finally {
    isLoading.value = false;
  }
    };

    const onInput = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }

    searchTimeout.value = setTimeout(() => {
        searchUsers();
    }, 300);
    };

    const onFocus = () => {
    if (query.value.length >= 2 && results.value.length > 0) {
        isOpen.value = true;
    }
    };

    const onBlur = () => {
    // Delay hiding to allow clicking on results
    setTimeout(() => {
        isOpen.value = false;
        selectedIndex.value = -1;
    }, 150);
    };

    const onKeyDown = (event: KeyboardEvent) => {
    if (!isOpen.value) return;

    switch (event.key) {
        case 'ArrowDown':
        event.preventDefault();
        selectedIndex.value = Math.min(selectedIndex.value + 1, results.value.length);
        break;
        case 'ArrowUp':
        event.preventDefault();
        selectedIndex.value = Math.max(selectedIndex.value - 1, -1);
        break;
        case 'Enter':
        event.preventDefault();
        if (selectedIndex.value === -1 || selectedIndex.value === results.value.length) {
            viewAllResults();
        } else if (selectedIndex.value >= 0 && selectedIndex.value < results.value.length) {
            selectUser(results.value[selectedIndex.value]);
        }
        break;
        case 'Escape':
        isOpen.value = false;
        selectedIndex.value = -1;
        searchInput.value?.blur();
        break;
    }
    };

    const selectUser = (user: User) => {
    // You can customize this action - for now, we'll just navigate to search results
    router.get('/search-users', { q: user.name });
    isOpen.value = false;
    };

    const viewAllResults = () => {
    if (query.value.length >= 2) {
        router.get('/search-users', { q: query.value });
        isOpen.value = false;
    }
    };

    const getUserAvatar = (user: User) => {
    return user.avatar || '/default-avatar.svg';
    };

    onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!dropdown.value?.contains(e.target as Node) &&
            !searchInput.value?.contains(e.target as Node)) {
        isOpen.value = false;
        selectedIndex.value = -1;
        }
    });
    });

    onUnmounted(() => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
    });
</script>

<template>
<div class="relative">
    <div class="relative">
    <input
        ref="searchInput"
        v-model="query"
        @input="onInput"
        @focus="onFocus"
        @blur="onBlur"
        @keydown="onKeyDown"
        type="text"
        placeholder="Search users..."
        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        autocomplete="off"
    />

    <!-- Search Icon -->
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    <!-- Loading Spinner -->
    <div v-if="isLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    </div>

        <!-- Debug Info (remove this later) -->
    <div class="text-xs text-gray-500 mt-1">
      Debug: isOpen={{ isOpen }}, hasResults={{ hasResults }}, resultsLength={{ results.length }}, query={{ query }}
    </div>

    <!-- Dropdown Results -->
    <div
      v-if="isOpen"
      ref="dropdown"
      class="absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto"
    >
    <!-- No Results -->
    <div v-if="!isLoading && !hasResults && query.length >= 2" class="p-4 text-center text-gray-500">
        No users found for "{{ query }}"
    </div>

    <!-- Results -->
    <div v-if="hasResults" class="py-2">
        <button
        v-for="(user, index) in results"
        :key="user.id"
        @click="selectUser(user)"
        :class="[
            'w-full px-4 py-3 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none flex items-center space-x-3',
            { 'bg-gray-50': selectedIndex === index }
        ]"
        >
        <img
            :src="getUserAvatar(user)"
            :alt="user.name"
            class="w-8 h-8 rounded-full object-cover"
        />
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
            {{ user.name }}
            </p>
            <p class="text-sm text-gray-500 truncate">
            {{ user.email }}
            </p>
        </div>
        </button>

        <!-- View All Results -->
        <div v-if="showViewAll" class="border-t border-gray-100">
        <button
            @click="viewAllResults"
            :class="[
            'w-full px-4 py-3 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none text-sm text-blue-600 font-medium',
            { 'bg-gray-50': selectedIndex === results.length }
            ]"
        >
            View all results for "{{ query }}"
        </button>
        </div>
    </div>
    </div>
</div>
</template>
