<?php

namespace App\Modules\Entreprise;

use App\Core\Database;
use App\Core\Auth;
use App\Modules\Auth\AuthRepository;
use App\Modules\Auth\AuthService;

/**
 * Contrôleur Entreprise (partie administrateur)
 * Gère l'affichage des vues et la transmission des actions
 * au service métier (EntrepriseService).
 */
class EntrepriseController
{
    /**
     * Fabrique une instance du service métier Entreprise.
     * Permet d'éviter la création manuelle des dépendances partout.
     */
    private function makeService(): EntrepriseService
    {
        $pdo  = Database::getConnection();
        $auth = new AuthService(new AuthRepository($pdo), $pdo);
        $repo = new EntrepriseRepository($pdo);

        return new EntrepriseService($repo, $auth, $pdo);
    }


    public function Index(): void
        {
        $service     = $this->makeService();
        $entreprises = $service->listEntreprises();

        // vue publique avec layout main
        $this->renderPublic("public_list", [
            "title"       => "Gestion des entreprises",
            "entreprises" => $entreprises
        ]);
    }
    
    private function renderPublic(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require __DIR__ . "/../../../views/entreprise/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }       
    
    /**
     * Charge une vue du tableau de bord (layout back-office).
     */
    private function renderDashboard(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require __DIR__ . "/../../../views/entreprise/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    
    public function adminIndex(): void
    {
        Auth::requireRole(['admin']); // Seul admin peut accéder à cette page

        $service     = $this->makeService();
        $entreprises = $service->listEntreprises();

        $this->renderDashboard("list", [
            "title"       => "Gestion des entreprises",
            "entreprises" => $entreprises
        ]);
    }

    /**
     * Formulaire de création d'une nouvelle entreprise.
     */
    public function createForm(): void
    {
        $service  = $this->makeService();
        $secteurs = $service->listSecteurs();

        $this->renderDashboard("create", [
            "title"    => "Créer une entreprise",
            "secteurs" => $secteurs
        ]);
    }

    /**
     * Traitement de création d'entreprise + gestionnaire.
     */
    public function create(): void
    {
    $service = $this->makeService();
    $result  = $service->createEntrepriseAvecGestionnaireAdmin($_POST);

    if (!$result['success']) {
        $this->renderDashboard("create", [
            "title"    => "Créer une entreprise",
            "error"    => $result['error'],
            "secteurs" => $service->listSecteurs()
        ]);
        return;
    }

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

        $service    = $this->makeService();
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
        $service = $this->makeService();
        $id      = (int)($_POST['id'] ?? 0);
        $result = $service->updateEntreprise($id, $_POST);
        if (!$result['success']) {
            die("Erreur : " . htmlspecialchars($result['error']));
        }

        header("Location: /admin/entreprises");
        exit;
    }

    /**
     * Suppression d'une entreprise.
     */
    public function delete(): void
    {
        $service = $this->makeService();
        $id      = (int)($_POST['id'] ?? 0);
        $service->deleteEntreprise($id);

        header("Location: /admin/entreprises");
        exit;
    }
}