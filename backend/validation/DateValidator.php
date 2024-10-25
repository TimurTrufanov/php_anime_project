<?php

namespace Backend\Validation;

class DateValidator implements ValidatorInterface {
    protected string $errorMessage = '';

    public function validate(string $data): bool {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $this->errorMessage = "Invalid date format.";
            return false;
        }

        list($year, $month, $day) = explode('-', $data);
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $this->errorMessage = "Invalid date. The date does not exist.";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}