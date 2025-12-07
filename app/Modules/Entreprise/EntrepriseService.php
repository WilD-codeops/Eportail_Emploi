<?php

namespace App\Modules\Entreprise;

use App\Modules\Auth\AuthService;
use PDO;
use Throwable;

class EntrepriseService
{
    public function __construct(
        public EntrepriseRepository $repo,
        private AuthService $authService,
        private PDO $pdo
    ) {}

    public function createEntrepriseEtGestionnaire(array $entrepriseData, array $gestionnaireData): array
    {
        try {
            $this->pdo->beginTransaction();

            /* --- 1. Hash du mot de passe gestionnaire --- */
            $gestionnaireData['mot_de_passe'] = password_hash(
                $gestionnaireData['mot_de_passe'],
                PASSWORD_DEFAULT
            );

            /* --- 2. Création du gestionnaire --- */
            $gestionnaireId = $this->authService->createUser($gestionnaireData);

            if (!$gestionnaireId) {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'error'   => "Impossible de créer le gestionnaire."
                ];
            }

            /* --- 3. Création de l’entreprise --- */
            $entrepriseData['gestionnaire_id'] = $gestionnaireId;

            $entrepriseId = $this->repo->createEntreprise($entrepriseData);

            if (!$entrepriseId) {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'error'   => "Impossible de créer l’entreprise."
                ];
            }

            /* --- 4. Mise à jour du user avec entreprise_id --- */
            $stmt = $this->pdo->prepare(
                "UPDATE users SET entreprise_id = :entreprise_id WHERE id = :id"
            );

            $stmt->execute([
                ':entreprise_id' => $entrepriseId,
                ':id'            => $gestionnaireId,
            ]);

            /* --- 5. Fin de la transaction --- */
            $this->pdo->commit();

            return [
                'success'       => true,
                'entreprise_id' => $entrepriseId,
                'error'         => null
            ];

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'success'       => false,
                'entreprise_id' => null,
                'error'         => "Erreur interne : " . $e->getMessage()
            ];
        }
    }
}

