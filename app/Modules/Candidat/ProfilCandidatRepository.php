<?php

namespace App\Modules\Candidat;

use PDO;

class ProfilCandidatRepository
{
    public function __construct(private PDO $pdo) {}

    public function createProfil(array $data): array
    {
        $sql = "INSERT INTO profils_candidats
                (candidat_id, poste_recherche, description, disponibilite, mobilite, annee_experience, niveau_etudes, statut_actuel)
                VALUES (:candidat_id, :poste_recherche, :description, :disponibilite, :mobilite, :annee_experience, :niveau_etudes, :statut_actuel)";

        try { 
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':candidat_id'       => $data['candidat_id'],
                ':poste_recherche'   => $data['poste_recherche'] ?? null,
                ':description'       => $data['description'] ?? null,
                ':disponibilite'     => $data['disponibilite'] ?? null,
                ':mobilite'          => $data['mobilite'] ?? null,
                ':annee_experience'  => $data['annee_experience'] ?? null,
                ':niveau_etudes'     => $data['niveau_etudes'] ?? null,
                ':statut_actuel'     => $data['statut_actuel'] ?? null,
            ]); 

            return ['success' => true, 'id' => (int)$this->pdo->lastInsertId()];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
}

