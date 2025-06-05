<?php

namespace App\Domain\Interfaces\Services;

use App\Infrastructure\Models\User;

interface UserServiceInterface
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;
}
