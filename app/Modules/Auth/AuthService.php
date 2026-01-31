<?php

namespace App\Modules\Auth;

use PDO;
use App\Core\SessionManager;
use App\Modules\Candidat\ProfilCandidatRepository;
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
            return $this->fail("Email invalide.");
        }

        $user = $this->repo->findByEmail($email);
        if (!$user) {
            return $this->fail("email incorrect");
        }

        if (!password_verify($password, $user['mot_de_passe'])) {
            return $this->fail("Mot de passe incorrect");
        }

        // demarrage session sécurisée
        SessionManager::startSession();
        SessionManager::regenerateSessionId();
        
        // Stockage des informations utilisateur utiles et non sensibles en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = strtolower($user['role']);
        $_SESSION['user_prenom'] =  ucfirst(strtolower($user['prenom'])); 
        $_SESSION['entreprise_id'] = $user['entreprise_id'] ?? null;
        
        $_SESSION['created_at'] = time();
        $_SESSION['last_activity'] = time();

        return $this->success("Connexion réussie.");
    }

    public function emailExists(string $email): bool
    {
        return $this->repo->emailExists($email);
    }


    // Inscription candidat simple

    public function registerCandidatAvecProfil(array $candidatData, array $profilData): array
    {
        try {
            $this->pdo->beginTransaction();

            // 1) créer l’utilisateur (safe)
            $created = $this->createUserSafe($candidatData);
            if (!$created['success']) {
                $this->pdo->rollBack();
                return $created; // erreur système normalisée
            }

            $candidatId = (int)$created['id'];

            // 2) créer le profil
            $profilRepo = new ProfilCandidatRepository($this->pdo);

            $profilData['candidat_id'] = $candidatId;
            $createdProfil = $profilRepo->createProfil($profilData);

            if (!$createdProfil['success']) {
                $this->pdo->rollBack();
                // on normalise comme entreprise
                return [
                    'success' => false,
                    'systemError' => true,
                    'error' => $createdProfil['error'] ?? 'Erreur système profil candidat',
                    'code' => $createdProfil['code'] ?? null
                ];
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Compte candidat et profil créés avec succès.",
                'id' => $candidatId
            ];

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'systemError' => true,
                'error' => "Erreur système lors de l'inscription du candidat.",
                'code' => $e->getCode()
            ];
        }
    }


    // Création utilisateur (utilisé pour céation gestionnaire pendant création entreprise)
    public function createUser(array $data): int
    {
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        return $this->repo->createUser($data);  
    }

    public function createUserSafe(array $data): array  // renvoie tableau avec success true/false et id ou message d'erreur le temps de traiter les erreurs
{
    try {
        $id = $this->repo->createUser($data); // renvoie int
        return ['success' => true, 'id' => $id];
    } catch (\PDOException $e) {
        return ['success' => false, 'systemError' => true, 'error' => $e->getMessage(), 'code' => $e->getCode()];
    }
}

    private function fail(string $msg): array
    {
        return ['success' => false, 'error' => $msg];
    }

    private function success(string $msg): array
    {
        return ['success' => true, 'message' => $msg];
    }
}