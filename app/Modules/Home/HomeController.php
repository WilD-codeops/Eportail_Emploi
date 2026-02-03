<?php

declare(strict_types=1);

namespace App\Modules\Home;

use App\Core\Database;
use App\Modules\Offres\OffresRepository;
use App\Modules\Offres\OffresService;
use App\Modules\Offres\OffresValidator;

class HomeController
{
    private function makeOffresService(): OffresService
    {
        $pdo       = Database::getConnection();
        $repo      = new OffresRepository($pdo);
        $validator = new OffresValidator();

        return new OffresService($repo, $validator);
    }
    private function renderStaticPublic(string $view, array $params = []): void
    {
        extract($params);
        
        ob_start();
        require __DIR__ . "/../../../views/public/{$view}.php";
        $content = ob_get_clean();
        
        require __DIR__ . "/../../../views/layouts/main.php";
    }  

    public function mentionsLegales(): void
    {
        $this->renderStaticPublic('mentions_légales',[
            'title' => 'Mentions légales – EPortailEmploi',
            'titre' => 'Mentions légales',
        ]);
    }

    public function contact(): void
    {
        $this->renderStaticPublic('Contact',[
            'title' => 'Contact – EPortailEmploi',
            'titre' => 'Contactez-nous',
        ]);
    }

    public function centreAide(): void
    {
        $this->renderStaticPublic('centre_aide',[
            'title' => 'Centre d’aide – EPortailEmploi',
            'titre' => 'Centre d’aide',
        ]);
    }
    
    public function aPropos(): void
    {
        $this->renderStaticPublic('a_propos',[
            'title' => 'À propos – EPortailEmploi',
            'titre' => 'À propos de EPortailEmploi',
        ]);
    }

    public function index(): void
    {
        $title = "Accueil – EPortailEmploi";

        $service = $this->makeOffresService();
        $latest  = $service->listPublic([], 1, 3);
        $latestOffers = $latest['data']['items'] ?? [];
        $refs = $service->getReferenceData(false)['data'] ?? [];

        ob_start();
        require __DIR__ . '/../../../views/home/index.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../../views/layouts/main.php';
    }



    public function maintenance(): void
    {
        $title = "Maintenance – EPortailEmploi";

        ob_start();
        require __DIR__ . '/../../../views/errors/maintenance.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../../views/layouts/main.php';
    }

    public function error500(): void
    {
        $title = "Erreur 500 – EPortailEmploi";

        ob_start();
        require __DIR__ . '/../../../views/errors/500.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../../views/layouts/main.php';
    }
}