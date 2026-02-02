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

        private function makeAuthRegistrationService(): AuthRegistrationService
        {
            return new AuthRegistrationService(
                $this->makeAuthService(),
                $this->makeEntrepriseService(),
                $repo=new AuthRepository(Database::getConnection())
            );  
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
            Auth::requireGuest();// Redirige si déjà connecté
            $this->renderAuth("login", [
                "title"       => "Connexion — EPortailEmploi",
                "authVariant" => "login",
                "h1"          => "Connexion à votre compte"
            ]);
        }

        public function login(): void
        {
            Auth::requireGuest();// Redirige si déjà connecté
            Security::requireCsrfToken('login', $_POST['csrf_token'] ?? null);

            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $service = $this->makeAuthService();
            $result  = $service->login($email, $password);
            
            if (!$result['success']) {
                self::VerifyFailSystem($result);
                
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
                    header("Location: /admin/users?reason=loggedin");
                    break;
                case 'gestionnaire':
                    header("Location: /dashboard/equipe?reason=loggedin");
                    break;
                case 'recruteur':
                    header("Location: /dashboard/profil?reason=loggedin");
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

            $service = $this->makeAuthRegistrationService();
        
            $result = $service->registerCandidat($_POST);

            
            if (!$result['success']) {
                if(!empty($result['systemrror'])) {
                    self::VerifyFailSystem($result); //verifie si erreur systeme est set +code erreur
                }

                $candidatToRegister=$_POST;//retour de donnees envoye par l'utilisateur au formulaire

                 $this->renderAuth("register_candidat", [
                    "titre"       => "Créer un compte candidat",
                    "authVariant" => "register_candidat",
                    "error"       => $result['error']?? 'erreur',
                    'candidat'    => $candidatToRegister
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
            auth::requireGuest();// Redirige si déjà connecté
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
            
            $service = $this->makeAuthRegistrationService();
        
            $result = $service->registerEntreprise($_POST);
        
            if (!$result['success']) {
                self::VerifyFailSystem($result);
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
            auth::requireGuest();// Redirige si déjà connecté
            $this->renderAuth("forgot_password", [
                "title"       => "Mot de passe oublié",
                "authVariant" => "forgot"
            ]);
        }

        public function forgotPassword(): void
        {
            Auth::requireGuest();// Redirige si déjà connecté
            Security::requireCsrfToken('forgot_password', $_POST['csrf_token'] ?? null);
    
            $service = $this->makeAuthRegistrationService();
             $result = $service->forgotPassword($_POST);
            
            if (!$result['success']) {
                self::VerifyFailSystem($result);
            
                $this->renderAuth('forgot_password', [
                    'title' => 'Mot de passe oublié',
                    'authVariant' => 'forgot',
                    'error' => $result['error'] ?? "Erreur",
                ]);
                return;
        }
            $debugLink = $result['debug_link'] ?? null; 
    
            $this->renderAuth('forgot_password', [
                'title' => 'Mot de passe oublié',
                'authVariant' => 'forgot',
                'success' => $result['message'] ?? "Si l’email existe, un lien de réinitialisation a été envoyé.",
                'debug_link' => $debugLink,
            ]);
        }

        public function showResetPassword(): void
        {
            Auth::requireGuest();// Redirige si déjà connecté
            $token = $_GET['token'] ?? '';

            $this->renderAuth("reset_password", [
                "title"       => "Réinitialiser le mot de passe",
                "authVariant" => "reset",  
                "token"       => $token
            ]);
        }

        public function resetPassword(): void
        {
            Auth::requireGuest();// Redirige si déjà connecté
            Security::requireCsrfToken('reset_password', $_POST['csrf_token'] ?? null);
    
            $service = $this->makeAuthRegistrationService();
            $result  = $service->resetPassword($_POST);
    
            if (!$result['success']) {
                self::VerifyFailSystem($result);
    
                $token = $_POST['token'] ?? '';
    
                $this->renderAuth('reset_password', [
                    'title' => 'Réinitialiser le mot de passe',
                    'authVariant' => 'reset',
                    'token' => $token,
                    'error' => $result['error'] ?? "Erreur",
                ]);
                return;
            }
    
            self::flashSuccess("Mot de passe mis à jour. Vous pouvez maintenant vous connecter.");
            header("Location: /login");
            exit;
        }


        // --------- LOGOUT ---------

        public static function logout(): void
        {
            Auth::logout();
            header("Location: /?reason=logout");
            exit;
        }

        // HELPERS refactor à venir dans un helper centralisé
        public static function flashSuccess(string $message): void
        {
            $_SESSION['success'] = $message;   
        }

        public static function flashError(string $message): void
        {
            $_SESSION['error'] = $message;   
        }

        public static function flashSystemError(string $message): void
        {
            $_SESSION['systemError'] = $message;   
        }

        public static function VerifyFailSystem($result): void
        {
            if (($result['systemError']??false) && $result['systemError']) {
                    self::flashSystemError($result['error']);
                    header("Location: /500");
                    exit;
                }
            
        }
    }