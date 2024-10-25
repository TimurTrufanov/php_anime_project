<?php

namespace Backend\Validation;

use Backend\Models\BaseModel;

class SelectValidator implements ValidatorInterface {
    private string $errorMessage = '';
    private BaseModel $model;

    public function __construct(BaseModel $model) {
        $this->model = $model;
    }

    public function validate(string $data): bool {
        if ($data === '') {
            return true;
        }

        $existingValues = array_column($this->model->getAll(), 'id');
        if (!in_array($data, $existingValues)) {
            $this->errorMessage = "Selected value is invalid.";
            return false;
        }
        return true;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}
