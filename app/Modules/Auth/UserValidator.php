<?php

namespace App\Modules\Auth;

use App\Core\Validator;

class UserValidator
{
    /**
     * Validation pour createUser()
     */
    public static function validateCreate(array $dataUsertoValidate): array
    {
       

        // --- VALIDATIONS DES CHAMPS ENTRÉS PAR L'UTILISATEUR ---

        // Prénom
        if (!Validator::validateNotEmpty($dataUsertoValidate['prenom'])) {
            return ['success' => false, 'error' => "Le prénom est obligatoire."];
        }
        if (!Validator::validateName($dataUsertoValidate['prenom'])) {
            return ['success' => false, 'error' => "Le prénom est invalide."];
        }

        // Nom
        if (!Validator::validateNotEmpty($dataUsertoValidate['nom'])) {
            return ['success' => false, 'error' => "Le nom est obligatoire."];
        }
        if (!Validator::validateName($dataUsertoValidate['nom'])) {
            return ['success' => false, 'error' => "Le nom est invalide."];
        }

        // Email
        if (!Validator::validateNotEmpty($dataUsertoValidate['email'])) {
            return ['success' => false, 'error' => "L'email est obligatoire."];
        }
        if (!Validator::validateEmail($dataUsertoValidate['email'])) {
            return ['success' => false, 'error' => "L'email est invalide."];
        }

        //teléphone
        if(!Validator::validateNotEmpty($dataUsertoValidate['telephone'])){
            return ['success' => false, 'error' => "Le téléphone est obligatoire."];
        }
        if(!Validator::validatePhone($dataUsertoValidate['telephone'])){
            return ['success' => false, 'error' => "Le téléphone est invalide."];
        }

        // Mot de passe
        if (!Validator::validatePasswordNotEmpty($dataUsertoValidate['mot_de_passe'])) {
            return ['success' => false, 'error' => "Le mot de passe est obligatoire."];
        }

        if (!Validator::validatePasswordComplex($dataUsertoValidate['mot_de_passe'])) {
            return ['success' => false, 'error' => "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre."];
        }

        // Rôle
        $rolesValides = ['admin', 'gestionnaire', 'recruteur'];
        if (!in_array($dataUsertoValidate['role'], $rolesValides)) {
            return ['success' => false, 'error' => "Le rôle sélectionné est invalide."];
        }

        // Entreprise ID
        
        if (!is_numeric($dataUsertoValidate['entreprise_id']) || (int)$dataUsertoValidate['entreprise_id'] <= 0) {
            return ['success' => false, 'error' => "L\'entreprise sélectionnée est invalide."];
        }

        // Tout est OK
        return ['success' => true, 'data' => $dataUsertoValidate];
    }


    // Validation pour updateUser() quasi identique à createUser() sauf mot de passe optionnel
    public static function validateUpdate(array $data): array
    {
        $canon = [
            'prenom'        => Validator::sanitize($data['prenom'] ?? ''),
            'nom'           => Validator::sanitize($data['nom'] ?? ''),
            'email'         => strtolower(Validator::sanitize($data['email'] ?? '')),
            'mot_de_passe'  => $data['mot_de_passe'] ?? '',
            'role'          => strtolower(Validator::sanitize($data['role'] ?? '')),
            'entreprise_id' => $data['entreprise_id'] ?? null,
        ];

        // Prénom
        if (!Validator::validateNotEmpty($canon['prenom'])) {
            return ['success' => false, 'error' => "Le prénom est obligatoire."];
        }
        if (!Validator::validateName($canon['prenom'])) {
            return ['success' => false, 'error' => "Le prénom est invalide."];
        }

        // Nom
        if (!Validator::validateNotEmpty($canon['nom'])) {
            return ['success' => false, 'error' => "Le nom est obligatoire."];
        }
        if (!Validator::validateName($canon['nom'])) {
            return ['success' => false, 'error' => "Le nom est invalide."];
        }

        //telephone
        if(!Validator::validateNotEmpty($canon['telephone'])){
            return ['success' => false, 'error' => "Le téléphone est obligatoire."];
        }
        if(!Validator::validatePhone($canon['telephone'])){
            return ['success' => false, 'error' => "Le téléphone est invalide."];
        }
        // Email
        if (!Validator::validateNotEmpty($canon['email'])) {
            return ['success' => false, 'error' => "L'email est obligatoire."];
        }
        if (!Validator::validateEmail($canon['email'])) {
            return ['success' => false, 'error' => "L'email est invalide."];
        }

        // Mot de passe (optionnel)
        if ($canon['mot_de_passe'] !== '') {
            if (!Validator::validatePasswordComplex($canon['mot_de_passe'])) {
                return ['success' => false, 'error' => "Le mot de passe doit faire au moins 8 caractères, contenir une majuscule, une minuscule, un chiffre et un caractère spécial."];
            }
        }

        // Rôle
        $rolesValides = ['admin', 'gestionnaire', 'recruteur', 'candidat'];
        if (!in_array($canon['role'], $rolesValides)) {
            return ['success' => false, 'error' => "Le rôle sélectionné est invalide."];
        }

        // Entreprise ID
        if (!is_numeric($canon['entreprise_id']) || (int)$canon['entreprise_id'] <= 0) {
            return ['success' => false, 'error' => "L'entreprise sélectionnée est invalide."];
        }

        return ['success' => true, 'data' => $canon];
    }
}