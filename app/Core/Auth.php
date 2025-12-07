<?php

namespace App\Core;

class Auth
{
    public static function isLogged(): bool
    {
        return 
        isset($_SESSION['user_id']);
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
            header("Location: /login");
            exit;
        }
    }


    //Acces restreint à certains rôles
    public static function requireRole(array $roles): void
    {
        if (!self::isLogged() || !in_array(self::role(), $roles)) {
            http_response_code(403);
            die("Accès interdit");
        }
    }
}