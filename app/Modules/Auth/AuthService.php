<?php

declare(strict_types=1);

namespace App\Modules\Auth;

class AuthService
{
    public function __construct(private AuthRepository $repository) {}

    public function register(string $email, string $password, string $confirm, string $role = 'candidat'): array
    {
        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if ($password !== $confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if ($this->repository->findUserByEmail($email)) {
            $errors[] = 'Un compte existe déjà avec cet email.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $inserted = $this->repository->createUser($email, $hash, $role);

        if (!$inserted) {
            return ['success' => false, 'errors' => ['Erreur interne : impossible de créer le compte.']];
        }

        return ['success' => true];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->repository->findUserByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
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

