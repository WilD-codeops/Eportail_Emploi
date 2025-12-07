<?php

namespace App\Modules\Auth;

use App\Core\Validator;
use App\Modules\Entreprise\EntrepriseService;

class AuthRegistrationService
{
    public function __construct(
        private AuthService $authService,
        private EntrepriseService $entrepriseService
    ) {}

    /* ============================================================
       INSCRIPTION CANDIDAT
    ============================================================ */
    public function registerCandidat(array $data): array
    {
        $prenom = Validator::sanitize($data['prenom'] ?? '');
        $nom = Validator::sanitize($data['nom'] ?? '');
        $email = Validator::sanitize($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirm = $data['password_confirm'] ?? '';

        // VALIDATION
        if (!Validator::validateCity($prenom)) {
            return $this->fail("Prénom invalide.");
        }

        if (!Validator::validateCity($nom)) {
            return $this->fail("Nom invalide.");
        }

        if (!Validator::validateEmail($email)) {
            return $this->fail("Email invalide.");
        }

        if (!Validator::validatePassword($password, $confirm)) {
            return $this->fail("Mot de passe invalide ou non confirmé.");
        }

        return $this->authService->registerCandidat([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe' => $password
        ]);
    }


    /* ============================================================
       INSCRIPTION ENTREPRISE + GESTIONNAIRE
    ============================================================ */

    public function registerEntreprise(array $data): array
    {
        foreach ($data as $k => $v) {
            $data[$k] = Validator::sanitize($v ?? '');
        }

        /* ====== ENTREPRISE ====== */

        if (empty($data['nom_entreprise'])) {
            return $this->fail("Le nom de l’entreprise est obligatoire.");
        }

        if (!is_numeric($data['secteur_id'])) {
            return $this->fail("Le secteur est obligatoire.");
        }

        if (empty($data['adresse'])) {
            return $this->fail("L’adresse est obligatoire.");
        }

        if (!Validator::validatePostalCode($data['code_postal'])) {
            return $this->fail("Code postal invalide.");
        }

        if (!Validator::validateCity($data['ville'])) {
            return $this->fail("Ville invalide.");
        }

        if (empty($data['pays'])) {
            return $this->fail("Le pays est obligatoire.");
        }

        if (!Validator::validateSiret($data['siret'])) {
            return $this->fail("SIRET invalide (14 chiffres).");
        }

        if (!empty($data['telephone']) &&
            !Validator::validatePhone($data['telephone'])) {
            return $this->fail("Téléphone entreprise invalide.");
        }

        if (!empty($data['email_entreprise']) &&
            !Validator::validateEmail($data['email_entreprise'])) {
            return $this->fail("Email entreprise invalide.");
        }

        /* ====== GESTIONNAIRE ====== */

        if (!Validator::validateCity($data['prenom'])) {
            return $this->fail("Prénom gestionnaire invalide.");
        }

        if (!Validator::validateCity($data['nom'])) {
            return $this->fail("Nom gestionnaire invalide.");
        }

        if (!Validator::validateEmail($data['email'])) {
            return $this->fail("Email gestionnaire invalide.");
        }

        if (!Validator::validatePassword($data['password'], $data['password_confirm'])) {
            return $this->fail("Mot de passe invalide ou non confirmé.");
        }

        /* ====== Construire les données ====== */

        $entrepriseData = [
            'nom'         => $data['nom_entreprise'],
            'secteur_id'  => (int)$data['secteur_id'],
            'adresse'     => $data['adresse'],
            'code_postal' => $data['code_postal'],
            'ville'       => $data['ville'],
            'pays'        => $data['pays'],
            'telephone'   => $data['telephone'] ?: null,
            'email'       => $data['email_entreprise'] ?: null,
            'siret'       => $data['siret'],
            'site_web'    => $data['site_web'] ?: null,
            'taille'      => $data['taille'] ?: null,
            'description' => $data['description'] ?: null,
            'logo'        => null
        ];

        $gestionnaireData = [
            'prenom'        => $data['prenom'],
            'nom'           => $data['nom'],
            'email'         => $data['email'],
            'mot_de_passe'  => $data['password'], // hashé plus tard
            'role'          => 'gestionnaire',
        ];

        return $this->entrepriseService->createEntrepriseEtGestionnaire(
            $entrepriseData,
            $gestionnaireData
        );
    }

    private function fail(string $msg): array
    {
        return ['success' => false, 'error' => $msg];
    }
}