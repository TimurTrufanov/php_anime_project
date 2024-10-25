<?php

namespace Backend\Validation;

class UsernameValidator implements ValidatorInterface {
    private string $errorMessage = '';

    public function validate(string $data): bool {
        if (strlen($data) < 5) {
            $this->errorMessage = "Username must be at least 5 characters long";
            return false;
        }

        if (strlen($data) > 255) {
            $this->errorMessage = "Username cannot be longer than 255 characters";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}

