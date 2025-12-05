<?php

declare(strict_types=1);

namespace App\Modules\Home;

class HomeController
{
    public function index(): void
    {
        $title = "Accueil – EPortailEmploi";

        ob_start();
        require __DIR__ . '/../../../views/home/index.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../../views/layouts/main.php';
    }
}