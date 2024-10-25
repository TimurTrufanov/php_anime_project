<?php

namespace Backend\Validation;

class NameValidator
{
    public function validateName(string $name): array
    {
        $errors = [];

        $requiredValidator = new RequiredValidator();
        $maxLengthValidator = new MaxLengthValidator(255);

        if (!$requiredValidator->validate($name)) {
            $errors['name'] = $requiredValidator->getErrorMessage();
        }

        if (!$maxLengthValidator->validate($name)) {
            $errors['name'] = $maxLengthValidator->getErrorMessage();
        }

        return $errors;
    }
}