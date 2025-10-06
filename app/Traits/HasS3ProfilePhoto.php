<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasS3ProfilePhoto
{
    /**
     * Update the user's profile photo.
     */
    public function updateProfilePhoto(UploadedFile $photo, string $storageMethod = 'storePublicly'): void
    {
        tap($this->profile_photo_path, function ($previous) use ($photo, $storageMethod) {
            $this->forceFill([
                'profile_photo_path' => $photo->{$storageMethod}(
                    $this->id
                ),
            ])->save();

            if ($previous) {
                Storage::disk('profile-pictures')->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     */
    public function deleteProfilePhoto(): void
    {
        if (! is_null($this->profile_photo_path)) {
            Storage::disk('profile-pictures')->delete($this->profile_photo_path);

            $this->forceFill([
                'profile_photo_path' => null,
            ])->save();
        }
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
                    ? Storage::disk('profile-pictures')->url($this->profile_photo_path)
                    : $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }
}
