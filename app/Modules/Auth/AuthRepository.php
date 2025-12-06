<?php

namespace App\Modules\Auth;

use PDO;

class AuthRepository
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
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
}