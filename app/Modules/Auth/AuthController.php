<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use App\Core\ControllerBase; // si tu fais une base, sinon pas grave
use App\Modules\Entreprise\EntrepriseService;

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
        private AuthService $service,
        private EntrepriseService $entrepriseService,
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
         // Préparation des données du formulaire
        $data = [
            'prenom'        => htmlspecialchars($_POST['prenom'] ?? ''),
            'nom'           => htmlspecialchars($_POST['nom'] ?? ''),
            'email'         => trim($_POST['email'] ?? ''),
            'mot_de_passe'  => $_POST['password'] ?? '',
            // Le service se chargera d'ajouter le rôle et l'ID d'entreprise
        ];

        $result = $this->service->registerCandidat($data);

        if ($result['success']) {
            header('Location: /login');
            exit;
        }

        // En cas d'erreur, réafficher le formulaire avec le message
        $this->renderAuth('register_candidat', [
            'title'       => 'Créer un compte candidat',
            'authVariant' => 'register',
            'error'       => $result['error'] ?? 'Erreur inconnue'
        ]);
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
    // --- 1) Données du gestionnaire ---
    $gestionnaireData = [
        'prenom'        => htmlspecialchars($_POST['prenom'] ?? ''),
        'nom'           => htmlspecialchars($_POST['nom'] ?? ''),
        'email'         => trim($_POST['email'] ?? ''),
        'mot_de_passe'  => $_POST['password'] ?? '',
        'role'          => 'gestionnaire',
        'entreprise_id' => null
    ];

    // --- 2) Données de l'entreprise ---
    $entrepriseData = [
        'nom'          => htmlspecialchars($_POST['nom_entreprise'] ?? ''),
        'secteur_id'   => (int)($_POST['secteur_id'] ?? 0),
        'adresse'      => htmlspecialchars($_POST['adresse'] ?? ''),
        'code_postal'  => htmlspecialchars($_POST['code_postal'] ?? ''),
        'ville'        => htmlspecialchars($_POST['ville'] ?? ''),
        'pays'         => htmlspecialchars($_POST['pays'] ?? ''),
        'telephone'    => htmlspecialchars($_POST['telephone'] ?? ''),
        'email'        => trim($_POST['email_entreprise'] ?? ''),
        'siret'        => trim($_POST['siret'] ?? ''),
        'site_web'     => htmlspecialchars($_POST['site_web'] ?? ''),
        'taille'       => htmlspecialchars($_POST['taille'] ?? ''),
        'description'  => htmlspecialchars($_POST['description'] ?? ''),
        'logo'         => null
    ];

    // 3) Appel du service métier
    $result = $this->entrepriseService->createEntrepriseEtGestionnaire(
        $entrepriseData,
        $gestionnaireData
    );

    if ($result['success']) {
        header("Location: /login");
        exit;
    }

    // En cas d'erreur
    $this->renderAuth("register_entreprise", [
        "title" => "Créer un espace entreprise",
        "authVariant" => "register",
        "error" => $result['error']
    ]);
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