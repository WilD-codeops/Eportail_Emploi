<?php

namespace App\Core;

use App\Modules\Auth\AuthController;
use App\Modules\Auth\AuthService;

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
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }
        session_unset();    // Supprime toutes les variables de session

            if(ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(                          // Supprime cookie PHPSESSID navigateur
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }   

            session_destroy();
            header("Location: /?reason=logout");
            exit;
        }
    

        public static function checkSessionExpiration(int $timeoutSeconds = 1800,int $absoluteTimeoutSeconds = 7200):void { // 30 min et 2 heures
            if (!self::isLogged()) {
                return; // Aucun contrôle pour les visiteurs
            }
        
            $currentTime = time();
        
            // Initialisation si nécessaire
            if (!isset($_SESSION['created_at'])) {
                $_SESSION['created_at'] = $currentTime;
            }
            if (!isset($_SESSION['last_activity'])) {
                $_SESSION['last_activity'] = $currentTime;
            }
        
            // Timeout d'inactivité
            if (($currentTime - $_SESSION['last_activity']) > $timeoutSeconds) {
                self::logout();
                exit;
            }
        
            // Timeout absolu
            if (($currentTime - $_SESSION['created_at']) > $absoluteTimeoutSeconds) {
                self::logout();
                exit;
            }
        
            // Mise à jour activité
            $_SESSION['last_activity'] = $currentTime;
        }       
    
        

}