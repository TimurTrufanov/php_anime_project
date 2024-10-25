<?php

namespace Backend\Validation;

class Validator {
    private array $validators = [];
    private array $errors = [];

    public function addValidator(string $field, ValidatorInterface $validator): void {
        $this->validators[$field] = $validator;
    }

    public function validate(array $data): bool {
        $isValid = true;

        foreach ($data as $field => $value) {
            if (empty($value)) {
                $isValid = false;
                $this->errors[$field] = ucfirst($field) . " is required";
                continue;
            }

            if (isset($this->validators[$field])) {
                $validator = $this->validators[$field];
                if (!$validator->validate($value)) {
                    $isValid = false;
                    $this->errors[$field] = $validator->getErrorMessage();
                }
            }
        }

        return $isValid;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
