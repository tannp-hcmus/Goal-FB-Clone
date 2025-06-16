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
        $this->validateName($name);
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

    private function validateName(string $name): void
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
    }

    private function getFirstName(): string
    {
        $nameParts = $this->parseNameParts();
        return $nameParts['firstname'];
    }

    private function getLastName(): string
    {
        $nameParts = $this->parseNameParts();
        return $nameParts['lastname'];
    }

    private function parseNameParts(): array
    {
        $trimmedName = trim($this->name);
        $nameParts = explode(' ', $trimmedName, 2);

        return [
            'firstname' => $nameParts[0],
            'lastname' => isset($nameParts[1]) ? trim($nameParts[1]) : '',
        ];
    }
}
