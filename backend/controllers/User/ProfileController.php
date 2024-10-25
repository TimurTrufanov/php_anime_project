<?php

namespace Backend\Controllers\User;

use Backend\Controllers\BaseController;
use Backend\Models\AnimeUserStatuses;
use Backend\Models\AnimeViewStatuses;
use Backend\Models\MangaReadStatuses;
use Backend\Models\MangaUserStatuses;
use Backend\Models\User;
use Backend\Models\Database\Database;
use Backend\Validation\PasswordConfirmationValidator;
use Backend\Validation\PasswordValidator;
use Backend\Validation\Validator;
use Backend\Validation\UsernameValidator;
use Backend\Validation\BirthDateValidator;
use Backend\Validation\AvatarUploader;
use JetBrains\PhpStorm\NoReturn;

class ProfileController extends BaseController
{
    protected ?\PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function showUserAnimeStatuses(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $user = new User($this->db);
        $currentUser = $user->getUserById($_SESSION['user_id']);

        if (!$currentUser) {
            die('User not found');
        }

        $viewStatusModel = new AnimeUserStatuses($this->db);
        $statuses = $viewStatusModel->getStatusesByUserId($_SESSION['user_id']);
        $organizedStatuses = [];

        foreach ($statuses as $status) {
            $statusName = $status['status_name'];
            $organizedStatuses[$statusName][] = [
                'title' => $status['anime_name'],
                'rating' => $status['rating'] ?? 'not rated',
                'anime_id' => $status['anime_id']
            ];
        }

        $statusModel = new AnimeViewStatuses($this->db);
        $allStatuses = $statusModel->getAll();
        foreach ($allStatuses as $status) {
            if (!isset($organizedStatuses[$status['name']])) {
                $organizedStatuses[$status['name']] = [];
            }
        }

        $data = [
            'currentUser' => $currentUser,
            'statuses' => $organizedStatuses,
            'errors' => $_SESSION['errors'] ?? [],
            'title' => 'Anime Statuses'
        ];

        unset($_SESSION['errors']);
        $this->render('user/anime/animeStatuses', $data);
    }

    public function showUserMangaStatuses(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $user = new User($this->db);
        $currentUser = $user->getUserById($_SESSION['user_id']);

        if (!$currentUser) {
            die('User not found');
        }

        $viewStatusModel = new MangaUserStatuses($this->db);
        $statuses = $viewStatusModel->getStatusesByUserId($_SESSION['user_id']);
        $organizedStatuses = [];

        foreach ($statuses as $status) {
            $statusName = $status['status_name'];
            $organizedStatuses[$statusName][] = [
                'title' => $status['manga_name'],
                'rating' => $status['rating'] ?? 'not rated',
                'manga_id' => $status['manga_id']
            ];
        }

        $statusModel = new MangaReadStatuses($this->db);
        $allStatuses = $statusModel->getAll();
        foreach ($allStatuses as $status) {
            if (!isset($organizedStatuses[$status['name']])) {
                $organizedStatuses[$status['name']] = [];
            }
        }

        $data = [
            'currentUser' => $currentUser,
            'statuses' => $organizedStatuses,
            'errors' => $_SESSION['errors'] ?? [],
            'title' => 'Manga Statuses'
        ];

        unset($_SESSION['errors']);
        $this->render('user/manga/mangaStatuses', $data);
    }

    public function showChangeInfo(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        $currentUser = $user->getUserById($_SESSION['user_id']);

        if (!$currentUser) {
            die('User not found');
        }

        $data = [
            'currentUser' => $currentUser,
            'errors' => $_SESSION['errors'] ?? [],
            'title' => 'Change User Info'
        ];

        unset($_SESSION['errors']);
        $this->render('user/changeInfo', $data);
    }

