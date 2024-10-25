<?php

namespace Backend\Validation;

use AllowDynamicProperties;

class BirthDateValidator extends DateValidator {
    public function validate(string $data): bool {
        if (!parent::validate($data)) {
            return false;
        }

        list($year, , ) = explode('-', $data);
        $minYear = 1970;
        $maxYear = 2018;
        if ($year < $minYear || $year > $maxYear) {
            $this->errorMessage = "Birth year must be between $minYear and $maxYear.";
            return false;
        }

        return true;
    }
}