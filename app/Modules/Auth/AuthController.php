<?php

declare(strict_types=1);

namespace App\Modules\Auth;
    use App\Core\Database;
    use App\Core\Validator;
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
            // Gestionnaire
            $gestionnaireData = [
                'prenom'        => htmlspecialchars($_POST['prenom'] ?? ''),
                'nom'           => htmlspecialchars($_POST['nom'] ?? ''),
                'email'         => trim($_POST['email'] ?? ''),
                'mot_de_passe'  => $_POST['password'] ?? '',
                'role'          => 'gestionnaire',
                'entreprise_id' => null,
            ];

            // Entreprise
            $entrepriseData = [
                'nom'         => htmlspecialchars($_POST['nom_entreprise'] ?? ''),
                'secteur_id'  => (int)($_POST['secteur_id'] ?? 0),
                'adresse'     => htmlspecialchars($_POST['adresse'] ?? ''),
                'code_postal' => htmlspecialchars($_POST['code_postal'] ?? ''),
                'ville'       => htmlspecialchars($_POST['ville'] ?? ''),
                'pays'        => htmlspecialchars($_POST['pays'] ?? ''),
                'telephone'   => htmlspecialchars($_POST['telephone'] ?? ''),
                'email'       => trim($_POST['email_entreprise'] ?? ''),
                'siret'       => trim($_POST['siret'] ?? ''),
                'site_web'    => htmlspecialchars($_POST['site_web'] ?? ''),
                'taille'      => htmlspecialchars($_POST['taille'] ?? ''),
                'description' => htmlspecialchars($_POST['description'] ?? ''),
                'logo'        => null,
            ];

            $entrepriseService = $this->makeEntrepriseService();

            $result = $entrepriseService->createEntrepriseEtGestionnaire(
                $entrepriseData,
                $gestionnaireData
            );

            if ($result['success']) {
                header("Location: /login");
                exit;
            }

            $this->renderAuth("register_entreprise", [
                "title"       => "Créer un espace entreprise",
                "authVariant" => "register",
                "error"       => $result['error'] ?? "Erreur lors de l'inscription entreprise"
            ]);
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