<?php

namespace App\Application\DTOs;

class UpdatePostDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly int $userId
    ) {}

    public static function fromArray(array $data, int $id, int $userId): self
    {
        return new self(
            id: $id,
            title: $data['title'],
            content: $data['content'],
            userId: $userId
        );
    }
}
