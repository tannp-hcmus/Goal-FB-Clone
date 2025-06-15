<?php

namespace App\Application\Services;

use App\Application\DTOs\RegisterUserDTO;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Domain\Interfaces\Services\UserServiceInterface;
use Illuminate\Auth\Events\Registered;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function create(RegisterUserDTO $dto): User
    {
        $user = $this->userRepository->create($dto->toArray());
        event(new Registered($user));

        return $user;
    }
}

