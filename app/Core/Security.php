<?php

declare(strict_types=1);

namespace App\Core;

class Security
{
   
    // Génère: token CSRF pour un formulaire cible , Token aléatoire sécurisé lié par clef de formulaire
    public static function generateCsrfToken(string $formKey): string 
    {
        SessionManager::startSession();

        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }

        // Token aléatoire sécurisé
        $token = bin2hex(random_bytes(32));

        // On associe ce token au formulaire
        $_SESSION['csrf_tokens'][$formKey] = $token;

        return $token;
    }

    
    public static function getCsrfToken(string $formKey): ?string  // Récupère le token CSRF pour un formulaire donné si existant
    {
        return $_SESSION['csrf_tokens'][$formKey] ?? null;
    }

    
    public static function verifyCsrfToken(string $formKey, ?string $token): bool // Verification du token CSRF + Suppression après usage
    {
        SessionManager::startSession();

        if (
            empty($token) ||
            !isset($_SESSION['csrf_tokens'][$formKey])
        ) {
            return false;
        }

        $isValid = hash_equals($_SESSION['csrf_tokens'][$formKey], $token);

        // Token one-time pour renforcer la sécurité
        unset($_SESSION['csrf_tokens'][$formKey]);

        return $isValid;
    }
}
