<?php

namespace App\Application\Services;

use App\Domain\Interfaces\Repositories\LikeRepositoryInterface;

class LikeService
{
    public function __construct(
        private readonly LikeRepositoryInterface $likeRepository
    ) {}

    /**
     * Toggle like on a post
     *
     * @param int $postId
     * @param int $userId
     * @return bool True if liked, false if unliked
     */
    public function toggleLike(int $postId, int $userId): bool
    {
        return $this->likeRepository->toggle($postId, $userId);
    }
}
