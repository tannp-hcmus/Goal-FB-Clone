<?php

namespace App\Domain\Interfaces\Services;

interface UserSearchServiceInterface
{
    /**
     * Search users by name or email using Elasticsearch.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchUsers(string $query, int $limit = 5): array;

    /**
     * Search users with pagination for full results page.
     *
     * @param string $query
     * @param int $perPage
     * @return array
     */
    public function searchUsersWithPagination(string $query, int $perPage = 20): array;
}
