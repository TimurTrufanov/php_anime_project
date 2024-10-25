<?php

namespace Backend\Validation;

use Backend\Models\User;

class RegistrationEmailValidator extends EmailValidator {
    private ?User $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    public function validate(string $data): bool {
        if (!parent::validate($data)) {
            return false;
        }

        if ($this->userModel->emailExists($data)) {
            $this->errorMessage = 'Email is already registered';
            return false;
        }

        return true;
    }
}
