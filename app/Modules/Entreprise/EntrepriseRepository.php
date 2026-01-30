<?php

namespace App\Modules\Entreprise;

use PDO;

class EntrepriseRepository
{
    public function __construct(private PDO $pdo) {}

    /** Toutes les entreprises */
    public function getAll(): array
    {
        try {
            $sql = "SELECT e.*, u.nom AS gestionnaire_nom, u.prenom AS gestionnaire_prenom, s.libelle AS secteur
                    FROM entreprises e
                    LEFT JOIN users u ON u.id = e.gestionnaire_id
                    LEFT JOIN secteurs_entreprises s ON s.id = e.secteur_id
                    ORDER BY e.nom ASC";

            $stmt=$this->pdo->prepare($sql);
            $stmt->execute();
            $dataEntreprises = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null;

            return ['success' => true, 'data' => $dataEntreprises];

        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code'=>$e->getCode()];
        }
    }


    //Tous les secteurs d'activité
    public function getSecteurs(): array
    {
        try {
            $sql = "SELECT id, libelle FROM secteurs_entreprises ORDER BY libelle ASC";

            $stmt=$this->pdo->prepare($sql);
            $stmt->execute();
            $dataSecteurs = $stmt->fetchAll(PDO::FETCH_ASSOC)?:null;
         
        return ['success' => true, 'data' => $dataSecteurs];

        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code'=>$e->getCode()];
        }
    }


    //Trouver entreprise
    public function find(int $id): array
    {
        try{
        //infos entreprise + gestionnaire lie à l'entrrise + secteur
        $sql = "SELECT e.*, u.nom AS gestionnaire_nom, u.prenom AS gestionnaire_prenom, s.libelle AS secteur
                FROM entreprises e
                LEFT JOIN users u ON u.id = e.gestionnaire_id
                LEFT JOIN secteurs_entreprises s ON s.id = e.secteur_id
                WHERE e.id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        return ['success' => true, 'data' => $data];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(),'code'=>$e->getCode()];
        }
    }



    public function siretExists(string $siret): array
    {
        try {
            $sql = "SELECT id FROM entreprises WHERE siret = :siret LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':siret', $siret);
            $stmt->execute();

            $result = $stmt->fetch() !== false; // Retourne true si un enregistrement est trouvé

            return ['success' => true, 'exists' => $result]; // Renvoie true si le SIRET existe en base

        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code'=>$e->getCode()];
        }
    }

    public function siretExistsExceptId(int $entrepriseId, string $siret): array  // Pour update entreprise pour exclure l'entreprise elle-même eviter le faux doublon
    {
        try {    
            $sql = "SELECT id FROM entreprises WHERE siret = :siret AND id != :id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':siret', $siret);
            $stmt->bindParam(':id', $entrepriseId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch() !== false; // Retourne true si un enregistrement est trouvé

            return ['success' => true, 'exists' => $result];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(),'code'=>$e->getCode()];
        }


    }
    

    //Créer entreprise
    public function createEntreprise(array $data): array
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

        try {
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
            return ['success' => true, 'id' => (int)$this->pdo->lastInsertId()];

        } catch (\PDOException $e) { // Gestion erreur PDO
            return ['success' => false, 'error' => $e->getMessage(), 'code'=>$e->getCode()]; // Recupere message erreur pour debug
        }
    }


    /** Lien gestionnaire → entreprise */
    public function attachUserToEntreprise(int $userId, int $entrepriseId): array
    {
        $sql = "UPDATE users SET entreprise_id = :eid WHERE id = :uid";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':eid', $entrepriseId, PDO::PARAM_INT);
            $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(),'code'=>$e->getCode()];
        }
    }


    /** Modifier entreprise */
    public function updateEntreprise(int $id, array $data): array
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
        try {
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
            $stmt->execute();

            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(),'code'=>$e->getCode()];
        }
    }

    /** Supprimer entreprise */
    public function deleteEntreprise(int $id): array
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM entreprises WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute( );

            return  ['success' => true];

        } catch (\PdoException $e) {
            return ['success' => false, 'error' => $e->getMessage(),'code'=>$e->getCode()];
        }    
    }
}