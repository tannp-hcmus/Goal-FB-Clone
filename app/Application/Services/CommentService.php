<?php

namespace App\Application\Services;

use App\Application\DTOs\CreateCommentDTO;
use App\Domain\Entities\Comment;
use App\Domain\Interfaces\Repositories\CommentRepositoryInterface;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository
    ) {}

    /**
     * Add a comment to a post
     *
     * @param CreateCommentDTO $dto
     * @return Comment
     */
    public function addComment(CreateCommentDTO $dto): Comment
    {
        return $this->commentRepository->create(
            $dto->postId,
            $dto->content,
            $dto->userId,
            $dto->parentCommentId
        );
    }

    /**
     * Delete a comment
     *
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function deleteComment(int $commentId, int $userId): bool
    {
        return $this->commentRepository->delete($commentId, $userId);
    }
}
