<?php

namespace App\Modules\Auth;

use PDO;

class AuthRepository
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?array
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

    public function emailExists(string $email): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return (bool) $stmt->fetch(); 
    }


    public function createUser(array $data): int
    {
        $sql = "INSERT INTO users 
               (prenom, nom, email, mot_de_passe, role, entreprise_id)
               VALUES (:prenom, :nom, :email, :mot_de_passe, :role, :entreprise_id)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':mot_de_passe', $data['mot_de_passe']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':entreprise_id', $data['entreprise_id']);

        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function createPasswordReset(int $userId, string $tokenHash, string $expiresAt): array
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash, :expires_at)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':token_hash' => $tokenHash,
                ':expires_at' => $expiresAt
            ]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    public function findValidPasswordReset(string $tokenHash): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, user_id
                FROM password_resets
                WHERE token_hash = :token_hash
                  AND used_at IS NULL
                  AND expires_at > NOW()
                ORDER BY id DESC
                LIMIT 1
            ");
            $stmt->execute([':token_hash' => $tokenHash]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            return ['success' => true, 'data' => $row ?: null];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    public function markPasswordResetUsed(int $resetId): array
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $resetId]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
    
    public function updateUserPassword(int $userId, string $hash): array
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET mot_de_passe = :hash WHERE id = :id");
            $stmt->execute([':hash' => $hash, ':id' => $userId]);
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }



}