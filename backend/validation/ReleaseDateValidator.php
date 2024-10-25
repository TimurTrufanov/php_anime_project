<?php

namespace Backend\Validation;

use AllowDynamicProperties;

class ReleaseDateValidator extends DateValidator {
    public function validate(string $data): bool {
        if (empty($data)) {
            return true;
        }

        if (!parent::validate($data)) {
            return false;
        }

        list($year, , ) = explode('-', $data);
        $minYear = 1900;
        $maxYear = 2050;
        if ($year < $minYear || $year > $maxYear) {
            $this->errorMessage = "Release date must be between $minYear and $maxYear year.";
            return false;
        }

        return true;
    }
}