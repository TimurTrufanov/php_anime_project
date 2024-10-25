<?php

namespace Backend\Controllers\Admin;

use Backend\Controllers\BaseController;
use Backend\Models\Database\Database;
use Backend\Models\User;

class UserController extends BaseController
{
    private ?\PDO $db;
    private User $userModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
    }

    public function index(): void
    {
        $users = $this->userModel->getAllUsers();
        $data = ['users' => $users, 'title' => 'Users List'];
        $this->render('admin/user/index', $data, 'admin');
    }
}
