<?php

namespace App\Modules\Entreprise;

class EntrepriseController
{
    public function __construct(private EntrepriseService $service) {}

    public function index(): void
    {
        $entreprises = $this->service->repo->getAll();
        require __DIR__ . "/views/list.php";
    }

    public function createForm(): void
    {
        require __DIR__ . "/views/create.php";
    }

    public function create(): void
    {
        $entrepriseData = [
            'nom' => $_POST['nom'],
            'secteur_id' => $_POST['secteur_id'],
            'adresse' => $_POST['adresse'],
            'code_postal' => $_POST['code_postal'],
            'ville' => $_POST['ville'],
            'pays' => $_POST['pays'],
            'telephone' => $_POST['telephone'],
            'email' => $_POST['email'],
            'siret' => $_POST['siret'],
            'site_web' => $_POST['site_web'],
            'taille' => $_POST['taille'],
            'description' => $_POST['description'],
            'logo' => null
        ];

        $gestionnaireData = [
            'prenom' => $_POST['prenom'],
            'nom' => $_POST['nom_gestionnaire'],
            'email' => $_POST['email_gestionnaire'],
            'mot_de_passe' => $_POST['mot_de_passe']
        ];

        $result = $this->service->createEntrepriseEtGestionnaire($entrepriseData, $gestionnaireData);

        if ($result['success']) {
            header("Location: /admin/entreprises");
            exit;
        }

        die("Erreur : " . $result['error']);
    }
}