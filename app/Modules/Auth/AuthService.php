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

        if ($this->repository->findByEmail($email)) {
            $errors[] = 'Un compte existe déjà avec cet email.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $inserted = $this->repository->createUser([
            'email'       => $email,
            'mot_de_passe'=> $hash,
            'role'        => $role
        ]); 

        if (!$inserted) {
            return ['success' => false, 'errors' => ['Erreur interne : impossible de créer le compte.']];
        }

        return ['success' => true];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->repository->findByEmail($email);

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


    
}

