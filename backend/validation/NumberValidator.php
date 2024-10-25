<?php

namespace Backend\Validation;

class NumberValidator implements ValidatorInterface {
    private string $errorMessage = '';

    public function validate(string $data): bool {
        if ($data === '') {
            return true;
        }

        if (!is_numeric($data)) {
            $this->errorMessage = "This field must be a number.";
            return false;
        }

        if ((int) $data < 0) {
            $this->errorMessage = "This field must be a positive integer.";
            return false;
        }

        if ((int) $data > 10000) {
            $this->errorMessage = "This field cannot exceed 10,000.";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}
