<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Post as PostEntity;
use App\Domain\Interfaces\Repositories\PostRepositoryInterface;
use App\Infrastructure\Models\Post;

class EloquentPostRepository implements PostRepositoryInterface
{
    /**
     * Get all posts for social feed (from all users)
     *
     * @param int|null $currentUserId
     * @return PostEntity[]
     */
    public function getAllPosts(?int $currentUserId = null): array
    {
        return Post::with(['user', 'likes', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Post $post) => $post->toDomainEntity($currentUserId))
            ->toArray();
    }

    /**
     * Get posts by specific user
     *
     * @param int $userId
     * @param int|null $currentUserId
     * @return PostEntity[]
     */
    public function getByUserId(int $userId, ?int $currentUserId = null): array
    {
        return Post::with(['user', 'likes', 'comments.user'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Post $post) => $post->toDomainEntity($currentUserId))
            ->toArray();
    }

    /**
     * Get a specific post by ID
     *
     * @param int $id
     * @param int|null $currentUserId
     * @return PostEntity|null
     */
    public function findById(int $id, ?int $currentUserId = null): ?PostEntity
    {
        $post = Post::with(['user', 'likes', 'comments.user'])
            ->find($id);

        return $post ? $post->toDomainEntity($currentUserId) : null;
    }

    /**
     * Get a specific post by ID for editing (authorization check)
     *
     * @param int $id
     * @param int $userId
     * @return PostEntity|null
     */
    public function findByIdAndUserId(int $id, int $userId): ?PostEntity
    {
        $post = Post::with(['user', 'likes', 'comments.user'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        return $post ? $post->toDomainEntity($userId) : null;
    }

    /**
     * Create a new post
     *
     * @param string $title
     * @param string $content
     * @param int $userId
     * @return PostEntity
     */
    public function create(string $title, string $content, int $userId): PostEntity
    {
        $post = Post::create([
            'title' => $title,
            'content' => $content,
            'user_id' => $userId,
        ]);

        return $post->load(['user', 'likes', 'comments.user'])->toDomainEntity($userId);
    }

    /**
     * Update an existing post (only by owner)
     *
     * @param int $id
     * @param string $title
     * @param string $content
     * @param int $userId
     * @return PostEntity|null
     */
    public function update(int $id, string $title, string $content, int $userId): ?PostEntity
    {
        $post = Post::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$post) {
            return null;
        }

        $post->update([
            'title' => $title,
            'content' => $content,
        ]);

        return $post->load(['user', 'likes', 'comments.user'])->toDomainEntity($userId);
    }

    /**
     * Delete a post (only by owner)
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function delete(int $id, int $userId): bool
    {
        return Post::where('id', $id)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
