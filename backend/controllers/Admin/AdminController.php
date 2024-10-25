<?php
namespace Backend\Controllers\Admin;

use Backend\Controllers\BaseController;

class AdminController extends BaseController
{
    public function showAdminPanel(): void
    {
        $data = [
            'title' => 'Admin Panel',
            'avatar' => $_SESSION['avatar'] ?? '/uploads/avatars/default_avatar.jpg',
        ];

        $this->render('admin/index', $data, 'admin');
    }
}