<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use PDO;

class AuthRepository
{
    public function __construct(private PDO $pdo) {}

    public function findUserByEmail(string $email): ?array
    {
        $sql = "SELECT id, email, mot_de_passe, role
                FROM users
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function createUser(string $email, string $passwordHash, string $role): bool
    {
        $sql = "INSERT INTO users (email, mot_de_passe, role, created_at)
                VALUES (:email, :mot_de_passe, :role, NOW())";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':mot_de_passe', $passwordHash, PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);

        return $stmt->execute();
    }
}


