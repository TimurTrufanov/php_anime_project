<?php

namespace Backend\Validation;

class MaxLengthValidator implements ValidatorInterface
{
    private int $maxLength;
    private string $errorMessage;

    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
        $this->errorMessage = "This field cannot exceed {$maxLength} characters.";
    }

    public function validate(string $data): bool
    {
        return strlen($data) <= $this->maxLength;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}