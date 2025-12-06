<?php

declare(strict_types=1);

namespace App\Modules\Auth;
use PDO;
class AuthService
{
    public function __construct(
        private AuthRepository $repository,
        private PDO $pdo  
        ) {}

    public function register(string $email, string $mot_de_passe, string $confirm, string $role = 'candidat'): array
    {
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }

        if (strlen($mot_de_passe) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if ($mot_de_passe !== $confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if ($this->repository->findUserByEmail($email)) {
            $errors[] = 'Un compte existe déjà avec cet email.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $inserted = $this->repository->createUser($email, $hash, $role);

        if (!$inserted) {
            return ['success' => false, 'errors' => ['Erreur interne : impossible de créer le compte.']];
        }

        return ['success' => true];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->repository->findUserByEmail($email);

        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            return [
                'success' => false,
                'error' => 'Identifiants incorrects.',
            ];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        return ['success' => true];
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

