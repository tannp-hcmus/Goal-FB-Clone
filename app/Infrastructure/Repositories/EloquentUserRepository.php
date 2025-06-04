<?php

namespace App\Infrastructure\Repositories;

use App\Application\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Application\DTOs\UserDTO;
use App\Domain\Entities\User as EntitiesUser;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private User $userModel
    )
    {}

    public function findById(int $id): ?UserDTO
    {
        $userData = User::find($id);

        if (!$userData) {
            return null;
        }

        return $this->mapToUser($userData);
    }

    public function findByEmail(string $email): ?UserDTO
    {
        $userData = User::where('email', $email)->first();

        if (!$userData) {
            return null;
        }

        return $this->mapToUser($userData);
    }

    public function create(array $data): User
    {
        return $this->userModel->create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'birthday' => $data['birthday'],
            'gender' => $data['gender'],
        ]);
    }

    private function mapToUser($data): UserDTO
    {
        $user = new EntitiesUser(
            $data->id,
            $data->firstname,
            $data->lastname,
            $data->email,
            $data->password,
            $data->gender,
            \DateTime::createFromFormat('Y-m-d', $data->birthday),
            $data->created_at,
            $data->updated_at
        );
        return UserDTO::fromUser($user);
    }
}
