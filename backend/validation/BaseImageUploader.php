<?php

namespace Backend\Validation;

abstract class BaseImageUploader implements FileValidatorInterface
{
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png'];
    protected int $maxFileSize = 10 * 1024 * 1024;
    protected string $errorMessage = '';

    abstract public function upload(array $file, string $uploadDir): false|string;

    public function validateFile(array $file): bool {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return true;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errorMessage = 'File upload error.';
            return false;
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $this->allowedExtensions)) {
            $this->errorMessage = 'Unsupported file format. Allowed formats are: jpg, jpeg, png.';
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            $this->errorMessage = 'File size exceeds the maximum limit of 10 MB.';
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}
