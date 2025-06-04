<?php

namespace App\Application\DTOs;

use App\Domain\Entities\User;

class UserDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public string $firstname,
        public string $lastname,
        public \DateTime $birthday,
        public ?string $gender = null,
        public ?string $avatar = null,
        public ?\DateTime $email_verified_at = null,
        public ?\DateTime $created_at = null,
        public ?\DateTime $updated_at = null,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public static function fromUser(User $user): UserDTO
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstname(),
            $user->getLastname(),
            $user->getBirthday(),
            $user->getGender(),
            $user->getAvatar(),
            $user->getEmailVerifiedAt(),
            $user->getCreatedAt(),
            $user->getUpdatedAt()
        );
    }
}
