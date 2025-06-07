<?php

namespace App\Application\Services;

use App\Application\DTOs\UpdateUserProfileDTO;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Services\UserProfileServiceInterface;
use App\Infrastructure\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserProfileService implements UserProfileServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function updateProfile(int $userId, UpdateUserProfileDTO $dto, $avatarFile = null): array
    {
        $user = User::findOrFail($userId);
        $data = $dto->toArray();

        // Remove avatar from data array - we'll handle it separately
        unset($data['avatar']);

        // Handle avatar upload
        if ($avatarFile instanceof UploadedFile) {
            if ($user->avatar) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }

            $avatarPath = $avatarFile->store('avatars', 'public');
            $data['avatar'] = Storage::url($avatarPath);
        }

        // Fill the user model with new data but don't save yet
        $user->fill($data);

        // Check if email changed using Laravel's isDirty method
        $emailVerificationNeeded = $user->isDirty('email');

        // If email changed, mark as unverified
        if ($emailVerificationNeeded) {
            $user->email_verified_at = null;
        }

        // Save the user
        $user->save();

        // Send verification email if needed
        if ($emailVerificationNeeded) {
            $user->sendEmailVerificationNotification();
        }

        return [
            'user' => $user,
            'email_verification_needed' => $emailVerificationNeeded,
        ];
    }
}

