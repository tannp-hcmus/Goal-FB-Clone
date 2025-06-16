<?php

namespace App\Domain\Interfaces\Services;

use App\Application\DTOs\UpdateUserProfileDTO;
use App\Infrastructure\Models\User;

interface UserProfileServiceInterface
{
    /**
     * Update user profile with data and optional avatar file.
     * Returns an array with the updated user and verification info.
     *
     * @param int $userId
     * @param UpdateUserProfileDTO $dto
     * @param \Illuminate\Http\UploadedFile|null $avatarFile
     * @return array{user: User, email_verification_needed: bool}
     */
    public function updateProfile(int $userId, UpdateUserProfileDTO $dto, $avatarFile = null): array;
}
