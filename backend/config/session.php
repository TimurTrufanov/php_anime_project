<?php

session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cookie_samesite' => 'Strict'
]);

require_once __DIR__ . '/../vendor/autoload.php';

use Backend\Models\Database\Database;
use Backend\Models\User;

if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        error_log('Error generating CSRF token: ' . $e->getMessage());
        exit('An error occurred while generating security tokens.');
    }
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    $database = new Database();
    $db = $database->getConnection();

    if ($db === null) {
        error_log('data base error');
        exit('server error');
    }

    $user = new User($db);

    $userData = $user->getUserByRememberToken($token);

    if ($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['avatar'] = $userData['avatar_url'];
        $_SESSION['role_id'] = $userData['role_id'];

        try {
            $newToken = bin2hex(random_bytes(64));
        } catch (Exception $e) {
            error_log('Error generating remember_me token: ' . $e->getMessage());
            exit('An error occurred while processing your request.');
        }

        setcookie('remember_me', $newToken, time() + (86400 * 30), "/", "", true, true);
        $user->storeRememberToken($userData['id'], $newToken);
    } else {
        setcookie('remember_me', '', time() - 3600, '/');
    }
}
