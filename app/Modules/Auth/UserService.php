<?php

namespace App\Modules\Auth;

use PDO;
use App\Core\Auth;
use App\Core\Validator;
use App\Modules\Auth\UserValidator;

class UserService
{
    private UserRepository $repo;// Déclaration de la propriété du repository 
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo; // Injection de la dependance du repository dans le service sinon on ne peut pas l'utiliser
    }


    /**
     * Récupérer tous les utilisateurs (liste)
     */
    public function listUsers(): array
    {
        $resultat = $this->repo->getAllUsers();// Appel de la méthode du repository pour récupérer tous les utilisateurs
        if($errorSystem = $this->systemError($resultat, "Erreur système lors de la récupération des utilisateurs : ")){  //verif erreur systeme et retour si erreur  
            return $errorSystem;
        }
        return $resultat; 
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function getUser(int $id): array
    {
       $resultat = $this->repo->findUserById($id);
       if($errorSystem = $this->systemError($resultat, "Erreur système lors de la récupération de l'utilisateur : ")){  //verif erreur systeme et retour si erreur  
            return $errorSystem;
        }
        return $resultat;
    }

    public function countUsers(): array
    {
        $resultat = $this->repo->countUsers();
        if($errorSystem = $this->systemError($resultat, "Erreur système lors du comptage des utilisateurs : ")){  //verif erreur systeme et retour si erreur  
            return $errorSystem;
        }
        return $resultat;
    }

    /**
     * Créer un utilisateur avec règles métier
     * - Admin peut créer tout le monde
     * - Gestionnaire peut créer uniquement des recruteurs
     * - Gestionnaire limité à 3 gestionnaires max dans son entreprise
     */
    public function createUser(array $data): array
    {
        // Normalisation des clés de données

        $canonicDataUser=[
            'prenom'    => Validator::sanitize($data['prenom']?? ['prenom_utilisateur'] ?? null),
            'nom'       =>      Validator::sanitize($data['nom']?? ['nom_utilisateur'] ?? null),
            'email'     =>      strtolower(Validator::sanitize($data['email']?? ['email_utilisateur'] ?? null)),
            'telephone' =>      Validator::sanitize($data['telephone']?? ['telephone_utilisateur'] ?? null),
            'mot_de_passe'  =>      $data['mot_de_passe']?? ['mot_de_passe_utilisateur'] ?? null,
            'role'          =>      Validator::sanitize($data['role']?? ['role_utilisateur'] ?? null),
            'entreprise_id'  =>     Validator::sanitize($data['entreprise_id']?? ['entreprise_id_utilisateur'] ?? null),
        ];

        // Validation des données entrées par l'utilisateur
        $validationResult = UserValidator::validateCreate($canonicDataUser);
        if (!$validationResult['success']) {
            return $validationResult; // Retourner l'erreur de validation
        }

        $data = $validationResult['data']; // Données validées

    /**
     * REGLES: QUI PEUT CREER QUOI  ET POUR QUI ? 
     */
        //Seul unadmin peut créer des admins
        if (Auth::role() !=='admin' && $data['role'] ==='admin') {
            return $this->fail("Seul un administrateur peut créer un autre administrateur.");
        }
        
        // Règle métier : un gestionnaire ne peut créer que des recruteurs
        if (Auth::role() === 'gestionnaire' && $data['role'] !== 'recruteur') {
            return $this->fail("Un gestionnaire ne peut créer que des recruteurs.");
        }

        //Regle metier : un gestionnaire ne peut pas créer d'utilisateur hors de son entreprise
        if (Auth::role() === 'gestionnaire' && $data['entreprise_id'] !== Auth::entrepriseId()) {
            return $this->fail("Un gestionnaire ne peut créer des utilisateurs que pour son entreprise.");
        }

    /**
     * REGLES/ COMBIEN D'UTILISATEURS PEUT ON CREER ?
     */

        //Règle métier : Limite du nombre de personnel par entreprise
        $totalMaxMembers = 4; // Limite de 4 utilisateurs par entreprise
        $totalMaxGestionnaires = 2; // Limite de 2 gestionnaires par entreprise
        
        // Vérifier le nombre d'utilisateurs déjà existants pour l'entreprise
        $countMembers = $this->repo->countUsersByEntreprise($data['entreprise_id']);
        if ($countMembers['success'] && $countMembers['count'] >= $totalMaxMembers) {
            return $this->fail("Limite de $totalMaxMembers utilisateurs atteinte pour cette entreprise.");
        }
        // Vérifier email déjà utilisé
        $resultat = $this->repo->findUserByEmail($data['email']);
        if ($resultat['success'] && $resultat  ['data']) {
            return $this->fail("L'email est déjà utilisé par un autre utilisateur.");
        }


        // Vérifier le nombre de gestionnaires si le rôle de l'utilisateur créé est gestionnaire
        if ($data['role'] === 'gestionnaire') {
            $count = $this->repo->countGestionnairesByEntreprise($data['entreprise_id']);
            if ($count['success'] && $count['count'] >= $totalMaxGestionnaires) {
                return $this->fail("Limite de $totalMaxGestionnaires gestionnaires atteinte pour cette entreprise.");
            }
        }


        // Hash du mot de passe
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        
       // verif erreur systeme avant création
        $created = $this->repo->createUser($data);
        if ($errorSystem = $this->systemError($created, "Erreur système lors de la création de l'utilisateur : ")) {
            return $errorSystem;
        }
        
        // Création en base
        return $created;
    }

    /**
     * Mettre à jour un utilisateur
     * - On récupère l'ancien utilisateur
     * - On remplace uniquement les champs modifiés
     * - On applique les règles métier
     */
    public function updateUser(int $id, array $data): array
    {
        // Normalisation des clés de données
        $canonicDataUser=[
            'prenom'    => Validator::sanitize($data['prenom']?? null),
            'nom'       =>      Validator::sanitize($data['nom']?? null),
            'email'     =>      strtolower(Validator::sanitize($data['email']?? null)),
            'telephone' =>      Validator::sanitize($data['telephone']?? null),
            'mot_de_passe'  =>      $data['mot_de_passe']?? null,
            'role'          =>      Validator::sanitize($data['role']?? null),
            'entreprise_id'  =>     Validator::sanitize($data['entreprise_id']?? null),

        ];

        // Validation des données entrées par l'utilisateur
        $validationResult = UserValidator::validateUpdate($canonicDataUser);
        if (!$validationResult['success']) {
            return $validationResult; // Retourner l'erreur de validation
        }

        // Récupérer l'ancien utilisateur
        $old = $this->repo->findUserById($id);

        // 1. Vérifier erreur système
        if ($errorSystem = $this->systemError($old, "Erreur système lors de la récupération de l'utilisateur : ")) {
            return $errorSystem; // ['success' => false, 'error' => 'Erreur système...']
        }

        // 2. Règle métier : utilisateur introuvable
        if (empty($old['data'])) {
            return $this->fail("Utilisateur introuvable.");
        }

        // 3. On récupère les données
        $old = $old['data'];


        /* ============================
   REGLES : QUI PEUT MODIFIER QUI ?
   ============================ */

        // 1. Seul un admin peut modifier un admin
        if (Auth::role() !== 'admin' && $old['role'] === 'admin') {
            return $this->fail("Vous ne pouvez pas modifier un administrateur.");
        }

        // 2. Gestionnaire : restrictions fortes
        if (Auth::role() === 'gestionnaire') {
        
            // 2.1 Il ne peut modifier que recruteur ou gestionnaire
            if (!in_array($old['role'], ['recruteur', 'gestionnaire'])) {
                return $this->fail("Vous ne pouvez modifier que des recruteurs ou gestionnaires.");
            }
        
            // 2.2 Il ne peut modifier que dans sa propre entreprise
            if ($old['entreprise_id'] !== Auth::entrepriseId()) {
                return $this->fail("Vous ne pouvez modifier que des utilisateurs de votre entreprise.");
            }
        
            // 2.3 Il ne peut attribuer que recruteur ou gestionnaire
            if (!in_array($data['role'], ['recruteur', 'gestionnaire'])) {
                return $this->fail("Un gestionnaire ne peut attribuer que les rôles de recruteur ou gestionnaire.");
            }
        }

        // 3. Recruteur / candidat : ne peuvent modifier que leur propre profil
        if (in_array(Auth::role(), ['recruteur', 'candidat']) && $old['id'] !== Auth::userId()) {
            return $this->fail("Vous ne pouvez modifier que votre propre profil.");
        }

        // 4. Seul un admin peut modifier l'entreprise d'un utilisateur
        if (Auth::role() !== 'admin' && $data['entreprise_id'] !== $old['entreprise_id']) {
            return $this->fail("Vous ne pouvez modifier que votre entreprise.");
        }

        // 5. Seul un admin peut modifier le mot de passe d’un autre utilisateur
        if (Auth::role() !== 'admin' && !empty($data['mot_de_passe']) && $old['id'] !== Auth::userId()) {
            return $this->fail("Vous ne pouvez modifier le mot de passe que pour votre propre compte.");
        }

        // Conserver les anciennes valeurs si un champ est vide
        $data['prenom'] = $data['prenom'] ?? $old['prenom'];
        $data['nom'] = $data['nom'] ?? $old['nom'];
        $data['email'] = $data['email'] ?? $old['email'];
        $data['telephone'] = $data['telephone'] ?? $old['telephone'];
        $data['role'] = $data['role'] ?? $old['role'];
        $data['entreprise_id'] = $data['entreprise_id'] ?? $old['entreprise_id'];
        $data['mot_de_passe'] = $data['mot_de_passe'] ? password_hash($data['mot_de_passe'], PASSWORD_DEFAULT) : $old['mot_de_passe'];// Hash du mot de passe si modifié, sinon conserver l'ancien


        // Mise à jour en base
        $Updated = $this->repo->updateUser($id, $data);

        // Vérification erreur système avant retour
        if ($errorSystem = $this->systemError($Updated, "Erreur système lors de la mise à jour de l'utilisateur : ")) {
            return $errorSystem;
        }
        return $this->success("Utilisateur mis à jour avec succès.");
    }

    /**
     * Supprimer un utilisateur
     * - Impossible de supprimer le dernier gestionnaire d'une entreprise
     */
    public function deleteUser(int $id): array
{
    /* ============================
       1. Récupération de l'utilisateur
       ============================ */
    $user = $this->repo->findUserById($id);

    // Erreur système
    if ($errorSystem = $this->systemError($user, "Erreur système lors de la récupération de l'utilisateur : ")) {
        return $errorSystem;
    }

    // Introuvable (métier)
    if (empty($user['data'])) {
        return $this->fail("Utilisateur introuvable.");
    }

    $user = $user['data'];


    /* ============================
       2. RÈGLES : QUI PEUT SUPPRIMER QUI ?
       ============================ */

    $connectedRole = Auth::role();
    $connectedEntreprise = Auth::entrepriseId();


    // 2.1 Seul un admin peut supprimer un admin
    if ($user['role'] === 'admin' && $connectedRole !== 'admin') {
        return $this->fail("Vous ne pouvez pas supprimer un administrateur.");
    }

    // 2.2 Un recruteur ou candidat ne peut supprimer personne
    if (in_array($connectedRole, ['recruteur', 'candidat'])) {
        return $this->fail("Vous n'avez pas l'autorisation de supprimer un utilisateur.");
    }

    // 2.3 Un gestionnaire ne peut supprimer que dans son entreprise
    if ($connectedRole === 'gestionnaire' && $user['entreprise_id'] !== $connectedEntreprise) {
        return $this->fail("Vous ne pouvez supprimer que des utilisateurs de votre entreprise.");
    }

    // 2.4 Un gestionnaire ne peut supprimer que recruteur ou gestionnaire
    if ($connectedRole === 'gestionnaire' && !in_array($user['role'], ['recruteur', 'gestionnaire'])) {
        return $this->fail("Vous ne pouvez supprimer que des recruteurs ou gestionnaires.");
    }


    /* ============================
       3. Règle métier : ne pas supprimer le dernier gestionnaire
       ============================ */
    if ($user['role'] === 'gestionnaire') {

        $count = $this->repo->countGestionnairesByEntreprise($user['entreprise_id']);

        // Erreur système
        if ($errorSystem = $this->systemError($count, "Erreur système lors du comptage des gestionnaires : ")) {
            return $errorSystem;
        }

        if ($count['count'] <= 1) {
            return $this->fail("Impossible de supprimer le dernier gestionnaire de l'entreprise.");
        }
    }


    /* ============================
       4. Suppression
       ============================ */
    $delete = $this->repo->deleteUser($id);

    if ($errorSystem = $this->systemError($delete, "Erreur système lors de la suppression de l'utilisateur : ")) {
        return $errorSystem;
    }

    return $this->success("Utilisateur supprimé avec succès.");
}

    /**
     * Recherche avec filtres
     */
    public function search(array $filters, $limit, $offset): array
    {
        $resultat = $this->repo->searchUser($filters, $limit, $offset);
        if ($errorSystem = $this->systemError($resultat, "Erreur système lors de la recherche des utilisateurs : ")) {
            return $errorSystem;
        }
        return $resultat;
    }

    // Comptage avec filtres
    public function countFiltered(array $filters = []): array
    {
        $result = $this->repo->countFilteredUsers($filters);

        if ($errorSystem = $this->systemError($result, "Erreur système lors du comptage des utilisateurs : ")) {
            return $errorSystem;
        }

        return $result;
    }


    /**
     * FONCTION UTILITAIRES POUR GERER LES ERREURS 
     */
    private function fail(string $msg): array // Retour d'erreur métier
    {
        return ['success' => false, 'error' => $msg];
    }

    private function success(string $msg): array // Retour succès métier
    {
        return ['success' => true,'message'=>$msg];
    }

    private function systemError($result, $msg){ // Vérification erreur système dans les retours des repository
        if(!$result['success']){
            return [
                'success' => false,
                'systemError'=>true,
                'error'=>($msg . $result['error']) ?? 'Erreur système inconnue', // concaténer message personnalisé et message d'erreurpour plus de clarté
                'code'=>$result['code']
            ];
        }
    }


 public function getAllEntreprises(): array
{
    $result = $this->repo->getAllEntreprises();
    if ($errorSystem = $this->systemError($result, "Erreur système lors de la récupération des entreprises : ")) {
        return $errorSystem;
    }
    return $result;
}

}