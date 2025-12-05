<?php

namespace App\Core;

class View
{
    public static function render(string $path, array $data = []): void
    {
        extract($data);

        ob_start();
        include __DIR__ . '/../../views/' . $path . '.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../views/layouts/main.php';
    }
} 