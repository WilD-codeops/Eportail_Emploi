<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database;

// Auth
use App\Modules\Auth\AuthRepository;
use App\Modules\Auth\AuthService;
use App\Modules\Auth\AuthController;

class App
{
    private static ?self $instance = null;

    private \PDO $pdo;

    // Instances controllers
    public AuthController $authController;

    private function __construct()
    {
        $this->pdo = Database::getConnection();

        // AUTH module
        $authRepository = new AuthRepository($this->pdo);
        $authService    = new AuthService($authRepository);
        $this->authController = new AuthController($authService);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function resolve(string $class)
    {
     return match ($class) 
     {
        \App\Modules\Auth\AuthController::class => $this->authController,
        default => new $class(), // fallback si pas de dépendances (HomeController par ex)
        };
    }
}