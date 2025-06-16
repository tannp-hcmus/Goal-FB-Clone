<?php

namespace App\Application\Services;

use App\Domain\Interfaces\Services\FileStorageServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService implements FileStorageServiceInterface
{
    public function storeAvatar(UploadedFile $file): string
    {
        $avatarPath = $file->store('avatars', 'public');
        return Storage::url($avatarPath);
    }

    public function deleteAvatar(string $avatarUrl): void
    {
        if ($avatarUrl) {
            // Convert public URL back to storage path
            $path = str_replace('/storage/', '', $avatarUrl);
            Storage::disk('public')->delete($path);
        }
    }
}
