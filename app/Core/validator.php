<?php

namespace App\Core;

/**
 * Règles de validation réutilisables.
 * Permet un code propre, centralisé et cohérent.
 */
class Validator
{
    /** Nettoyage basique */
    public static function sanitize(string $value): string
    {
        return trim($value);
    }

    /** Champ non vide */
    public static function validateNotEmpty(?string $value): bool
    {
        return isset($value) && trim($value) !== '';
    }

    /** Email valide */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Téléphone FR (0X… ou +33X…) */
    // Doit accepter français
    public static function validatePhone(string $phone): bool {
        return preg_match('/^(0[1-9]\d{8}|\+33[1-9]\d{8})$/', $phone) === 1;
    }


    /** Code postal FR */
    public static function validatePostalCode(string $cp): bool
    {
        return preg_match('/^\d{5}$/', $cp);
    }

    /** Chaîne alphabétique (villes, prénoms…) */
    public static function validateCity(string $city): bool
    {
        return preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\-\s]+$/', $city);
    }

    /** SIRET = 14 chiffres */
    public static function validateSiret(string $siret): bool
    {
        return preg_match('/^\d{14}$/', $siret);
    }

    /** Mot de passe + confirmation */
    public static function validatePassword(string $pass, string $confirm): bool
    {
        return strlen($pass) >= 6 && $pass === $confirm;
    }

    /** ID secteur numérique */
    public static function validateSecteurId($value): bool
    {
        return is_numeric($value) && (int)$value > 0;
    }

    /** Description optionnelle + limite */
    public static function validateDescription(?string $desc): bool
    {
        if (!$desc) return true;
        return strlen($desc) <= 800;
    }
}