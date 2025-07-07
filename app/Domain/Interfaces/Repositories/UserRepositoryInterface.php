<?php

namespace App\Domain\Interfaces\Repositories;

use App\Application\DTOs\UserDTO;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Collection;

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
     * Get all users.
     *
     * @return Collection<User>
     */
    public function getAll(): Collection;

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

    /**
     * Find a user model by ID for profile operations.
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findModelById(int $id): User;

    /**
     * Update user profile with data.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User;

    /**
     * Process all users in chunks for efficient memory usage.
     *
     * @param int $count
     * @param callable $callback
     * @return void
     */
    public function chunkAll(int $count, callable $callback): void;
}
