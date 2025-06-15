<?php

namespace App\Application\DTOs;

class RegisterUserDTO
{
    public function __construct(
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $gender,
        public readonly string $birthday,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            firstname: $data['firstname'],
            lastname: $data['lastname'],
            gender: $data['gender'],
            birthday: $data['birthday'],
            email: $data['email'],
            password: $data['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
