<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Post;

interface PostRepositoryInterface
{
    /**
     * Get all posts for social feed (from all users)
     *
     * @param int|null $currentUserId
     * @return Post[]
     */
    public function getAllPosts(?int $currentUserId = null): array;

    /**
     * Get posts by specific user
     *
     * @param int $userId
     * @param int|null $currentUserId
     * @return Post[]
     */
    public function getByUserId(int $userId, ?int $currentUserId = null): array;

    /**
     * Get a specific post by ID
     *
     * @param int $id
     * @param int|null $currentUserId
     * @return Post|null
     */
    public function findById(int $id, ?int $currentUserId = null): ?Post;

    /**
     * Get a specific post by ID for editing (authorization check)
     *
     * @param int $id
     * @param int $userId
     * @return Post|null
     */
    public function findByIdAndUserId(int $id, int $userId): ?Post;

    /**
     * Create a new post
     *
     * @param string $title
     * @param string $content
     * @param int $userId
     * @return Post
     */
    public function create(string $title, string $content, int $userId): Post;

    /**
     * Update an existing post (only by owner)
     *
     * @param int $id
     * @param string $title
     * @param string $content
     * @param int $userId
     * @return Post|null
     */
    public function update(int $id, string $title, string $content, int $userId): ?Post;

    /**
     * Delete a post (only by owner)
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function delete(int $id, int $userId): bool;
}
