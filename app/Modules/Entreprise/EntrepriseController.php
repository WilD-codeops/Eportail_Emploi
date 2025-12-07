<?php

namespace App\Modules\Entreprise;

use App\Core\Auth;
use App\Core\Database;
use App\Modules\Auth\AuthRepository;
use App\Modules\Auth\AuthService;

class EntrepriseController
{
    private function makeService(): EntrepriseService
    {
        $pdo = Database::getConnection();
        $authService = new AuthService(new AuthRepository($pdo), $pdo);
        $repo = new EntrepriseRepository($pdo);

        return new EntrepriseService($repo, $authService, $pdo);
    }

    /**
     * Liste publique des entreprises partenaires URL : /Eportail_Emploi/public/entreprises
     * Layout : main.php
     */
    public function index(): void
    {
        $service = $this->makeService();
        $entreprises = $service->repo->getAllPublic();

        $title = "Entreprises partenaires – EPortailEmploi";

        ob_start();
        require __DIR__ . "/../../../views/entreprise/public_list.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }

    /**
     * Liste admin des entreprises
     * URL : /Eportail_Emploi/public/admin/entreprises
     * Layout : dashboard.php
     */
    public function adminIndex(): void
    {
        Auth::requireRole(['admin']);

        $service = $this->makeService();
        $entreprises = $service->repo->getAllAdmin();

        $title = "Gestion des entreprises";

        ob_start();
        require __DIR__ . "/../../../views/entreprise/list.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    /**
     * Formulaire de création entreprise (admin)
     */
    public function createForm(): void
    {
        Auth::requireRole(['admin']);

        $service = $this->makeService();
        $secteurs = $service->repo->getSecteurs();

        $title = "Créer une entreprise";

        ob_start();
        require __DIR__ . "/../../../views/entreprise/create.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    /**
     * Traitement création entreprise + gestionnaire (admin)
     * (tu as déjà la logique dans EntrepriseService)
     */
    public function create(): void
    {
        Auth::requireRole(['admin']);

        $service = $this->makeService();

        $entrepriseData = [
            'nom'         => $_POST['nom_entreprise'] ?? '',
            'secteur_id'  => (int)($_POST['secteur_id'] ?? 0),
            'adresse'     => $_POST['adresse'] ?? '',
            'code_postal' => $_POST['code_postal'] ?? '',
            'ville'       => $_POST['ville'] ?? '',
            'pays'        => $_POST['pays'] ?? '',
            'telephone'   => $_POST['telephone'] ?? null,
            'email'       => $_POST['email_entreprise'] ?? null,
            'siret'       => $_POST['siret'] ?? '',
            'site_web'    => $_POST['site_web'] ?? null,
            'taille'      => $_POST['taille'] ?? null,
            'description' => $_POST['description'] ?? null,
            'logo'        => null,
        ];

        $gestionnaireData = [
            'prenom'        => $_POST['prenom'] ?? '',
            'nom'           => $_POST['nom_gestionnaire'] ?? '',
            'email'         => $_POST['email_gestionnaire'] ?? '',
            'mot_de_passe'  => $_POST['mot_de_passe'] ?? '',
            'role'          => 'gestionnaire',
            'entreprise_id' => null
        ];

        $result = $service->createEntrepriseEtGestionnaire($entrepriseData, $gestionnaireData);

        if (!$result['success']) {
            // pour l’instant : simple, tu pourras améliorer
            die("Erreur création entreprise : " . $result['error']);
        }

        header("Location: /Eportail_Emploi/public/admin/entreprises");
        exit;
    }

    /**
     * Formulaire d'édition entreprise (admin)
     * URL : /Eportail_Emploi/public/admin/entreprises/edit?id=123
     */
    public function edit(): void
    {
        Auth::requireRole(['admin']);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo "ID entreprise invalide";
            return;
        }

        $service = $this->makeService();
        $entreprise = $service->repo->find($id);
        $secteurs = $service->repo->getSecteurs();

        if (!$entreprise) {
            http_response_code(404);
            echo "Entreprise introuvable";
            return;
        }

        $title = "Modifier une entreprise";

        ob_start();
        require __DIR__ . "/../../../views/entreprise/edit.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    /**
     * Traitement modification entreprise (admin)
     */
    public function update(): void
    {
        Auth::requireRole(['admin']);

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo "ID entreprise invalide";
            return;
        }

        $service = $this->makeService();

        $data = [
            'nom'         => $_POST['nom_entreprise'] ?? '',
            'secteur_id'  => (int)($_POST['secteur_id'] ?? 0),
            'adresse'     => $_POST['adresse'] ?? '',
            'code_postal' => $_POST['code_postal'] ?? '',
            'ville'       => $_POST['ville'] ?? '',
            'pays'        => $_POST['pays'] ?? '',
            'telephone'   => $_POST['telephone'] ?? null,
            'email'       => $_POST['email_entreprise'] ?? null,
            'siret'       => $_POST['siret'] ?? '',
            'site_web'    => $_POST['site_web'] ?? null,
            'taille'      => $_POST['taille'] ?? null,
            'description' => $_POST['description'] ?? null,
        ];

        $ok = $service->repo->updateEntreprise($id, $data);

        if (!$ok) {
            http_response_code(500);
            echo "Erreur lors de la mise à jour de l’entreprise";
            return;
        }

        header("Location: /Eportail_Emploi/public/admin/entreprises");
        exit;
    }

    /**
     * Suppression entreprise (admin)
     * URL : POST /Eportail_Emploi/public/admin/entreprises/delete
     */
    public function delete(): void
    {
        Auth::requireRole(['admin']);

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo "ID entreprise invalide";
            return;
        }

        $service = $this->makeService();

        $service->repo->deleteEntreprise($id);

        header("Location: /Eportail_Emploi/public/admin/entreprises");
        exit;
    }
}