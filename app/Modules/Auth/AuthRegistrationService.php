<?php

namespace App\Modules\Auth;

use App\Core\Auth;
use App\Core\Validator;
use App\Modules\Entreprise\EntrepriseService;
use App\Modules\Entreprise\EntrepriseValidator;
use App\Modules\Auth\AuthRepository ;


class AuthRegistrationService
{
    public function __construct(
        private AuthService $authService,
        private EntrepriseService $entrepriseService,
        private AuthRepository $authRepo
    ) {}


    /* ============================================================
       INSCRIPTION CANDIDAT
    ============================================================ */
    public function registerCandidat(array $data): array
    {
        $dataCanonique = [
            'utilisateur' => [
                'role'           => 'candidat',
                'prenom'         => Validator::sanitize($data['prenom'] ?? ''),
                'nom'            => Validator::sanitize($data['nom'] ?? ''),
                'email'          => strtolower(Validator::sanitize($data['email'] ?? '')),
                'telephone'      => Validator::sanitize($data['telephone'] ?? ''), // optionnel
                'mot_de_passe'   => $data['password'] ?? null,// non sanitizé pour validation   
                'confirmation_mdp' => $data['password_confirm'] ?? null,// non sanitizé pour validation
            ],
            'profil' => [
                'poste_recherche'  => Validator::sanitize($data['poste_recherche'] ?? ''),
                'description'      => Validator::sanitize($data['description'] ?? ''),
                'disponibilite'    => $data['disponibilite'] ?? null,
                'mobilite'         => Validator::sanitize($data['mobilite'] ?? ''),
                'annee_experience' => $data['annee_experience'] ?? null,
                'niveau_etudes'    => Validator::sanitize($data['niveau_etudes'] ?? ''),
                'statut_actuel'    => Validator::sanitize($data['statut_actuel'] ?? ''),
            ]
        ];      

        // VALIDATION
        if (!Validator::validateName($dataCanonique['utilisateur']['prenom'])) {
            return $this->fail("Prénom invalide.");
        }

        if (!Validator::validateName($dataCanonique['utilisateur']['nom'])) {
            return $this->fail("Nom invalide.");
        }

        if (!Validator::validateEmail($dataCanonique['utilisateur']['email'])) {
            return $this->fail("Email invalide.");
        }

        if (!Validator::validatePassword($dataCanonique['utilisateur']['mot_de_passe'], $dataCanonique['utilisateur']['confirmation_mdp'])) {
            return $this->fail("Mot de passe invalide ou non confirmé.");
        }
        // Vérifier email UNIQUE
        if ($this->authService->emailExists($dataCanonique['utilisateur']['email'])) {
            return $this->fail("Cet email est déjà utilisé.");
        }   

         // Préparer les données pour l'inscription
        $candidatData = [
            'role' => $dataCanonique['utilisateur']['role'],
            'prenom' => $dataCanonique['utilisateur']['prenom'],
            'nom' => $dataCanonique['utilisateur']['nom'],
            'telephone' => $dataCanonique['utilisateur']['telephone'],
            'email' => $dataCanonique['utilisateur']['email'],
            'entreprise_id' => null,
            'mot_de_passe' => password_hash($dataCanonique['utilisateur']['mot_de_passe'], PASSWORD_DEFAULT),
         ];

         $candidatProfilData = [
            'poste_recherche'  => $dataCanonique['profil']['poste_recherche'],
            'description'      => $dataCanonique['profil']['description'],
            'disponibilite'    => $dataCanonique['profil']['disponibilite'],
            'mobilite'         => $dataCanonique['profil']['mobilite'],
            'annee_experience' => $dataCanonique['profil']['annee_experience'],
            'niveau_etudes'    => $dataCanonique['profil']['niveau_etudes'],
            'statut_actuel'    => $dataCanonique['profil']['statut_actuel'],
         ];

         $resultat = $this->authService->registerCandidatAvecProfil($candidatData, $candidatProfilData);// SERVICE DE TRANSACTION

            if (!$resultat['success']) {
                return $resultat; //remontée de l'erreur systeme à gerer au niveau du controller
            }

        return $this->success("Inscription candidat réussie"); //


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
                'email'       => strtolower(Validator::sanitize($data['email_entreprise'] ?? $data['email'] ?? null)),
                'siret'       => $data['siret'] ?? null,
                'site_web'    => $data['site_web'] ?? null,
                'taille'      => $data['taille'] ?? $data['taille_entreprise'] ?? null,
                'description' => $data['description'] ?? $data['description_entreprise'] ?? null,
                'logo'        => $data['logo'] ?? $data['logo_entreprise'] ?? null,
 
            ],
            'gestionnaire' => [
                'prenom'   => $data['prenom'] ?? null,
                'nom'      => $data['nom'] ?? $data['nom_gestionnaire'] ?? null,
                'email'    => strtolower(Validator::sanitize($data['email_gestionnaire'] ?? $data['email'] ?? null))    ,
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
        
        $siretCheck = $this->entrepriseService->siretExists($dataCanonique['entreprise']['siret']);
        if (isset($siretCheck['systemError']) && $siretCheck['systemError']) {
            // remonter l’erreur système
            return $siretCheck;
        }
        if (($siretCheck['success'] ?? false) && ($siretCheck['exists'] ?? false)) {
            // le SIRET est déjà utilisé
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

    public function forgotPassword(array $data): array
    {
        $email = Validator::sanitize($data['email'] ?? '');
    
        if (!Validator::validateEmail($email)) {
            return $this->fail("Email invalide.");
        }
    
        $userRes = $this->authRepo->findByEmail($email);
        if ($err = $this->systemError($userRes)) return $err;
    
        $user = $userRes['data'] ?? null;
    
        // Important : ne pas révéler si l’email existe (sécurité)
        $genericMsg = "Si l’email existe, un lien de réinitialisation a été envoyé.";
    
        if (!$user) {
            return $this->success($genericMsg);
        }
    
        $token = bin2hex(random_bytes(32));// 64 caractères
        $tokenHash = hash('sha256', $token);
        //$expiresAt = null; // non utilisé car géré en base de données
    
         
    
        $create = $this->authRepo->createPasswordReset((int)$user['id'], $tokenHash);
        if ($err = $this->systemError($create)) return $err;
    
        // renvoie un lien "debug" (en prod -> email) 
        return [
            'success' => true,
            'message' => $genericMsg,
            'debug_link' => "/password/reset?token=" . $token
        ];
    }
    

    public function resetPassword(array $data): array
    {
        // Récupérer et valider les données
        $token = trim((string)($data['token'] ?? ''));// non sanitizé pour validation
        $password = (string)($data['password'] ?? '');// non sanitizé pour validation
        $confirm  = (string)($data['password_confirm'] ?? '');// non sanitizé pour validation

        if ($token === '' || strlen($token) < 30) {// token trop court pour être valide
            return $this->fail("Lien invalide.");
        }

        if (!Validator::validatePassword($password, $confirm)) {
            return $this->fail("Mot de passe invalide ou non confirmé.");
        }

        $tokenHash = hash('sha256', $token);

        $resetRes = $this->authRepo->findValidPasswordReset($tokenHash);
        if ($err = $this->systemError($resetRes)) return $err;

        $reset = $resetRes['data'] ?? null;
        if (!$reset) {
            return $this->fail("Lien expiré ou déjà utilisé.");
        }

        $userId = (int)$reset['user_id'];
        $resetId = (int)$reset['id'];

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $up = $this->authRepo->updateUserPassword($userId, $hash);
        if ($err = $this->systemError($up)) return $err;

        $used = $this->authRepo->markPasswordResetUsed($resetId);
        if ($err = $this->systemError($used)) return $err;

        return $this->success("Mot de passe mis à jour. Vous pouvez vous connecter.");
    }



    //

    private function fail(string $msg): array
    {
        return ['success' => false, 'error' => $msg];
    }

    
    private function success(string $msg): array
    {
        return ['success' => true,'message'=>$msg];
    }
    private function systemError($result){
        if(!$result['success']){
            return [
                'success' => false,
                'systemError'=>true,
                'error'=>$result['error'] ?? 'Erreur système inconnue',
                'code'=>$result['code']
            ];
        }
    }
}