<?php

namespace Backend\Validation;

class PasswordConfirmationValidator implements ValidatorInterface {
    private string $errorMessage = '';

    private string $password;

    public function __construct(string $password) {
        $this->password = $password;
    }

    public function validate(string $data): bool {
        if ($this->password !== $data) {
            $this->errorMessage = "Passwords do not match!";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}
