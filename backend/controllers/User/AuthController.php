<?php

namespace Backend\Controllers\User;

use Backend\Controllers\BaseController;
use Backend\Models\User;
use Backend\Models\Database\Database;
use Backend\Validation\Validator;
use Backend\Validation\EmailValidator;
use Backend\Validation\PasswordValidator;
use Backend\Validation\RegistrationEmailValidator;
use Backend\Validation\UsernameValidator;
use Backend\Validation\PasswordConfirmationValidator;
use Backend\Validation\BirthDateValidator;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class AuthController extends BaseController
{
    public function showLoginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $data = [
            'title' => 'Login',
            'errors' => $_SESSION['errors'] ?? []
        ];

        unset($_SESSION['errors']);
        $this->render('user/login', $data);
    }

    public function login(): void
    {
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        $errors = [];
        $validator = new Validator();

        $validator->addValidator('email', new EmailValidator());
        $validator->addValidator('password', new PasswordValidator());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken();

            $data = [
                'email' => $_POST['email'],
                'password' => $_POST['password'],
            ];

            if ($validator->validate($data)) {
                if ($user->emailExists($data['email'])) {
                    if (password_verify($data['password'], $user->getPasswordByEmail($data['email']))) {
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $user->getIdByEmail($data['email']);
                        $_SESSION['username'] = $user->getUsernameByEmail($data['email']);
                        $_SESSION['avatar'] = $user->getAvatarByEmail($data['email']);
                        $_SESSION['role_id'] = $user->getRoleIdByEmail($data['email']);

                        if (isset($_POST['remember_me'])) {
                            try {
                                $token = bin2hex(random_bytes(64));
                                setcookie('remember_me', $token, time() + (86400 * 30), "/", "", true, true);
                                $user->storeRememberToken($user->getIdByEmail($data['email']), $token);
                            } catch (Exception $e) {
                                error_log('Error generating remember_me token: ' . $e->getMessage());
                                $_SESSION['errors']['token'] = 'An error occurred while setting the remember me token.';
                            }
                        }

                        header('Location: /');
                        exit;
                    } else {
                        $errors['password'] = 'Invalid password';
                    }
                } else {
                    $errors['email'] = 'Email not found';
                }
            } else {
                $errors = $validator->getErrors();
            }

            $_SESSION['errors'] = $errors;
            header('Location: /login');
            exit;
        }
    }

    public function showRegistrationForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $data = [
            'title' => 'Register',
            'errors' => $_SESSION['errors'] ?? [],
            'formData' => $_SESSION['form_data'] ?? []
        ];

        unset($_SESSION['errors'], $_SESSION['form_data']);
        $this->render('user/register', $data);
    }

    public function register(): void
    {
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        $errors = [];
        $validator = new Validator();

        $validator->addValidator('email', new RegistrationEmailValidator($user));
        $validator->addValidator('username', new UsernameValidator());
        $validator->addValidator('password', new PasswordValidator());
        $validator->addValidator('birth_date', new BirthDateValidator());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken();

            $data = [
                'email' => $_POST['email'],
                'username' => $_POST['username'],
                'birth_date' => $_POST['birth_date'],
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
            ];

            if ($validator->validate($data)) {
                $passwordConfirmationValidator = new PasswordConfirmationValidator($data['password']);

                if (!$passwordConfirmationValidator->validate($data['confirm_password'])) {
                    $errors['confirm_password'] = $passwordConfirmationValidator->getErrorMessage();
                } else {
                    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

                    $user->username = $data['username'];
                    $user->email = $data['email'];
                    $user->birth_date = $data['birth_date'];
                    $user->password = $hashedPassword;
                    $user->role_id = 1;
                    $user->avatar_url = '/uploads/avatars/default_avatar.jpg';

                    if ($user->register()) {
                        $_SESSION['user_id'] = $db->lastInsertId();
                        $_SESSION['username'] = $data['username'];
                        $_SESSION['email'] = $data['email'];

                        header('Location: /');
                        exit;
                    } else {
                        $errors['general'] = "Error during registration!";
                    }
                }
            } else {
                $errors = $validator->getErrors();
            }

            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header('Location: /register');
            exit;
        }
    }

    #[NoReturn] public function logout(): void
    {
        session_unset();
        session_destroy();

        if (isset($_COOKIE['remember_me'])) {
            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);

            $user->deleteRememberToken($_COOKIE['remember_me']);
            setcookie('remember_me', '', time() - 3600, '/');
        }

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        header('Location: /');
        exit;
    }

    private function checkCsrfToken(): void
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF attack detected!');
        }
    }
}
