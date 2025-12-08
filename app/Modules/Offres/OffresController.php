<?php

declare(strict_types=1);

namespace App\Modules\Offres;

use App\core\Auth;  

class OffresController
{
    public function index(): void
    { 
        Auth::requireRole(['admin']);
        $this->renderOffers('offerspublic_list', [
            'title' => "Nos Offres d'emploi | EPortailEmploi",
        ]);
    }


private function renderOffers(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require __DIR__ . "/../../../views/offres/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }   
}