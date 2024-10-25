<?php

namespace Backend\Validation;

interface ValidatorInterface {
    public function validate(string $data): bool;
    public function getErrorMessage(): string;
}