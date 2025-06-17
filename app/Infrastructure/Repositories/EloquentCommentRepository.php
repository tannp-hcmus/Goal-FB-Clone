<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Comment as CommentEntity;
use App\Domain\Interfaces\Repositories\CommentRepositoryInterface;
use App\Infrastructure\Models\Comment;

class EloquentCommentRepository implements CommentRepositoryInterface
{
    /**
     * Add a comment to a post
     *
     * @param int $postId
     * @param string $content
     * @param int $userId
     * @param int|null $parentCommentId
     * @return CommentEntity
     */
    public function create(int $postId, string $content, int $userId, ?int $parentCommentId = null): CommentEntity
    {
        $comment = Comment::create([
            'content' => $content,
            'post_id' => $postId,
            'user_id' => $userId,
            'parent_comment_id' => $parentCommentId,
        ]);

        return $comment->load('user')->toDomainEntity();
    }

    /**
     * Delete a comment (only by owner)
     *
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function delete(int $commentId, int $userId): bool
    {
        return Comment::where('id', $commentId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
