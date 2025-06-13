<?php

namespace App\Domain\Entities;

class Post
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly int $userId,
        public readonly string $authorName,
        public readonly \DateTime $createdAt,
        public readonly \DateTime $updatedAt,
        public readonly int $likesCount = 0,
        public readonly bool $isLikedByCurrentUser = false,
        public readonly array $comments = []
    ) {}

    public static function create(
        int $id,
        string $title,
        string $content,
        int $userId,
        string $authorName,
        \DateTime $createdAt,
        \DateTime $updatedAt,
        int $likesCount = 0,
        bool $isLikedByCurrentUser = false,
        array $comments = []
    ): self {
        return new self($id, $title, $content, $userId, $authorName, $createdAt, $updatedAt, $likesCount, $isLikedByCurrentUser, $comments);
    }
}
