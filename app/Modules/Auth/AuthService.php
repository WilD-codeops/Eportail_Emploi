<?php

declare(strict_types=1);

namespace App\Modules\Auth;

class AuthService
{
    public function __construct(private AuthRepository $repository) {}

    /**
     * Tente de connecter un utilisateur à partir de son email et mot de passe.
     *
     * @return bool true si la connexion réussit, false sinon.
     */
    public function login(string $email, string $password): bool
    {
        
        $user = $this->repository->findUserByEmail($email);

        if (!$user) {
            return false;
        }

        // Vérification du mot de passe hashé
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Si tout est OK, on stocke les infos  en session
        $_SESSION['user'] = [
            'id'   => $user['id'],
            'role' => $user['role']
        ];

        return true;
    }

    /**
     * Inscription d'un utilisateur.
     * Le mot de passe est hashé avant d'être envoyé au repository.
     */
    public function register(string $email, string $password, string $role): int
    {
        // Hash du mot de passe 
        $hash = password_hash($password, PASSWORD_DEFAULT);

        return $this->repository->createUser($email, $hash, $role);
    }

    /*Déconnexion: on détruit la session PHP.*/
    public function logout(): void
    {
        session_destroy();
    }
}