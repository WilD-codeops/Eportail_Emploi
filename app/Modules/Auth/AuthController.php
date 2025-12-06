<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use App\Core\ControllerBase; // si tu fais une base, sinon pas grave

/**
 * Contrôleur d'authentification.
 * Rôles :
 *  - Afficher les vues (login / register / forgot / reset)
 *  - Récupérer les données envoyées par formulaires
 *  - Appeler l’AuthService pour la logique métier
 *  - Gérer les redirections
 */
class AuthController
{
    public function __construct(
        private AuthService $service
    ) {}

    // ---------------------------------------------
    //  MÉTHODE UTILITAIRE : RENDER DU LAYOUT AUTH
    // ---------------------------------------------
    private function renderAuth(string $view, array $params = []): void
    {
        // Ex : $title, $authVariant...
        extract($params);

        // 1. Capturer le contenu de la vue
        ob_start();
        require __DIR__ . "/../../../views/auth/{$view}.php";
        $content = ob_get_clean();

        // 2. Charger le layout d’authentification
        require __DIR__ . "/../../../views/layouts/auth.php";
    }

    // ---------------------------------------------
    //  CONNEXION
    // ---------------------------------------------
    public function showLogin(): void
    {
        $this->renderAuth("login", [
            "title"       => "Connexion — EPortailEmploi",
            "authVariant" => "login"
        ]);
    }

    public function login(): void{
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $this->service->login($email, $password);

    if ($result['success']) {
        header("Location: /");
        exit;
        }

    $this->renderAuth("login", [
        "error" => $result['error'],
        "title" => "Connexion — EPortailEmploi"
        ]);
    }

    // ---------------------------------------------
    //  INSCRIPTION CANDIDAT
    // ---------------------------------------------
    public function showRegisterCandidat(): void
    {
        $this->renderAuth("register_candidat", [
            "title"       => "Créer un compte candidat",
            "authVariant" => "register"
        ]);
    }

    public function registerCandidat(): void
    {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $this->service->register($email, $password, "candidat");

        header("Location: /login");
        exit;
    }

    // ---------------------------------------------
    //  INSCRIPTION ENTREPRISE / GESTIONNAIRE
    // ---------------------------------------------
    public function showRegisterEntreprise(): void
    {
        $this->renderAuth("register_entreprise", [
            "title"       => "Créer un espace entreprise",
            "authVariant" => "register"
        ]);
    }

    public function registerEntreprise(): void
    {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // création DU GESTIONNAIRE (rôle par défaut)
        $this->service->registerCandidat($data);

        header("Location: /login");
        exit;
    }

    // ---------------------------------------------
    //  MOT DE PASSE OUBLIÉ
    // ---------------------------------------------
    public function showForgotPassword(): void
    {
        $this->renderAuth("forgot_password", [
            "title"       => "Mot de passe oublié",
            "authVariant" => "forgot"
        ]);
    }

    // ---------------------------------------------
    //  RÉINITIALISATION (NOUVEAU MOT DE PASSE)
    // ---------------------------------------------
    public function showResetPassword(): void
    {
        $this->renderAuth("reset_password", [
            "title"       => "Réinitialiser le mot de passe",
            "authVariant" => "reset"
        ]);
    }
    public function logout(): void
{
    session_destroy();
    header("Location: /Eportail_Emploi/public/login");
    exit;
}
}