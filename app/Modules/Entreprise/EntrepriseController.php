<?php

namespace App\Modules\Entreprise;

use App\Core\Database;
use App\Modules\Auth\AuthRepository;
use App\Modules\Auth\AuthService;
use PDO;

class EntrepriseController
{
    private function makeService(): EntrepriseService
    {
        $pdo = Database::getConnection();

        $authRepo   = new AuthRepository($pdo);
        $authService = new AuthService($authRepo, $pdo);
        $repo       = new EntrepriseRepository($pdo);

        return new EntrepriseService($repo, $authService, $pdo);
    }

    public function index(): void
    {
        $service     = $this->makeService();
        $entreprises = $service->repo->getAll();

        $title = "Entreprises — Admin";

        ob_start();
        require __DIR__ . "/../../../views/entreprise/list.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }

    public function createForm(): void
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->query("SELECT id, libelle FROM secteurs_entreprises ORDER BY libelle ASC");
        $secteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = "Créer une entreprise — Admin";

        ob_start();
        require __DIR__ . "/../../../views/entreprise/create.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }

    public function create(): void
    {
        $service = $this->makeService();

        $entrepriseData = [
            'nom'         => $_POST['nom'] ?? '',
            'secteur_id'  => (int)($_POST['secteur_id'] ?? 0),
            'adresse'     => $_POST['adresse'] ?? '',
            'code_postal' => $_POST['code_postal'] ?? '',
            'ville'       => $_POST['ville'] ?? '',
            'pays'        => $_POST['pays'] ?? '',
            'telephone'   => $_POST['telephone'] ?? '',
            'email'       => $_POST['email'] ?? '',
            'siret'       => $_POST['siret'] ?? '',
            'site_web'    => $_POST['site_web'] ?? '',
            'taille'      => $_POST['taille'] ?? '',
            'description' => $_POST['description'] ?? '',
            'logo'        => null,
        ];

        $gestionnaireData = [
            'prenom'        => $_POST['prenom'] ?? '',
            'nom'           => $_POST['nom_gestionnaire'] ?? '',
            'email'         => $_POST['email_gestionnaire'] ?? '',
            'mot_de_passe'  => $_POST['mot_de_passe'] ?? '',
            'role'          => 'gestionnaire',
            'entreprise_id' => null,
        ];

        $result = $service->createEntrepriseEtGestionnaire($entrepriseData, $gestionnaireData);

        if ($result['success']) {
            header("Location: /admin/entreprises");
            exit;
        }

        die("Erreur : " . $result['error']);
    }
}