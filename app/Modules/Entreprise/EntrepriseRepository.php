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


    //Tous les secteurs d'activité
    public function getSecteurs(): array
    {
        return $this->pdo
            ->query("SELECT id, libelle FROM secteurs_entreprises ORDER BY libelle ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    //Trouver entreprise
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM entreprises WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }



    public function siretExists(string $siret): bool
    {
        $sql = "SELECT id FROM entreprises WHERE siret = :siret LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':siret', $siret);
        $stmt->execute();
        
        return $stmt->fetch() !== false; // Retourne true si un enregistrement est trouvé
    }

    public function siretExistsExceptId(int $entrepriseId, string $siret): bool  // Pour update entreprise pour exclure l'entreprise elle-même eviter le faux doublon
    {
        $sql = "SELECT id FROM entreprises WHERE siret = :siret AND id != :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':siret', $siret);
        $stmt->bindParam(':id', $entrepriseId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch() !== false; // Retourne true si un enregistrement est trouvé
    }
    

    //Créer entreprise
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

        $stmt->bindParam(':gestionnaire_id', $data['gestionnaire_id'], PDO::PARAM_INT);
        $stmt->bindParam(':nom', $data['nom'], PDO::PARAM_STR);
        $stmt->bindParam(':secteur_id', $data['secteur_id'], PDO::PARAM_INT);
        $stmt->bindParam(':adresse', $data['adresse'], PDO::PARAM_STR);
        $stmt->bindParam(':code_postal', $data['code_postal'], PDO::PARAM_STR);
        $stmt->bindParam(':ville', $data['ville'], PDO::PARAM_STR);
        $stmt->bindParam(':pays', $data['pays'], PDO::PARAM_STR);
        $stmt->bindParam(':telephone', $data['telephone'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':siret', $data['siret'], PDO::PARAM_STR);
        $stmt->bindParam(':site_web', $data['site_web'], PDO::PARAM_STR);
        $stmt->bindParam(':taille', $data['taille'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':logo', $data['logo'], PDO::PARAM_STR);

        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }


    /** Lien gestionnaire → entreprise */
    public function attachUserToEntreprise(int $userId, int $entrepriseId): void
    {
        $sql = "UPDATE users SET entreprise_id = :eid WHERE id = :uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':eid', $entrepriseId, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();
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
                    description = :description  ,
                    logo = :logo
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $data['nom'], PDO::PARAM_STR);
        $stmt->bindParam(':secteur_id', $data['secteur_id'], PDO::PARAM_INT);
        $stmt->bindParam(':adresse', $data['adresse'], PDO::PARAM_STR);
        $stmt->bindParam(':code_postal', $data['code_postal'], PDO::PARAM_STR);
        $stmt->bindParam(':ville', $data['ville'], PDO::PARAM_STR);
        $stmt->bindParam(':pays', $data['pays'], PDO::PARAM_STR);
        $stmt->bindParam(':telephone', $data['telephone'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':siret', $data['siret'], PDO::PARAM_STR);
        $stmt->bindParam(':site_web', $data['site_web'], PDO::PARAM_STR);
        $stmt->bindParam(':taille', $data['taille'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':logo', $data['logo'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /** Supprimer entreprise */
    public function deleteEntreprise(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM entreprises WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute( );
    }
}