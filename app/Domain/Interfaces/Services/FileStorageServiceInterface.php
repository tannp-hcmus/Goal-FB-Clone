<?php

namespace App\Domain\Interfaces\Services;

use Illuminate\Http\UploadedFile;

interface FileStorageServiceInterface
{
    /**
     * Store an avatar file and return the public URL.
     *
     * @param UploadedFile $file
     * @return string The public URL of the stored file
     */
    public function storeAvatar(UploadedFile $file): string;

    /**
     * Delete an avatar file by its path.
     *
     * @param string $avatarUrl The public URL of the avatar to delete
     * @return void
     */
    public function deleteAvatar(string $avatarUrl): void;
}
