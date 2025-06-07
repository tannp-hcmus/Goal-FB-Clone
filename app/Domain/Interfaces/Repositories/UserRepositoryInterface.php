<?php

namespace App\Domain\Interfaces\Repositories;

use App\Application\DTOs\UserDTO;
use App\Infrastructure\Models\User;

interface UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return UserDTO|null
     */
    public function findById(int $id): ?UserDTO;

    /**
     * Find a user by their email.
     *
     * @param string $email
     * @return UserDTO|null
     */
    public function findByEmail(string $email): ?UserDTO;

    /**
     * Update a user's profile.
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function update(int $id, array $data): User;
}
