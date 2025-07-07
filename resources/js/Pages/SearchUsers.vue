<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

    interface User {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    }

    interface Props {
    query: string;
    users: User[];
    }

    const props = defineProps<Props>();

    const getUserAvatar = (user: User) => {
    return user.avatar || '/default-avatar.svg';
    };
</script>

<template>
  <Head title="Search Users" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Search Users
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Search Query Display -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900">
                <span v-if="query">Search results for "{{ query }}"</span>
                <span v-else>Search Users</span>
              </h3>
              <p class="mt-1 text-sm text-gray-600">
                {{ users.length }} {{ users.length === 1 ? 'result' : 'results' }}
                <span v-if="query"> found</span>
              </p>
            </div>

            <!-- No Query State -->
            <div v-if="!query" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No search query</h3>
              <p class="mt-1 text-sm text-gray-500">Use the search bar above to find users by name or email.</p>
            </div>

            <!-- No Results State -->
            <div v-else-if="users.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
              <p class="mt-1 text-sm text-gray-500">
                We couldn't find any users matching "{{ query }}". Try a different search term.
              </p>
            </div>

            <!-- Results Grid -->
            <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="user in users"
                :key="user.id"
                class="relative rounded-lg border border-gray-200 bg-white px-6 py-4 shadow-sm hover:shadow-md transition-shadow duration-200"
              >
                <div class="flex items-center space-x-3">
                  <img
                    :src="getUserAvatar(user)"
                    :alt="user.name"
                    class="h-12 w-12 rounded-full object-cover"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-base font-medium text-gray-900 truncate">
                      {{ user.name }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      {{ user.email }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
