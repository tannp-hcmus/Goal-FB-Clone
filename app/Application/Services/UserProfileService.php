<?php

namespace App\Application\Services;

use App\Application\DTOs\UpdateUserProfileDTO;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Services\UserProfileServiceInterface;
use App\Domain\Interfaces\Services\FileStorageServiceInterface;
use App\Infrastructure\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UserProfileService implements UserProfileServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private FileStorageServiceInterface $fileStorageService,
    ) {}

    public function updateProfile(int $userId, UpdateUserProfileDTO $dto, $avatarFile = null): array
    {
        return DB::transaction(function () use ($userId, $dto, $avatarFile) {
            $user = $this->userRepository->findModelById($userId);
            $data = $dto->toArray();

            unset($data['avatar']);

            if ($avatarFile instanceof UploadedFile) {
                if ($user->avatar) {
                    $this->fileStorageService->deleteAvatar($user->avatar);
                }

                // Store new avatar
                $data['avatar'] = $this->fileStorageService->storeAvatar($avatarFile);
            }

            $user->fill($data);

            $emailVerificationNeeded = $user->isDirty('email');

            if ($emailVerificationNeeded) {
                $user->email_verified_at = null;
            }

            $updatedUser = $this->userRepository->updateProfile($user, $data);

            if ($emailVerificationNeeded) {
                $updatedUser->sendEmailVerificationNotification();
            }

            return [
                'user' => $updatedUser,
                'email_verification_needed' => $emailVerificationNeeded,
            ];
        });
    }
}

