<?php

namespace App\Application\DTOs;

class UpdateUserProfileDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $description = null,
        public ?string $avatar = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'firstname' => $this->getFirstName(),
            'lastname' => $this->getLastName(),
            'email' => $this->email,
            'description' => $this->description,
            'avatar' => $this->avatar,
        ];
    }

    private function getFirstName(): string
    {
        $nameParts = explode(' ', $this->name, 2);
        return $nameParts[0];
    }

    private function getLastName(): string
    {
        $nameParts = explode(' ', $this->name, 2);
        return isset($nameParts[1]) ? $nameParts[1] : '';
    }
}
