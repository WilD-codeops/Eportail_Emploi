<?php

declare(strict_types=1);

namespace App\Modules\Auth;
    use App\Core\Database;
    use App\Core\Validator;
    use App\Core\ErrorHandler;
    use App\Modules\Entreprise\EntrepriseRepository;
    use App\Modules\Entreprise\EntrepriseService;

    class AuthController
    {
        // --------- Fabrique des services ---------
        private function makeAuthService(): AuthService
        {
            $pdo  = Database::getConnection();
            $repo = new AuthRepository($pdo);

            return new AuthService($repo, $pdo);
        }

        private function makeEntrepriseService(): EntrepriseService
        {
            $pdo          = Database::getConnection();
            $authService  = $this->makeAuthService();
            $entrepriseRepo = new EntrepriseRepository($pdo);

            return new EntrepriseService($entrepriseRepo, $authService, $pdo);
        }

        // --------- Render layout d'auth ---------

        private function renderAuth(string $view, array $params = []): void
        {
            extract($params);

            ob_start();
            require __DIR__ . "/../../../views/auth/{$view}.php";
            $content = ob_get_clean();

            require __DIR__ . "/../../../views/layouts/auth.php";
        }

        // --------- LOGIN ---------

        public function showLogin(): void
        {
            $this->renderAuth("login", [
                "title"       => "Connexion — EPortailEmploi",
                "authVariant" => "login"
            ]);
        }

        public function login(): void
        {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $service = $this->makeAuthService();
            $result  = $service->login($email, $password);

            if (!$result['success']) {
                $this->renderAuth("login", [
                    "title"       => "Connexion — EPortailEmploi",
                    "authVariant" => "login",
                    "error"       => $result['error'] ?? "Identifiants incorrects"
                ]);
                return;
            }

            // Redirection selon le rôle
            $role = $_SESSION['user_role'] ?? null;

            switch ($role) {
                case 'admin':
                    header("Location: /admin/dashboard");
                    break;
                case 'gestionnaire':
                    header("Location: /gestionnaire/dashboard");
                    break;
                case 'recruteur':
                    header("Location: /recruteur/dashboard");
                    break;
                case 'candidat':
                    header("Location: /candidat/dashboard");
                    break;
                default:
                    header("Location: /");
            }
            exit;
        }

        // --------- REGISTER CANDIDAT ---------

        public function showRegisterCandidat(): void
        {
            $this->renderAuth("register_candidat", [
                "title"       => "Créer un compte candidat",
                "authVariant" => "register"
            ]);
        }

        public function registerCandidat(): void
        {
            $data = [
                'prenom'       => htmlspecialchars($_POST['prenom'] ?? ''),
                'nom'          => htmlspecialchars($_POST['nom'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'mot_de_passe' => $_POST['password'] ?? '',
            ];

            $service = $this->makeAuthService();
            $result  = $service->registerCandidat($data);

            if ($result['success']) {
                header("Location: /login");
                exit;
            }

            $this->renderAuth("register_candidat", [
                "title"       => "Créer un compte candidat",
                "authVariant" => "register",
                "error"       => $result['error'] ?? "Erreur lors de l'inscription"
            ]);
        }

        // --------- REGISTER ENTREPRISE / GESTIONNAIRE ---------

        public function showRegisterEntreprise(): void
        {
            $this->renderAuth("register_entreprise", [
                "title"       => "Créer un espace entreprise",
                "authVariant" => "register_entreprise"
            ]);
        }

        public function showEntrepriseError(string $message): void
        {
            $this->renderAuth("register_entreprise", [
                "title"       => "Créer un espace entreprise",
                "authVariant" => "register_entreprise",
                "error"       => $message
            ]);
        }

        
        public function registerEntreprise(): void
        {
            $data = $_POST;
        
            // Sanitisation minimale
            foreach ($data as $key => $value) {
                $data[$key] = trim($value ?? '');
            }
        
            /* --- Validation entreprise --- */
        
            if (empty($data['nom_entreprise'])) {
                $this->showEntrepriseError("Le nom de l’entreprise est obligatoire.")    ;
                return ;
            }
        
            if (empty($data['secteur_id']) || !is_numeric($data['secteur_id'])) {
                $this->showEntrepriseError("Le secteur d’activité est obligatoire.");
                return ;
            }
        
            if (empty($data['adresse'])) {
                $this->showEntrepriseError("L’adresse est obligatoire.");
                return ;
            }
        
            if (!Validator::validatePostalCode($data['code_postal'])) {
                $this->showEntrepriseError("Code postal invalide.");
                return ;    
            }
        
            if (!Validator::validateCity($data['ville'])) {
                $this->showEntrepriseError("Ville invalide.");
                return ;
            }
        
            if (empty($data['pays'])) {
                $this->showEntrepriseError("Le pays est obligatoire.");
                return ;
            }
        
            if (!Validator::validateSiret($data['siret'])) {
                $this->showEntrepriseError("Le SIRET doit contenir 14 chiffres.");
                return ;
            }
        
            if (!empty($data['telephone']) &&
                !Validator::validatePhone($data['telephone'])) {
                $this->showEntrepriseError("Numéro de téléphone entreprise invalide.");
                return ;
            }
        
            if (!empty($data['email_entreprise']) &&
                !Validator::validateEmail($data['email_entreprise'])) {
                $this->showEntrepriseError("Email entreprise invalide.");
                return ;
            }
        
            /* --- Validation gestionnaire --- */
        
            if (empty($data['prenom']) || !Validator::validateCity($data['prenom'])) {
                $this->showEntrepriseError("Prénom du gestionnaire invalide.");
                return ;
            }
        
            if (empty($data['nom']) || !Validator::validateCity($data['nom'])) {
                $this->showEntrepriseError("Nom du gestionnaire invalide.");
                return ;
            }
        
            if (!Validator::validateEmail($data['email'])) {
                $this->showEntrepriseError("Email du gestionnaire invalide.");
                return ;
            }
        
            if (!empty($data['telephone_gestionnaire']) &&
                !Validator::validatePhone($data['telephone_gestionnaire'])) {
                $this->showEntrepriseError("Téléphone du gestionnaire invalide.");
                return ;
            }
        
            if (!Validator::validatePassword($data['password'], $data['password_confirm'])) {
                $this->showEntrepriseError("Mot de passe invalide ou non confirmé.");
                return ;    
            }
        
    /* --- Préparation des données entreprise --- */

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

    /* --- Préparation gestionnaire --- */

    $gestionnaireData = [
        'prenom'        => $data['prenom'],
        'nom'           => $data['nom'],
        'email'         => $data['email'],
        'mot_de_passe'  => $data['password'], // hashé dans AuthService
        'role'          => 'gestionnaire',
        'entreprise_id' => null
    ];

    /* --- Transaction métier --- */

    $entrepriseService = $this->makeEntrepriseService();

    $result = $entrepriseService->createEntrepriseEtGestionnaire(
        $entrepriseData,
        $gestionnaireData
    );

    if (!$result['success']) {
        $this->showEntrepriseError($result['error']);
        return ;
    }

    header("Location: /login");
    exit;
}

        // --------- MOT DE PASSE OUBLIÉ (vues uniquement pour l’instant) ---------

        public function showForgotPassword(): void
        {
            $this->renderAuth("forgot_password", [
                "title"       => "Mot de passe oublié",
                "authVariant" => "forgot"
            ]);
        }

        public function showResetPassword(): void
        {
            $this->renderAuth("reset_password", [
                "title"       => "Réinitialiser le mot de passe",
                "authVariant" => "reset"
            ]);
        }

        // --------- LOGOUT ---------

        public function logout(): void
        {
            session_destroy();
            header("Location:  /Eportail_Emploi/public/login");
            exit;
        }
    }