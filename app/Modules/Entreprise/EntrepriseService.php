<?php

namespace App\Modules\Entreprise;

use App\Modules\Auth\AuthService;
use PDO;

class EntrepriseService
{
    public function __construct(
        public EntrepriseRepository $repo,
        private AuthService $authService,
        private PDO $pdo
    ) {
        // Constructeur léger — les dépendances sont injectées
    }

    /**
     * Crée une entreprise et son gestionnaire en transaction.
     *
     * @param array $entrepriseData  Données de l'entreprise à créer.
     * @param array $gestionnaireData Données du gestionnaire à créer.
     * @return array ['success' => bool, 'entreprise_id' => int|null, 'error' => string|null]
     */
    public function createEntrepriseEtGestionnaire(
        array $entrepriseData,
        array $gestionnaireData
    ): array {
        try {
                $this->pdo->beginTransaction();
                // 1) Création du gestionnaire
                $gestionnaireId = $this->authService->createUser($gestionnaireData);

                // 2) Création de l'entreprise
                $entrepriseData['gestionnaire_id'] = $gestionnaireId;
                $entrepriseId = $this->repo->createEntreprise($entrepriseData);

                // 3) Mise à jour de l'utilisateur (entreprise_id)
                $stmt = $this->pdo->prepare("UPDATE users SET entreprise_id = :entreprise_id WHERE id = :id");
                $stmt->execute([
                    ':entreprise_id' => $entrepriseId,
                    ':id'            => $gestionnaireId,
                ]);

                $this->pdo->commit();

                return [
                    'success'       => true,
                    'entreprise_id' => $entrepriseId,
                    'error'         => null,
                ];

             } catch (\Throwable $e) {
                    $this->pdo->rollBack();

                    return [
                        'success'       => false,
                        'entreprise_id' => null,
                        'error'         => 'Erreur lors de l\'inscription entreprise : ' . $e->getMessage(),
                    ];
                }           
}
}