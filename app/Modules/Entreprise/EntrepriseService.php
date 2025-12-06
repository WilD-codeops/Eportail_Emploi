<?php

namespace App\Modules\Entreprise;

use App\Modules\Auth\AuthService;
use PDO;

class EntrepriseService
{
    public function __construct(
        private EntrepriseRepository $repo,
        private AuthService $authService,
        private PDO $pdo
    ) {}

    public function createEntrepriseEtGestionnaire(array $entrepriseData, array $gestionnaireData): array
{
    try {
        $this->pdo->beginTransaction();

        // 1) Création du gestionnaire
        $gestionnaireId = $this->authService->createUser($gestionnaireData);

        // 2) Création de l'entreprise
        $entrepriseData['gestionnaire_id'] = $gestionnaireId;
        $entrepriseId = $this->repo->createEntreprise($entrepriseData);

        $this->pdo->commit();

        return [
            'success' => true,
            'entreprise_id' => $entrepriseId
        ];

    } catch (\Throwable $e) {
        $this->pdo->rollBack();
        return [
            'success' => false,
            'error' => "Erreur lors de l'inscription entreprise : " . $e->getMessage()
        ];
    }
}
}