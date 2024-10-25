<?php

namespace Backend\Validation;

interface FileValidatorInterface {
    public function validateFile(array $file): bool;
    public function getErrorMessage(): string;
}