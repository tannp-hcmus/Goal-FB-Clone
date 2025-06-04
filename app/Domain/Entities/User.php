<?php

namespace App\Domain\Entities;

class User
{
    public function __construct(
        private int $id,
        private string $firstname,
        private string $lastname,
        private string $email,
        private string $password,
        private string $gender,
        private \DateTime $birthday,
        private ?string $avatar = null,
        private ?\DateTime $email_verified_at = null,
        private ?\DateTime $created_at = null,
        private ?\DateTime $updated_at = null,
    ) {
        $this->created_at = $created_at ?? new \DateTime();
        $this->updated_at = $updated_at ?? new \DateTime();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getEmailVerifiedAt(): ?\DateTime
    {
        return $this->email_verified_at;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function setBirthday(\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function verifyEmail(): void
    {
        $this->email_verified_at = new \DateTime();
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
