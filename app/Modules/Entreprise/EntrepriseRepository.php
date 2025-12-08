<?php

namespace App\Modules\Entreprise;

use PDO;

class EntrepriseRepository
{
    public function __construct(private PDO $pdo) {}

    /** Toutes les entreprises */
    public function getAll(): array
    {
        $sql = "SELECT e.*, s.libelle AS secteur
                FROM entreprises e
                LEFT JOIN secteurs_entreprises s ON s.id = e.secteur_id
                ORDER BY e.nom ASC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Secteurs */
    public function getSecteurs(): array
    {
        return $this->pdo
            ->query("SELECT id, libelle FROM secteurs_entreprises ORDER BY libelle ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Trouver entreprise */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM entreprises WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** Créer entreprise */
    public function createEntreprise(array $data): int
    {
        if (empty($data['gestionnaire_id'])) {
            throw new \Exception("Entreprise sans gestionnaire interdite.");
        }

        $sql = "INSERT INTO entreprises
                (gestionnaire_id, nom, secteur_id, adresse, code_postal, ville, pays,
                 telephone, email, siret, site_web, taille, description, logo)
                VALUES
                (:gestionnaire_id, :nom, :secteur_id, :adresse, :code_postal, :ville, :pays,
                 :telephone, :email, :siret, :site_web, :taille, :description, :logo)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }

    /** Lien gestionnaire → entreprise */
    public function attachUserToEntreprise(int $userId, int $entrepriseId): void
    {
        $sql = "UPDATE users SET entreprise_id = :eid WHERE id = :uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'eid' => $entrepriseId,
            'uid' => $userId
        ]);
    }

    /** Modifier entreprise */
    public function updateEntreprise(int $id, array $data): bool
    {
        $sql = "UPDATE entreprises SET
                    nom = :nom,
                    secteur_id = :secteur_id,
                    adresse = :adresse,
                    code_postal = :code_postal,
                    ville = :ville,
                    pays = :pays,
                    telephone = :telephone,
                    email = :email,
                    siret = :siret,
                    site_web = :site_web,
                    taille = :taille,
                    description = :description
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    /** Supprimer entreprise */
    public function deleteEntreprise(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM entreprises WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}