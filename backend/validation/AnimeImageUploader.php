<?php

namespace Backend\Validation;

class AnimeImageUploader extends BaseImageUploader
{
    public function upload(array $file, string $uploadDir): false|string
    {
        $filename = 'anime_' . uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return '/uploads/media/anime/' . $filename;
        }

        $this->errorMessage = 'Failed to upload anime image.';
        return false;
    }
}