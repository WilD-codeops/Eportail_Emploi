<?php

namespace App\Modules\Entreprise;

use App\Core\Database;
use App\Core\Auth;
use App\Modules\Auth\AuthRepository;
use App\Modules\Auth\AuthService;
use App\Modules\Auth\AuthRegistrationService;
use App\Core\Security;

/**
 * Contrôleur Entreprise (partie administrateur)
 * Gère l'affichage des vues et la transmission des actions
 * au service métier (EntrepriseService).
 */
class EntrepriseController
{
    /**
     * Fabrique les instances des services métier Entreprise, AuthRegistrationService et Auth.
     * Permet d'éviter la création manuelle des dépendances partout.
     */

    private function makeAuthService(): AuthService
    {
        $pdo  = Database::getConnection();
        $repo = new AuthRepository($pdo);

        return new AuthService($repo, $pdo);
    }

    private function makeEntrepriseService(): EntrepriseService
    {
        $pdo  = Database::getConnection();
        $auth = $this->makeAuthService();
        $repo = new EntrepriseRepository($pdo);

        return new EntrepriseService($repo, $auth, $pdo);
    }

    private function makeAuthRegistrationService(): AuthRegistrationService
    {
        $authService = $this->makeAuthService();
        $entrepriseService = $this->makeEntrepriseService();

        return new AuthRegistrationService($authService, $entrepriseService);
    }

    /**
     * MES RENDERERS PERSONNALISÉS
     */
    
    // Charge une vue publique (layout main)
    private function renderPublic(string $view, array $params = []): void
    {
        extract($params);
        
        ob_start();
        require __DIR__ . "/../../../views/entreprise/{$view}.php";
        $content = ob_get_clean();
        
        require __DIR__ . "/../../../views/layouts/main.php";
    }       
    
    // Charge une vue du tableau de bord (layout dashboard) 
    private function renderDashboard(string $view, array $params = []): void
    {
        extract($params);
        
        ob_start();
        require __DIR__ . "/../../../views/entreprise/{$view}.php";
        $content = ob_get_clean();
        
        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    /**
     * MES FONCTIONS CONTROLEUR
     */
    public function Index(): void
        { 
        
        $service     = $this->makeEntrepriseService();
        $entreprises = $service->listEntreprises();

        // vue publique avec layout main
        $this->renderPublic("public_list", [
            "title"       => "Gestion des entreprises",
            "entreprises" => $entreprises
        ]);
    }
    
    
    public function adminIndex(): void
    {
        Auth::requireRole(['admin']); // Seul admin peut accéder à cette page

        $service     = $this->makeEntrepriseService();
        $entreprises = $service->listEntreprises();

        $this->renderDashboard("list", [
            "title"       => "Gestion des entreprises",
            "entreprises" => $entreprises
        ]);
    }
    

    /*Formulaire de création d'une nouvelle entreprise*/
    public function createForm(): void
    {
        Auth::requireRole(['admin']); // Seul admin peut accéder à cette page
        
        $service  = $this->makeEntrepriseService();
        $secteurs = $service->listSecteurs();

        $this->renderDashboard("create", [
            "title"    => "Créer une entreprise",
            "secteurs" => $secteurs
        ]);
    }

    /*Traitement de création d'entreprise + gestionnaire post createForm*/
    public function create(): void
    {
        Auth::requireRole(['admin']); // Seul admin peut accéder à cette action
        // Vérification token CSRF
        Security::requireCsrfToken('entreprise_create', $_POST['csrf_token'] ?? null);
        
        $service = $this->makeEntrepriseService();
        $registration = $this->makeAuthRegistrationService();
        $result  = $registration->registerEntreprise($_POST);

        if (!$result['success']) {

            $entrepriseToCreate = $_POST;
            $this->renderDashboard("create", [
                "title"       => "Créer une entreprise",
                "entreprise" => $entrepriseToCreate,
                "error"      => $result['error'],
                "secteurs"   => $service->listSecteurs()
            ]);
            
            return;
        }

        self::flashSuccess($result['message']);
        header("Location: /admin/entreprises");
        exit;
    }

    /**
     * Formulaire de modification d'une entreprise.
     */
    public function editForm(): void
    {
        Auth::requireRole(['admin','recruteur','gestionnaire']); // Accès restreint aux rôles spécifiés
        $id = (int)($_GET['id'] ?? 0);

        $service    = $this->makeEntrepriseService();
        $entreprise = $service->findEntreprise($id);

        if (!$entreprise) {
            die("Entreprise introuvable.");
        }

        $this->renderDashboard("edit", [
            "title"      => "Modifier une entreprise",
            "entreprise" => $entreprise,
            "secteurs"   => $service->listSecteurs()
        ]);
    }

    /**
     * Traitement des modifications d'entreprise.
     */
    public function update(): void
    {
        Auth::requireRole(['admin','recruteur','gestionnaire']); // Acces restreint aux rôles spécifiés
        Security::requireCsrfToken('entreprise_edit', $_POST['csrf_token'] ?? null);

        $service = $this->makeEntrepriseService();
        $id      = (int)($_POST['id'] ?? 0);

        // Appel service
        $result = $service->updateEntreprise($id, $_POST);

        // Si erreur => on ré-affiche le formulaire avec les données saisies
        if (!$result['success']) {
            // IMPORTANT : on renvoie au form les valeurs saisies (POST) pour éviter de tout retaper
            // On garde aussi l'id car edit.php construit l'action avec l'id
            $entreprise = $_POST;
            $entreprise['id'] = $id;

            $this->renderDashboard("edit", [
                "title"      => "Modifier une entreprise",
                "entreprise" => $entreprise,
                "secteurs"   => $service->listSecteurs(),
                "error"      => $result['error'],    // message d’erreur
            ]);
            return;
        }
        self::flashSuccess($result['message']);
        header("Location: /admin/entreprises");
        exit;
    }


    /**
     * Suppression d'une entreprise.
     */
    public function delete(): void
    {
        $service = $this->makeEntrepriseService();
        $id      = (int)($_POST['id'] ?? 0);
        $result=$service->deleteEntreprise($id);

        if (!$result['success']) {
            self::flashError($result['error']);
            header("Location: /admin/entreprises");
            exit;
        } 

        self::flashSuccess($result['message']);
        header("Location: /admin/entreprises");
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