<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database;

// Auth
use App\Modules\Auth\AuthRepository;
use App\Modules\Auth\AuthService;
use App\Modules\Auth\AuthController;

// Entreprise
use App\Modules\Entreprise\EntrepriseRepository;
use App\Modules\Entreprise\EntrepriseService;
use App\Modules\Entreprise\EntrepriseController;


class App
{
    private static ?self $instance = null;
    private \PDO $pdo;
    //Singleton et connexion PDO partagée


    // Controllers à partager
    public AuthController $authController;
    public EntrepriseController $entrepriseController;

    private function __construct()
    {
        $this->pdo = Database::getConnection();

        // Module Auth
        $authRepository = new AuthRepository($this->pdo);
        $authService    = new AuthService($authRepository, $this->pdo);
        $this->authController = new AuthController($authService);

        // Module Entreprise
        $entrepriseRepository = new EntrepriseRepository($this->pdo);
        // besoin d'AuthService pour créer un gestionnaire lors de l'inscription
        $entrepriseService = new EntrepriseService(
            $entrepriseRepository,
            $authService,
            $this->pdo
        );

        $this->entrepriseController = new EntrepriseController($entrepriseService);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function resolve(string $class)
    {
        return match ($class) {
            \App\Modules\Auth\AuthController::class => $this->authController,
            \App\Modules\Entreprise\EntrepriseController::class => $this->entrepriseController,
            default => new $class(), // fallback si pas de dépendances (HomeController par ex)
        };
    }
}