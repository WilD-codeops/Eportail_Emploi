<?php
namespace App\Modules\Candidat;

use PDO;

class ProfilCandidatRepository {
    public function __construct(private PDO $pdo) {}

    public function createProfil(array $data): array {

        $sql="  INSERT INTO profils_candidats (candidat_id, poste_recherche, description, disponibilite, mobilite, annee_experience, niveau_etudes, statut_actuel)
                VALUES (:candidat_id, :poste_recherche, :description, :disponibilite, :mobilite, :annee_experience, :niveau_etudes, :statut_actuel)";
                try {
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->bindParam(':candidat_id', $data['candidat_id']);
                        $stmt->bindParam(':poste_recherche', $data['poste_recherche'] ?? null);
                        $stmt->bindParam(':description', $data['description'] ?? null);
                        $stmt->bindParam(':disponibilite', $data['disponibilite'] ?? null);
                        $stmt->bindParam(':mobilite', $data['mobilite'] ?? null);
                        $stmt->bindParam(':annee_experience', $data['annee_experience'] ?? null);
                        $stmt->bindParam(':niveau_etudes', $data['niveau_etudes'] ?? null);
                        $stmt->bindParam(':statut_actuel', $data['statut_actuel'] ?? null);
                       
                        $stmt->execute();

                        return ['success' => true, 'id' => $this->pdo->lastInsertId()];
                     } catch (\PDOException $e) {
                        return ['success' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
                     }
    }
}
