<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\Repositories\LikeRepositoryInterface;
use App\Infrastructure\Models\Like;

class EloquentLikeRepository implements LikeRepositoryInterface
{
    /**
     * Toggle like on a post
     *
     * @param int $postId
     * @param int $userId
     * @return bool True if liked, false if unliked
     */
    public function toggle(int $postId, int $userId): bool
    {
        $like = Like::where('post_id', $postId)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            return false; // unliked
        } else {
            Like::create([
                'post_id' => $postId,
                'user_id' => $userId,
            ]);
            return true; // liked
        }
    }
}
