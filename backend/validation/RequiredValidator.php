<?php

namespace Backend\Validation;

class RequiredValidator implements ValidatorInterface
{
    private string $errorMessage = 'This field is required.';

    public function validate(string $data): bool
    {
        return !empty(trim($data));
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}