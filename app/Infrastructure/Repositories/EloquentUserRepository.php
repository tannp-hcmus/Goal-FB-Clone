<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Application\DTOs\UserDTO;
use App\Domain\Entities\User as EntitiesUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

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

    public function getAll(): Collection
    {
        return $this->userModel->all();
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

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        $updateData = array_filter($data, function($value) {
            return $value !== null;
        });

        $user->update($updateData);

        return $user->fresh();
    }

    public function findModelById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function updateProfile(User $user, array $data): User
    {
        $updateData = array_filter($data, function($value) {
            return $value !== null;
        });

        $user->fill($updateData);
        $user->save();

        return $user->fresh();
    }

    public function chunkAll(int $count, callable $callback): void
    {
        $this->userModel->chunk($count, $callback);
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
