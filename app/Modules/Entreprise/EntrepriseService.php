<?php

namespace App\Modules\Entreprise;

use App\Core\Database;
use App\Modules\Auth\AuthService;
use PDO;

class EntrepriseService
{
    public function __construct(
        private EntrepriseRepository $repo,
        private AuthService $authService,
        private PDO $pdo
    ) {}

    /** Liste */
    public function listEntreprises(): array
    {
        return $this->repo->getAll();
    }

    /** Secteurs */
    public function listSecteurs(): array
    {
        return $this->repo->getSecteurs();
    }

    /** Trouver entreprise */
    public function findEntreprise(int $id): ?array
    {
        return $this->repo->find($id);
    }
    

    /** SIRET deja enreistré en base*/
    public function siretExists(string $siret): bool
    {
        return $this->repo->siretExists($siret);
    }



    /* Créer entreprise + gestionnaire Inscription */
    public function createEntrepriseEtGestionnaire(array $entrepriseData, array $gestionnaireData): array
    {
        // Validation entreprise
        $validE = EntrepriseValidator::validateCreate($entrepriseData);
        if (!$validE['success']) return $validE;

        // Validation gestionnaire
        $validG = EntrepriseValidator::validateGestionnaire($gestionnaireData);
        if (!$validG['success']) return $validG;

        try {
            $this->pdo->beginTransaction();

            // 1) Création user gestionnaire
            $gestionnaireId = $this->authService->createUser([
                'prenom'       => $gestionnaireData['prenom'],
                'nom'          => $gestionnaireData['nom'],
                'email'        => $gestionnaireData['email'],
                'mot_de_passe' => $gestionnaireData['password'],
                'role'         => 'gestionnaire',
                'entreprise_id'=> null
            ]);

            // 2) Création entreprise
            $entrepriseData['gestionnaire_id'] = $gestionnaireId;
            $entrepriseId = $this->repo->createEntreprise($entrepriseData);

            // 3) Lier user → entreprise
            $this->repo->attachUserToEntreprise($gestionnaireId, $entrepriseId);

            $this->pdo->commit();
            return ['success' => true];

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }





    /**Création d'Entreprise Version admin */
    public function createEntrepriseAvecGestionnaireAdmin(array $data): array
    {
        return $this->createEntrepriseEtGestionnaire($data, $data);
    }



    /** Modifier */
    public function updateEntreprise(int $id, array $data): array
    {
        $valid = EntrepriseValidator::validateCreate($data);
        if (!$valid['success']) return $valid;

        $ok = $this->repo->updateEntreprise($id, $data);

        return $ok ? ['success' => true] : ['success' => false, 'error' => "Échec update"];
    }


    
    /** Supprimer */
    public function deleteEntreprise(int $id): bool
    {
        return $this->repo->deleteEntreprise($id);
    }
}