<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use PDO;

class AuthRepository
{
    public function __construct(private PDO $db) {}

    public function findUserByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createUser(string $email, string $passwordHash, string $role): int
    {
        $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $passwordHash, $role]);

        return (int) $this->db->lastInsertId();
    }
}