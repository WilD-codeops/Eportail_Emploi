<?php

namespace App\Modules\Entreprise;

use App\Core\Validator;

/**
 * Valide les données du module Entreprise (Admin + Gestionnaire).
 */
class EntrepriseValidator
{
    /** Validation création entreprise */
    public static function validateCreate(array $data): array
    {
        if (!Validator::validateNotEmpty($data['nom_entreprise'] ?? ''))
            return self::fail("Nom entreprise obligatoire.");

        if (!Validator::validateSecteurId($data['secteur_id'] ?? null))
            return self::fail("Secteur invalide.");

        if (!Validator::validateNotEmpty($data['adresse'] ?? ''))
            return self::fail("Adresse obligatoire.");

        if (!Validator::validatePostalCode($data['code_postal'] ?? ''))
            return self::fail("Code postal invalide.");

        if (!Validator::validateCity($data['ville'] ?? ''))
            return self::fail("Ville invalide.");

        if (!Validator::validateNotEmpty($data['pays'] ?? ''))
            return self::fail("Pays obligatoire.");

        if (!Validator::validateSiret($data['siret'] ?? ''))
            return self::fail("SIRET invalide.");

        if (!empty($data['email_entreprise']) &&
            !Validator::validateEmail($data['email_entreprise']))
            return self::fail("Email entreprise invalide.");

        if (!empty($data['telephone']) &&
            !Validator::validatePhone($data['telephone']))
            return self::fail("Téléphone invalide.");

        if (!Validator::validateDescription($data['description'] ?? null))
            return self::fail("Description trop longue.");

        return ['success' => true];
    }

    /** Validation des infos du gestionnaire lié à l'entreprise */
    public static function validateGestionnaire(array $data): array
    {
        if (!Validator::validateCity($data['prenom'] ?? ''))
            return self::fail("Prénom invalide.");

        if (!Validator::validateCity($data['nom'] ?? ''))
            return self::fail("Nom invalide.");

        if (!Validator::validateEmail($data['email'] ?? ''))
            return self::fail("Email invalide.");

        if (!Validator::validatePassword($data['password'] ?? '', $data['password_confirm'] ?? ''))
            return self::fail("Mot de passe incorrect.");

        return ['success' => true];
    }

    private static function fail(string $msg): array
    {
        return ['success' => false, 'error' => $msg];
    }
}