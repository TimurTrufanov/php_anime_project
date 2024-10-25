<?php

namespace Backend\Validation;

class AvatarUploader extends BaseImageUploader
{
    public function upload(array $file, string $uploadDir): false|string
    {
        $filename = 'avatar_' . uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return '/uploads/avatars/' . $filename;
        }

        $this->errorMessage = 'Failed to upload avatar.';
        return false;
    }
}
