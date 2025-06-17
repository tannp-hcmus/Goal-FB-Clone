<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Comment;

interface CommentRepositoryInterface
{
    /**
     * Add a comment to a post
     *
     * @param int $postId
     * @param string $content
     * @param int $userId
     * @param int|null $parentCommentId
     * @return Comment
     */
    public function create(int $postId, string $content, int $userId, ?int $parentCommentId = null): Comment;

    /**
     * Delete a comment (only by owner)
     *
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function delete(int $commentId, int $userId): bool;
}
