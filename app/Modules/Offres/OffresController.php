<?php

declare(strict_types=1);

namespace App\Modules\Offres;

use App\Core\View;

class OffresController
{
    public function index(): void
    {
        View::render('offres/index', [
            'title' => 'Accueil — Offres | EPortailEmploi',
        ]);
    }
}