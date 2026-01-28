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
        $dataCanonique = [  // structure canonique des données attendue pour validation et evite la trop forte dépendance aux noms des champs du formulaire
            'entreprise' => [
                'nom'         => $data['nom_entreprise'] ?? $data['nom'] ?? null,
                'secteur_id'  => $data['secteur_id'] ?? null,
                'adresse'     => $data['adresse'] ?? null,
                'code_postal' => $data['code_postal'] ?? null,
                'ville'       => $data['ville'] ?? null,
                'pays'        => $data['pays'] ?? null,
                'telephone'   => $data['telephone'] ?? $data['telephone_entreprise'] ?? null,
                'email'       => $data['email_entreprise'] ?? $data['email'] ?? null,
                'siret'       => $data['siret'] ?? null,
                'site_web'    => $data['site_web'] ?? null,
                'taille'      => $data['taille'] ?? $data['taille_entreprise'] ?? null,
                'description' => $data['description'] ?? $data['description_entreprise'] ?? null,
                'logo'        => $data['logo'] ?? $data['logo_entreprise'] ?? null,
 
            ],
            'gestionnaire' => [
                'prenom'   => $data['prenom'] ?? null,
                'nom'      => $data['nom'] ?? null,
                'email'    => $data['email_gestionnaire'] ?? $data['email'] ?? null,
                'telephone'=> $data['telephone_gestionnaire'] ?? $data['telephone'] ?? null,
                'mot_de_passe' => $data['mot_de_passe'] ?? $data['password'] ?? null,
                'confirmation_mdp'  => $data['confirmation_mdp'] ?? $data['password_confirm'] ?? null,
            ]
        ];


        // SANITIZE
        foreach ($dataCanonique['entreprise'] as $k => $v) {
            if (is_string($v)){ 
                $dataCanonique['entreprise'][$k] = Validator::sanitize($v ?? ''); // sanitize tous les champs entreprise
            }
        }

        foreach ($dataCanonique['gestionnaire'] as $k => $v) { 
            if (in_array($k, ["mot_de_passe", "confirmation_mdp"],true)) { // sanitize tous les champs gestionnaire sauf mdp pour qu'il soit vérifié en clair
                continue;
            }
            if (is_string($v)){
                $dataCanonique["gestionnaire"][$k] = Validator::sanitize($v ?? '');
            }
        }


        // VALIDATION DONNEES ENTREPRISE
        $validEntreprise = EntrepriseValidator::validateEntreprise($dataCanonique['entreprise']);
        if (!$validEntreprise['success']) return $validEntreprise;
       

        // VALIDATION DONNEES GESTIONNAIRE
        $validGestionnaire = EntrepriseValidator::validateGestionnaire($dataCanonique['gestionnaire']);
        if (!$validGestionnaire['success']) return $validGestionnaire;


        // Vérifier email gestionnaire UNIQUE
        if ($this->authService->emailExists($dataCanonique['gestionnaire']['email'])) {
            return $this->fail("Cet email est déjà utilisé.");
        }
        
        // Vérifier SIRET UNIQUE (entreprise)
        if ($this->entrepriseService->siretExists($dataCanonique['entreprise']['siret'])) {
            return $this->fail("Ce SIRET est déjà enregistré.");
        }

        //hashage du mdp gestionnaire apres validation
        $dataCanonique['gestionnaire']['mot_de_passe'] = password_hash($dataCanonique['gestionnaire']['mot_de_passe'], PASSWORD_DEFAULT);
        unset($dataCanonique['gestionnaire']['confirmation_mdp']); // inutilisé après validation


        return $this->entrepriseService->createEntrepriseEtGestionnaire(
            $dataCanonique['entreprise'],
            $dataCanonique['gestionnaire']
        );
    }

    private function fail(string $msg): array
    {
        return ['success' => false, 'error' => $msg];
    }
}