    public function updateProfile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);

        $currentUser = $user->getUserById($_SESSION['user_id']);
        if (!$currentUser) {
            die('User not found');
        }

        $errors = [];
        $currentAvatar = $currentUser['avatar_url'] ?? '/uploads/avatars/default_avatar.jpg';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('CSRF attack detected!');
            }

            $username = $_POST['username'];
            $birthDate = $_POST['birth_date'];
            $profilePhoto = $_FILES['profile_photo'];

            $validator = new Validator();
            $validator->addValidator('username', new UsernameValidator());
            $validator->addValidator('birth_date', new BirthDateValidator());

            $dataToValidate = [
                'username' => $username,
                'birth_date' => $birthDate,
            ];

            if (!$validator->validate($dataToValidate)) {
                $errors = $validator->getErrors();
            }

            if ($profilePhoto && $profilePhoto['error'] === UPLOAD_ERR_OK) {
                $avatarUploader = new AvatarUploader();

                if (!$avatarUploader->validateFile($profilePhoto)) {
                    $errors['profile_photo'] = $avatarUploader->getErrorMessage();
                } else {
                    $uploadDir = __DIR__ . '/../../uploads/avatars/';

                    if ($currentAvatar && $currentAvatar !== '/uploads/avatars/default_avatar.jpg') {
                        $oldAvatarPath = __DIR__ . '/../../' . $currentAvatar;
                        if (file_exists($oldAvatarPath)) {
                            unlink($oldAvatarPath);
                        }
                    }

                    $currentAvatar = $avatarUploader->upload($profilePhoto, $uploadDir);

                    if (!$currentAvatar) {
                        $errors['profile_photo'] = $avatarUploader->getErrorMessage();
                    }
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: /change-info');
                exit;
            }

            if ($user->updateUser($currentUser['id'], $username, $birthDate, $currentAvatar)) {
                $_SESSION['success_message'] = 'Profile updated successfully!';
                $_SESSION['username'] = $username;
                $_SESSION['birth_date'] = $birthDate;
                $_SESSION['avatar'] = $currentAvatar;
            } else {
                $_SESSION['errors']['general'] = 'Failed to update profile.';
            }

            header('Location: /change-info');
            exit;
        }
    }

    public function showChangePasswordForm(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        $currentUser = $user->getUserById($_SESSION['user_id']);

        $avatar = $currentUser['avatar_url'] ?? '/uploads/avatars/default_avatar.jpg';

        $success = $_SESSION['success'] ?? false;
        $errors = $_SESSION['errors'] ?? [];

        unset($_SESSION['success'], $_SESSION['errors']);

        $data = [
            'title' => 'Change Password',
            'success' => $success,
            'errors' => $errors,
            'avatar' => $avatar
        ];

        $this->render('user/changePassword', $data);
    }

    #[NoReturn] public function changePassword(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);

        $validator = new Validator();
        $validator->addValidator('current_password', new PasswordValidator());
        $validator->addValidator('new_password', new PasswordValidator());
        $validator->addValidator('confirm_password', new PasswordConfirmationValidator($_POST['new_password']));

        $data = [
            'current_password' => $_POST['current_password'],
            'new_password' => $_POST['new_password'],
            'confirm_password' => $_POST['confirm_password'],
        ];

        $errors = [];
        $success = false;

        if ($validator->validate($data)) {
            $currentUser = $user->getUserById($_SESSION['user_id']);

            if (password_verify($data['current_password'], $currentUser['password'])) {
                $hashedPassword = password_hash($data['new_password'], PASSWORD_BCRYPT);
                if ($user->updatePassword($_SESSION['user_id'], $hashedPassword)) {
                    $success = true;
                } else {
                    $errors['general'] = 'Failed to update password. Please try again later.';
                }
            } else {
                $errors['current_password'] = 'Current password is incorrect.';
            }
        } else {
            $errors = $validator->getErrors();
        }

        $_SESSION['success'] = $success;
        $_SESSION['errors'] = $errors;

        header('Location: /change-password');
        exit;
    }
}