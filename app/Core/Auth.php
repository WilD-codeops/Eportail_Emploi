<?php

namespace App\Core;


use App\Core\SessionManager;

class Auth
{
    public static function isLogged(): bool
    {
        return 
        isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }

    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public static function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::isLogged()) {
            header("Location: /login?reason=unauthenticated");
            exit;
        }
    }


    //Acces restreint à certains rôles
    public static function requireRole(array $roles): void
    {
        if (!self::isLogged()) {
            self::redirectToLogin();
        }

        $hasRequiredRole = in_array(
            strtolower(self::role() ?? ''), 
            array_map('strtolower', $roles)
        );

        if (!$hasRequiredRole) {
            self::forbidden();
        }
    }

    private static function redirectToLogin(): void
    {
        header('Location: /login?reason=unauthenticated');
        exit;
    }

    private static function forbidden(): void
    {
        http_response_code(403);
        require __DIR__ . '/../../views/errors/403.php';
        exit;
    }


    public static function logout(): void
    {
        SessionManager::sessionDestroy();
        }
    

               
    
     
    

}