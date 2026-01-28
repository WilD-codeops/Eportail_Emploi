<?php

namespace App\Modules\Auth;

use App\Core\Validator;
use App\Modules\Entreprise\EntrepriseService;
use App\Modules\Entreprise\EntrepriseValidator;

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
        $password = ($data['password'] ?? '');
        $confirm = $data['password_confirm'] ?? '';

        // VALIDATION
        if (!Validator::validateName($prenom)) {
            return $this->fail("Prénom invalide.");
        }

        if (!Validator::validateName($nom)) {
            return $this->fail("Nom invalide.");
        }

        if (!Validator::validateEmail($email)) {
            return $this->fail("Email invalide.");
        }

        if (!Validator::validatePassword($password, $confirm)) {
            return $this->fail("Mot de passe invalide ou non confirmé.");
        }

        return $this->authService->registerCandidat($candidatData = [
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
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


        // VALIDATION DONNEES ENTREPRISE
        $validEntreprise = EntrepriseValidator::validateEntreprise($entrepriseData);
        if (!$validEntreprise['success']) return $validEntreprise;
       

        // VALIDATION DONNEES GESTIONNAIRE
        $validGestionnaire = EntrepriseValidator::validateGestionnaire($gestionnaireData);
        if (!$validGestionnaire['success']) return $validGestionnaire;


        // Vérifier email gestionnaire UNIQUE
        if ($this->authService->emailExists($data['email'])) {
            return $this->fail("Cet email est déjà utilisé.");
        }
        
        // Vérifier SIRET UNIQUE (entreprise)
        if ($this->entrepriseService->siretExists($data['siret'])) {
            return $this->fail("Ce SIRET est déjà enregistré.");
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
            'logo'        => null,
        ];

        $gestionnaireData = [
            'prenom'        => $data['prenom'],
            'nom'           => $data['nom'],
            'email'         => $data['email'],
            'telephone'     => $data['telephone_gestionnaire'] ?: null,
            'mot_de_passe'  => password_hash($data['password'], PASSWORD_DEFAULT),
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