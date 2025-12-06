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

     public function createUser(array $data): int
    {   // Sécurisation + validation minimale   
        if (empty($data['email']) || empty($data['mot_de_passe']) || empty($data['role'])) {
            throw new \InvalidArgumentException("Champs requis manquants pour créer l'utilisateur.");
        }

        // Hash du mot de passe
        $hash = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (prenom, nom, email, mot_de_passe, role, entreprise_id)
                VALUES (:prenom, :nom, :email, :mot_de_passe, :role, :entreprise_id)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':prenom'         => $data['prenom'] ?? null,
            ':nom'            => $data['nom'] ?? null,
            ':email'          => $data['email'],
            ':mot_de_passe'   => $hash,
            ':role'           => $data['role'],
            ':entreprise_id'  => $data['entreprise_id'] ?? null  // utile pour recruteurs plus tard
        ]);

        return (int) $this->pdo->lastInsertId();
    }  
}


