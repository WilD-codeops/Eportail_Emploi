<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use App\Core\Auth;
use App\Core\Database;
    use App\Core\Validator;
    use App\Modules\Auth\AuthRegistrationService;
    use App\Modules\Entreprise\EntrepriseRepository;
    use App\Modules\Entreprise\EntrepriseService;
    use App\Core\Security;

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
                "authVariant" => "login",
                "h1"          => "Connexion à votre compte"
            ]);
        }

        public function login(): void
        {
            Security::requireCsrfToken('login', $_POST['csrf_token'] ?? null);

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
                    header("Location: admin/entreprises?reason=loggedin");
                    break;
                case 'gestionnaire':
                    header("Location: /gestionnaire/dashboard/offres?reason=loggedin");
                    break;
                case 'recruteur':
                    header("Location: /recruteur/dashboard/offres?reason=loggedin");
                    break;
                case 'candidat':
                    header("Location: /?reason=loggedin");
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
                "authVariant" => "register_candidat"
            ]);
        }

        public function registerCandidat(): void
        {             
            Security::requireCsrfToken('register_candidat', $_POST['csrf_token'] ?? null);

            $service = new AuthRegistrationService(
                $this->makeAuthService(),
                $this->makeEntrepriseService()
            );
        
            $result = $service->registerCandidat($_POST);
        
            if (!$result['success']) {
                 $this->renderAuth("register_candidat", [
                    "title"       => "Créer un compte candidat",
                    "authVariant" => "register_candidat",
                    "error"       => $result['error']
                ]);
                return;
            }
            
            self::flashSuccess("Inscription réussie. Vous pouvez maintenant vous connecter.");
            header("Location: /login");
            exit;   
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
            Security::requireCsrfToken('register_entreprise', $_POST['csrf_token'] ?? null);
            
            $service = new AuthRegistrationService(
                $this->makeAuthService(),
                $this->makeEntrepriseService()
            );
        
            $result = $service->registerEntreprise($_POST);
        
            if (!$result['success']) {
                $entrepriseToCreate = $_POST;

                     $this->renderAuth("register_entreprise", [
                        "title"       => "Créer un espace entreprise",
                        "authVariant" => "register_entreprise",
                        "error"       => $result['error'],
                        "entreprise" => $entrepriseToCreate,
                    ]);
                    return;
                }
            
                self::flashSuccess($result['message']);
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

        public static function logout(): void
        {
            Auth::logout();
            header("Location: /?reason=logout");
            exit;
        }

        public static function flashSuccess(string $message): void
        {
            $_SESSION['success'] = $message;   
        }

        public static function flashError(string $message): void
        {
            $_SESSION['error'] = $message;   
        }
    }