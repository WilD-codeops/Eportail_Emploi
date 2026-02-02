<?php

namespace App\Modules\Auth;
use PDO;

class UserRepository
{
    public function __construct(private PDO $pdo) {}

    // Trouver un utilisateur par ID
    public function findUserById(int $id): ?array
    {
       try{
            $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

            return ['success' => true , 'data' => $user ?: null];
    
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    public function findUserByEmail(string $email): ?array
    {
        try{
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

            return ['success' => true , 'data' => $user ?: null];
    
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }   

    // Mise à jour de la date du dernier accès
    public function updateLastLogin(int $userId): array
    {
        try {
                $sql = "UPDATE users SET dernier_acces = NOW() WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
                $stmt->execute();
            
                return ['success' => true];
            } catch (\PDOException $e) {
                return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
            }
    }

    // Récupérer tous les utilisateurs
    public function getAllUsers(): array
    {
        try {
            $sql = "SELECT u.*, e.nom AS entreprise FROM users u LEFT JOIN entreprises e ON u.entreprise_id = e.id";
            $stmt = $this->pdo->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'data' => $users];
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    
    // Suppression utilisateur
    public function deleteUser(int $userId): array
    {
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    // Mise à jour des informations utilisateur
    public function updateUser(int $userId, array $data): array
    {
        try {
            $sql = "UPDATE users SET prenom = :prenom, nom = :nom, email = :email, mot_de_passe = :mot_de_passe, telephone = :telephone, role = :role, entreprise_id = :entreprise_id WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':mot_de_passe', $data['mot_de_passe']);
            $stmt->bindParam(':telephone', $data['telephone']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':entreprise_id', $data['entreprise_id']);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    // Création utilisateur
    public function createUser(array $data): array
    {
        $sql = "INSERT INTO users 
               (prenom, nom, email, mot_de_passe, telephone, role, entreprise_id,date_creation)
                VALUES (:prenom, :nom, :email, :mot_de_passe, :telephone, :role, :entreprise_id,NOW())";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':mot_de_passe', $data['mot_de_passe']);
            $stmt->bindParam(':telephone', $data['telephone']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':entreprise_id', $data['entreprise_id']);

            $stmt->execute();

            return ['success' => true , 'id' => (int) $this->pdo->lastInsertId()];
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }      
    }    

    // Compter le nombre total d'utilisateurs
    public function countUsers(): array
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM users";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'total' => (int)$result['total']];
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
// Compter le nombre d'utilisateurs par entreprise
    public  function countUsersByEntreprise(int $entrepriseId): array
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM users WHERE entreprise_id = :entreprise_id AND role IN ('gestionnaire','recruteur')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':entreprise_id', $entrepriseId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'count' => (int)$result['total']];
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }


    // Recherche d'utilisateurs avec filtres   
    public function searchUser(array $filters, int $limit = 20, int $offset = 0): array 
    {
        try {
            $sql = "SELECT SQL_CALC_FOUND_ROWS u.*, e.nom AS entreprise
                    FROM users u
                    LEFT JOIN entreprises e ON u.entreprise_id = e.id
                    WHERE 1=1";
    
            $params = [];
        
            if (!empty($filters['nom'])) {
                $sql .= " AND (u.prenom LIKE :nom OR u.nom LIKE :nom)";
                $params[':nom'] = '%' . $filters['nom'] . '%';
            }
        
            if (!empty($filters['email'])) {
                $sql .= " AND u.email LIKE :email";
                $params[':email'] = '%' . $filters['email'] . '%';
            }
        
            if (!empty($filters['role'])) {
                $sql .= " AND u.role = :role";
                $params[':role'] = $filters['role'];
            }
        
            if (!empty($filters['entreprise_id'])) {
                $sql .= " AND u.entreprise_id = :entreprise_id";
                $params[':entreprise_id'] = $filters['entreprise_id'];
            }
        
            if (!empty($filters['dernier_acces'])) {
                $sql .= " AND u.dernier_acces >= :dernier_acces";
                $params[':dernier_acces'] = $filters['dernier_acces'];
            }
        
            // Pagination
            $sql .= " LIMIT :offset, :limit";
        
            $stmt = $this->pdo->prepare($sql);
        
            // Bind des paramètres
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
        
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
            // Récupération du total filtré
            $total = $this->pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
        
            return [
                'success' => true,
                'data'    => $users,
                'total'   => (int)$total
            ];
        
        } catch (\PDOException $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'code'    => $e->getCode()
            ];
        }
    }

    public function countGestionnairesByEntreprise(int $entrepriseId): array
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM users WHERE entreprise_id = :entreprise_id AND role = 'gestionnaire'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':entreprise_id', $entrepriseId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'count' => (int)$result['total']];
        } catch (\PDOException $e) {
            return ['success' => false , 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    // Compter les utilisateurs avec filtres
    public function countFilteredUsers(array $filters = []): array
{
    try {
        $sql = "SELECT COUNT(*) AS total
                FROM users u
                WHERE 1=1";

        $params = [];

        if (!empty($filters['nom'])) {
            $sql .= " AND (u.prenom LIKE :nom OR u.nom LIKE :nom)";
            $params[':nom'] = '%' . $filters['nom'] . '%';
        }

        if (!empty($filters['email'])) {
            $sql .= " AND u.email LIKE :email";
            $params[':email'] = '%' . $filters['email'] . '%';
        }

        if (!empty($filters['role'])) {
            $sql .= " AND u.role = :role";
            $params[':role'] = $filters['role'];
        }

        if (!empty($filters['entreprise_id'])) {
            $sql .= " AND u.entreprise_id = :entreprise_id";
            $params[':entreprise_id'] = $filters['entreprise_id'];
        }

        if (!empty($filters['dernier_acces'])) {
            $sql .= " AND u.dernier_acces >= :dernier_acces";
            $params[':dernier_acces'] = $filters['dernier_acces'];
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        return [
            'success' => true,
            'total'   => $total
        ];

    } catch (\PDOException $e) {
        return [
            'success' => false,
            'error'   => $e->getMessage(),
            'code'    => $e->getCode()
        ];
    }
}

public function getAllEntreprises(): array
{
    try {
        $sql = "SELECT id, nom FROM entreprises ORDER BY nom ASC";
        $stmt = $this->pdo->query($sql);
        $entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['success' => true, 'data' => $entreprises];
    } catch (\PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
    }
}
}