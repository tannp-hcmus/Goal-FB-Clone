<?php

namespace App\Domain\Interfaces\Services;

use App\Application\DTOs\RegisterUserDTO;
use App\Infrastructure\Models\User;

interface UserServiceInterface
{
    /**
     * Create a new user.
     *
     * @param RegisterUserDTO $dto
     * @return User
     */
    public function create(RegisterUserDTO $dto): User;
}
