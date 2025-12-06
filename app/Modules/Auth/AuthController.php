<?php

declare(strict_types=1);

namespace App\Modules\Auth;

/**
 * Contrôleur d'authentification.
 * Il est responsable :
 *  - d'afficher les vues (login / register)
 *  - de récupérer les données $_POST
 *  - d'appeler le service métier
 *  - puis de rediriger l'utilisateur.
 */
class AuthController
{
    public function __construct(
        private AuthService $service
    ) {}

    
    public function showLogin(): void
    {
        $title = "Connexion";

        ob_start();
        require __DIR__ . "/../../../views/auth/login.php";
        $content = ob_get_clean();

        // On injecte $content dans le layout principal
        require __DIR__ . "/../../../views/layouts/main.php";
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? "";
        $password = $_POST['password'] ?? "";

        // On délègue la logique métier au service
        if ($this->service->login($email, $password)) {
            // Si OK → redirection vers le tableau de bord (à affiner par rôle)
            header("Location: /dashboard");
            exit;
        }

        // Sinon, message simple (plus tard, on affichera une erreur plus propre dans la vue)
        echo "Identifiants incorrects.";
    }

    
    /* Affiche formulaire d'inscription pour un candidat.*/
    public function showRegisterCandidat(): void
    {
        $title = "Inscription candidat";

        ob_start();
        require __DIR__ . "/../../../views/auth/register_candidat.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }

    /*S'occupe du formulaire d'inscription candidat.*/
    public function registerCandidat(): void
    {
        $this->service->register(
            $_POST['email'],
            $_POST['password'],
            "candidat" // rôle fixé pour ce formulaire
        );

        header("Location: /login");
        exit;
    }

    /**
     * Affiche le formulaire d'inscription pour une entreprise
     * (gestionnaire d’entreprise par défaut).
     */
    public function showRegisterEntreprise(): void
    {
        $title = "Inscription entreprise";

        ob_start();
        require __DIR__ . "/../../../views/auth/register_entreprise.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }

    /*pour formulaire d'inscription entreprise*/
    public function registerEntreprise(): void
    {
        $this->service->register(
            $_POST['email'],
            $_POST['password'],
            "gestionnaire" // ici, on crée un gestionnaire d’entreprise
        );

        header("Location: /login");
        exit;
    }
}