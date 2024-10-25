<?php

namespace Backend\Validation;

class EmailValidator implements ValidatorInterface {
    protected string $errorMessage = '';

    public function validate(string $data): bool {
        if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessage = 'Invalid email format';
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}
