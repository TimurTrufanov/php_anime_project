<?php

namespace Backend\Validation;

class PasswordValidator implements ValidatorInterface {
    private string $errorMessage = '';

    public function validate(string $data): bool {
        if (strlen($data) < 8) {
            $this->errorMessage = "Password must be at least 8 characters long";
            return false;
        }

        if (!preg_match('/[A-Z]/', $data)) {
            $this->errorMessage = "Password must contain at least one uppercase letter";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}