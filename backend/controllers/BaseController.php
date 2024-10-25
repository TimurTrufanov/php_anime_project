<?php

namespace Backend\Controllers;

class BaseController
{
    protected function preRender(): array
    {
        return [
            'avatar' => $_SESSION['avatar'] ?? '/uploads/avatars/default_avatar.jpg',
        ];
    }

    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $commonData = $this->preRender();
        $data = array_merge($commonData, $data);

        extract($data);
        ob_start();
        include __DIR__ . "/../views/$view.php";
        $content = ob_get_clean();
        include __DIR__ . "/../views/layouts/$layout.php";
    }
}

