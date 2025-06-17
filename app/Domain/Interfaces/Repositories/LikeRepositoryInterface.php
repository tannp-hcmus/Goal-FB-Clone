<?php

namespace App\Domain\Interfaces\Repositories;

interface LikeRepositoryInterface
{
    /**
     * Toggle like on a post
     *
     * @param int $postId
     * @param int $userId
     * @return bool True if liked, false if unliked
     */
    public function toggle(int $postId, int $userId): bool;
}
