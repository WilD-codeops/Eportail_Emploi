<?php

namespace App\Modules\Offres;

use App\Core\Validator;

/**
 * Validation des données d'offre (sans accès DB).
 * Note : aucun contrôle sur entreprise_id / auteur_id ici
 * (restriction entreprise fait côté Service/Repository).
 */
class OffresValidator
{
    /**
     * Valide et normalise les données d'une offre.
     */
    public function validate(array $data, bool $isAdmin = false): array
    {
        $errors = [];

        // Nettoyage / normalisation
        $clean = [
            'titre'                   => Validator::sanitize($data['titre'] ?? ''),
            'description'             => Validator::sanitize($data['description'] ?? ''),
            'type_offre_id'           => (int)($data['type_offre_id'] ?? 0),
            'niveau_qualification_id' => (int)($data['niveau_qualification_id'] ?? 0),
            'domaine_emploi_id'       => (int)($data['domaine_emploi_id'] ?? 0),
            'localisation_id'         => (int)($data['localisation_id'] ?? 0),
            'date_debut'              => trim($data['date_debut'] ?? '') ?: null,
            'date_fin'                => trim($data['date_fin'] ?? '') ?: null,
            'duree_contrat'           => isset($data['duree_contrat']) && $data['duree_contrat'] !== '' ? (int)$data['duree_contrat'] : null,
            'salaire'                 => isset($data['salaire']) && $data['salaire'] !== '' ? $data['salaire'] : null,
            'statut'                  => trim($data['statut'] ?? '') ?: 'active',
        ];

        // Titre
        if (!Validator::validateNotEmpty($clean['titre'])) {
            $errors['titre'] = "Le titre est requis.";
        } elseif (strlen($clean['titre']) > 150) {
            $errors['titre'] = "Le titre doit faire 150 caractères maximum.";
        }

        // Description optionnelle
        if ($clean['description'] !== '') {
            if (strlen($clean['description']) < 10) {
                $errors['description'] = "La description doit contenir au moins 10 caractères.";
            } elseif (!Validator::validateDescription($clean['description'])) {
                $errors['description'] = "Description trop longue.";
            }
        }

        // IDs obligatoires
        $idFields = [
            'type_offre_id'           => "Type d'offre requis.",
            'niveau_qualification_id' => "Niveau de qualification requis.",
            'domaine_emploi_id'       => "Domaine d'emploi requis.",
            'localisation_id'         => "Localisation requise."
        ];

        foreach ($idFields as $field => $message) {
            if (!Validator::validateSecteurId($clean[$field])) {
                $errors[$field] = $message;
            }
        }

        // Dates : format simple YYYY-MM-DD
        $datePattern = '/^\d{4}-\d{2}-\d{2}$/';

        if ($clean['date_fin'] && !$clean['date_debut']) {
            $errors['date_fin'] = "La date de début est requise si une date de fin est fournie.";
        }

        if ($clean['date_debut'] && !preg_match($datePattern, $clean['date_debut'])) {
            $errors['date_debut'] = "Format de date début invalide (YYYY-MM-DD).";
        }

        if ($clean['date_fin'] && !preg_match($datePattern, $clean['date_fin'])) {
            $errors['date_fin'] = "Format de date fin invalide (YYYY-MM-DD).";
        }

        if ($clean['date_debut'] && $clean['date_fin'] && !$errors) {
            if (strtotime($clean['date_fin']) < strtotime($clean['date_debut'])) {
                $errors['date_fin'] = "La date de fin doit être postérieure ou égale à la date de début.";
            }
        }

        // Durée de contrat
        if ($clean['duree_contrat'] !== null && $clean['duree_contrat'] < 1) {
            $errors['duree_contrat'] = "La durée de contrat doit être supérieure ou égale à 1.";
        }

        // Salaire
        if ($clean['salaire'] !== null) {
            if (!is_numeric($clean['salaire'])) {
                $errors['salaire'] = "Le salaire doit être numérique.";
            } elseif ((float)$clean['salaire'] < 0) {
                $errors['salaire'] = "Le salaire doit être positif.";
            } else {
                $clean['salaire'] = (float)$clean['salaire'];
            }
        }

        // Statut
        $allowedStatus = ['active', 'inactive', 'archive'];
        if (!in_array($clean['statut'], $allowedStatus, true)) {
            $errors['statut'] = "Statut invalide.";
        } elseif (!$isAdmin && $clean['statut'] === 'archive') {
            $errors['statut'] = "Le statut archive est réservé à l'administrateur.";
        }

        return [
            'isValid' => empty($errors),
            'errors'  => $errors,
            'clean'   => $clean
        ];
    }
}
