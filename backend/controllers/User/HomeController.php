<?php

namespace Backend\Controllers\User;

use Backend\Controllers\BaseController;
use Backend\Traits\AdminTrait;
use Backend\Traits\UserTrait;

class HomeController extends BaseController
{
    use AdminTrait, UserTrait {
        UserTrait::welcome insteadof AdminTrait;
        AdminTrait::welcome as welcomeAdmin;
    }

    public function showHome(): void
    {
        $role_id = $_SESSION['role_id'] ?? null;

        $data = [
            'title' => 'Home Page',
            'welcomeMessage' => $this->welcomeForRole($role_id)
        ];

        $this->render('user/index', $data);
    }

    public function welcomeForRole($role_id): string
    {
        if ($role_id == 2) {
            return $this->welcomeAdmin();
        }

        return $this->welcome();
    }
}
