<?php

namespace App\Modules\Entreprise;

use PDO;

class EntrepriseRepository
{
    public function __construct(private PDO $pdo) {}

    public function createEntreprise(array $data): int
{
    $sql = "INSERT INTO entreprises 
           (gestionnaire_id, nom, secteur_id, adresse, code_postal, ville, pays, telephone, email, siret, site_web, taille, description, logo)
           VALUES
           (:gestionnaire_id, :nom, :secteur_id, :adresse, :code_postal, :ville, :pays, :telephone, :email, :siret, :site_web, :taille, :description, :logo)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($data);

    return (int)$this->pdo->lastInsertId();
}

    public function getAll(): array
    {
        return $this->pdo
            ->query("SELECT e.*, s.libelle AS secteur FROM entreprises e 
                     JOIN secteurs_entreprises s ON s.id = e.secteur_id
                     ORDER BY e.nom ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}