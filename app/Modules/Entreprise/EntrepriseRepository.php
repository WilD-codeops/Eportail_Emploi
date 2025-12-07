<?php

namespace App\Modules\Entreprise;

use PDO;

class EntrepriseRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Liste admin (avec secteur)
     */
    public function getAllAdmin(): array
    {
        $sql = "
            SELECT e.*, s.libelle AS secteur
            FROM entreprises e
            LEFT JOIN secteurs_entreprises s ON s.id = e.secteur_id
            ORDER BY e.nom ASC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Liste publique (tu peux filtrer plus tard : actifs, etc.)
     */
    public function getAllPublic(): array
    {
        $sql = "
            SELECT e.id, e.nom, e.ville, e.pays, e.secteur_id, s.libelle AS secteur, e.taille, e.logo, e.description
            FROM entreprises e
            LEFT JOIN secteurs_entreprises s ON s.id = e.secteur_id
            ORDER BY e.nom ASC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Récupération d'une entreprise par ID
     */
    public function find(int $id): ?array
    {
        $sql = "
            SELECT e.*, s.libelle AS secteur
            FROM entreprises e
            LEFT JOIN secteurs_entreprises s ON s.id = e.secteur_id
            WHERE e.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

        return $entreprise ?: null;
    }


    /**
     * Création entreprise (utilisée dans EntrepriseService)
     */
    public function createEntreprise(array $data): int
    {
        $sql = "INSERT INTO entreprises 
            (gestionnaire_id, nom, secteur_id, adresse, code_postal, ville, pays, telephone, email, siret, site_web, taille, description, logo)
            VALUES
            (:gestionnaire_id, :nom, :secteur_id, :adresse, :code_postal, :ville, :pays, :telephone, :email, :siret, :site_web, :taille, :description, :logo)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }

    
    /**
     * Mise à jour d'une entreprise
     */
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
                WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $data['id'] = $id;

        return $stmt->execute($data);
    }

    /**
     * Suppression entreprise (à gérer côté BDD : FK / ON DELETE, etc.)
     */
    public function deleteEntreprise(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM entreprises WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Pour alimenter les select secteurs dans les formulaires
     */
    public function getSecteurs(): array
    {
        $sql = "SELECT id, libelle FROM secteurs_entreprises ORDER BY libelle ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}