<?php

namespace App\Domain\Entities;

class Comment
{
    public function __construct(
        public readonly int $id,
        public readonly string $content,
        public readonly int $userId,
        public readonly string $authorName,
        public readonly int $postId,
        public readonly ?int $parentCommentId,
        public readonly \DateTime $createdAt,
        public readonly \DateTime $updatedAt,
        public readonly array $replies = []
    ) {}

    public static function create(
        int $id,
        string $content,
        int $userId,
        string $authorName,
        int $postId,
        ?int $parentCommentId,
        \DateTime $createdAt,
        \DateTime $updatedAt,
        array $replies = []
    ): self {
        return new self($id, $content, $userId, $authorName, $postId, $parentCommentId, $createdAt, $updatedAt, $replies);
    }
}
