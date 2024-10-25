<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/session.php';

use Backend\Controllers\Admin\AdminController;
use Backend\Controllers\Admin\AnimeController;
use Backend\Controllers\Admin\AnimeStatusController;
use Backend\Controllers\Admin\AnimeViewStatusController;
use Backend\Controllers\Admin\AuthorController;
use Backend\Controllers\Admin\DirectorController;
use Backend\Controllers\Admin\MangaController;
use Backend\Controllers\Admin\MangaReadStatusController;
use Backend\Controllers\Admin\MangaStatusController;
use Backend\Controllers\Admin\UserController;
use Backend\Controllers\Admin\WriterController;
use Backend\Controllers\Admin\ArtistController;
use Backend\Controllers\User\AuthController;
use Backend\Controllers\User\ProfileController;
use Backend\Controllers\User\HomeController;
use Backend\Controllers\Admin\GenreController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function requireAdmin(): void
{
    if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
        http_response_code(404);
        echo "404 Not Found";
        exit;
    }
}

$routes = [
    '/' => [HomeController::class, 'showHome'],
    '/change-info' => [ProfileController::class, 'showChangeInfo', 'POST' => 'updateProfile'],
    '/login' => [AuthController::class, 'showLoginForm', 'POST' => 'login'],
    '/register' => [AuthController::class, 'showRegistrationForm', 'POST' => 'register'],
    '/change-password' => [ProfileController::class, 'showChangePasswordForm', 'POST' => 'changePassword'],
    '/user-anime-statuses' => [ProfileController::class, 'showUserAnimeStatuses'],
    '/user-manga-statuses' => [ProfileController::class, 'showUserMangaStatuses'],
    '/logout' => [AuthController::class, 'logout'],
    '/admin' => [AdminController::class, 'showAdminPanel', 'requireAdmin' => true],
    '/admin/users' => [UserController::class, 'index', 'requireAdmin' => true],
    '/anime' => [Backend\Controllers\User\AnimeController::class, 'index'],
    '/anime/{id}' => [Backend\Controllers\User\AnimeController::class, 'show'],
    '/anime/rate' => [Backend\Controllers\User\AnimeController::class, 'rate'],
    '/anime/add-comment' => [Backend\Controllers\User\AnimeController::class, 'addComment'],
    '/anime/delete-comment/{id}' => [Backend\Controllers\User\AnimeController::class, 'deleteComment'],
    '/anime/set-view-status' => [Backend\Controllers\User\AnimeController::class, 'setViewStatus'],
    '/anime/toggle-like-comment' => [Backend\Controllers\User\AnimeController::class, 'toggleLikeComment'],
    '/manga' => [Backend\Controllers\User\MangaController::class, 'index'],
    '/manga/{id}' => [Backend\Controllers\User\MangaController::class, 'show'],
    '/manga/rate' => [Backend\Controllers\User\MangaController::class, 'rate'],
    '/manga/add-comment' => [Backend\Controllers\User\MangaController::class, 'addComment'],
    '/manga/delete-comment/{id}' => [Backend\Controllers\User\MangaController::class, 'deleteComment'],
    '/manga/set-read-status' => [Backend\Controllers\User\MangaController::class, 'setReadStatus'],
    '/manga/toggle-like-comment' => [Backend\Controllers\User\MangaController::class, 'toggleLikeComment'],
];

$entityRoutes = [
    'genres' => GenreController::class,
    'directors' => DirectorController::class,
    'writers' => WriterController::class,
    'artists' => ArtistController::class,
    'authors' => AuthorController::class,
    'anime-statuses' => AnimeStatusController::class,
    'manga-statuses' => MangaStatusController::class,
    'anime-view-statuses' => AnimeViewStatusController::class,
    'manga-read-statuses' => MangaReadStatusController::class,
    'anime' => AnimeController::class,
    'manga' => MangaController::class,
];

function createEntityRoutes($path, $controllerClass): bool
{
    requireAdmin();
    $controller = new $controllerClass();
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($uri === "/admin/$path" && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller->index();
        return true;
    }

    if ($uri === "/admin/$path/create") {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->create();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->create();
        }
        return true;
    }

    if (preg_match("#^/admin/$path/(\d+)$#", $uri, $matches)) {
        $id = (int)$matches[1];
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->show($id);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update($id);
        }
        return true;
    }

    if (preg_match("#^/admin/$path/(\d+)/edit$#", $uri, $matches)) {
        $id = (int)$matches[1];
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $controller->edit($id);
        }
        return true;
    }

    if (preg_match("#^/admin/$path/(\d+)/delete$#", $uri, $matches)) {
        $id = (int)$matches[1];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->delete($id);
        }
        return true;
    }

    return false;
}

function handleRoute(array $routes, string $uri): bool
{
    $method = $_SERVER['REQUEST_METHOD'];

    foreach ($routes as $path => $routeInfo) {
        $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '(\d+)', $path);

        if (preg_match("#^$pattern$#", $uri, $matches)) {
            if (isset($routeInfo['requireAdmin']) && in_array($method, ['POST', 'GET'])) {
                requireAdmin();
            }

            $controllerClass = $routeInfo[0];
            $controller = new $controllerClass();

            $action = $routeInfo[$method] ?? $routeInfo[1];

            if (count($matches) > 1) {
                $controller->$action($matches[1]);
            } else {
                $controller->$action();
            }
            return true;
        }
    }

    return false;
}


$routeMatched = handleRoute($routes, $uri);

if (!$routeMatched) {
    foreach ($entityRoutes as $path => $controllerClass) {
        if (createEntityRoutes($path, $controllerClass)) {
            $routeMatched = true;
            break;
        }
    }
}

if (!$routeMatched) {
    http_response_code(404);
    echo "404 Not Found";
}
