<?php

namespace App\Modules\Auth;

use PDO;

class AuthService
{
    public function __construct(
        private AuthRepository $repo,
        private PDO $pdo
    ) {}

    // Connexion utilisateur
    public function login(string $email, string $password): array
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Email invalide'];
        }

        $user = $this->repo->findByEmail($email);
        if (!$user) {
            return ['success' => false, 'error' => 'Identifiants incorrects'];
        }

        if (!password_verify($password, $user['mot_de_passe'])) {
            return ['success' => false, 'error' => 'Identifiants incorrects'];
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        return ['success' => true, 'user' => $user];
    }


    // Inscription candidat simple
    public function registerCandidat(array $data): array
    {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => "Email invalide"];
        }

        if (strlen($data['mot_de_passe']) < 6) {
            return ['success' => false, 'error' => "Mot de passe trop court"];
        }

        if ($this->repo->emailExists($data['email'])) {
            return ['success' => false, 'error' => "Cet email existe déjà"];
        }

        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        $data['role'] = 'candidat';
        $data['entreprise_id'] = null;

        $id = $this->repo->createUser($data);

        return ['success' => true, 'id' => $id];
    }

    // Création utilisateur (utilisé pour céation gestionnaire pendant création entreprise)
    public function createUser(array $data): int
    {
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        return $this->repo->createUser($data);  
    }
}