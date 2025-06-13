<?php

namespace App\Application\DTOs;

class CreateCommentDTO
{
    public function __construct(
        public readonly string $content,
        public readonly int $postId,
        public readonly int $userId,
        public readonly ?int $parentCommentId = null
    ) {}

    public static function fromArray(array $data, int $postId, int $userId, ?int $parentCommentId = null): self
    {
        return new self(
            content: $data['content'],
            postId: $postId,
            userId: $userId,
            parentCommentId: $parentCommentId
        );
    }
}
