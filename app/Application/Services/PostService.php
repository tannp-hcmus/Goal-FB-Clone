<?php

namespace App\Application\Services;

use App\Application\DTOs\CreatePostDTO;
use App\Application\DTOs\UpdatePostDTO;
use App\Domain\Entities\Post;
use App\Domain\Interfaces\PostRepositoryInterface;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {}

    /**
     * Get all posts for social feed
     *
     * @param int|null $currentUserId
     * @return Post[]
     */
    public function getAllPosts(?int $currentUserId = null): array
    {
        return $this->postRepository->getAllPosts($currentUserId);
    }

    /**
     * Get posts by a specific user
     *
     * @param int $userId
     * @param int|null $currentUserId
     * @return Post[]
     */
    public function getUserPosts(int $userId, ?int $currentUserId = null): array
    {
        return $this->postRepository->getByUserId($userId, $currentUserId);
    }

    /**
     * Create a new post
     *
     * @param CreatePostDTO $dto
     * @return Post
     */
    public function createPost(CreatePostDTO $dto): Post
    {
        return $this->postRepository->create(
            $dto->title,
            $dto->content,
            $dto->userId
        );
    }

    /**
     * Update an existing post
     *
     * @param UpdatePostDTO $dto
     * @return Post|null
     */
    public function updatePost(UpdatePostDTO $dto): ?Post
    {
        return $this->postRepository->update(
            $dto->id,
            $dto->title,
            $dto->content,
            $dto->userId
        );
    }

    /**
     * Delete a post
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function deletePost(int $id, int $userId): bool
    {
        return $this->postRepository->delete($id, $userId);
    }

    /**
     * Get a specific post for editing
     *
     * @param int $id
     * @param int $userId
     * @return Post|null
     */
    public function getPostForEdit(int $id, int $userId): ?Post
    {
        return $this->postRepository->findByIdAndUserId($id, $userId);
    }

    /**
     * Toggle like on a post
     *
     * @param int $postId
     * @param int $userId
     * @return bool
     */
    public function toggleLike(int $postId, int $userId): bool
    {
        return $this->postRepository->toggleLike($postId, $userId);
    }
}
