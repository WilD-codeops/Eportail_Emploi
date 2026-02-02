<?php

namespace App\Modules\Entreprise;

use App\Core\Database;
use App\Modules\Auth\AuthService;
use PDO;

class EntrepriseService
{
    public function __construct(
        private EntrepriseRepository $repo,
        private AuthService $authService,
        private PDO $pdo
    ) {}

    /** Liste */
    public function listEntreprises(): array
    {
        $resultat = $this->repo->getAll();
        if ($errorSystem = $this->systemError($resultat, "Erreur système lors de la récupération des entreprises : ")) {//verif erreur systeme  
            return $errorSystem;
        }
        return $resultat;
    }

    /** Liste des gestionnaires (les entreprises sont liés a un gestionnaire) */
    public function listGestionnaires(): array
    {
        $resultat = $this->repo->getGestionnaires();
        if ($errorSystem = $this->systemError($resultat, "Erreur système lors de la récupération des gestionnaires : ")) {
            return $errorSystem;
        }
        return $resultat;
    }

    // Recherche entreprises avec filtres
    public function searchEntreprises(array $filters, int $limit, int $offset): array
    {
        $result = $this->repo->search($filters, $limit, $offset);
        if ($errorSystem = $this->systemError($result, "Erreur système lors de la recherche des entreprises : ")) {
            return $errorSystem;
        }
        return $result;
    }

    /** Secteurs */
    public function listSecteurs(): array
    {
        $resultat = $this->repo->getSecteurs();
        if ($errorSystem = $this->systemError($resultat, "Erreur système lors de la récupération des secteurs : ")) {
            return $errorSystem;
        }
        return $resultat;
    }

    /** Trouver entreprise */
    public function findEntreprise(int $id): ?array
    {
        $resultat = $this->repo->find($id);
        if ($errorSystem = $this->systemError($resultat, "Erreur système lors de la récupération de l'entreprise : ")) {
            return $errorSystem;
        }
        return $resultat;
    }

    public function listOffresByEntreprise(int $entrepriseId): array
    {
        $resultat = $this->repo->getOffresByEntreprise($entrepriseId);
        if ($errorSystem = $this->systemError($resultat, "Erreur système lors de la récupération des offres : ")) {
            return $errorSystem;
        }
        return $resultat;
    }
    

    /** SIRET deja enreistré en base*/
    public function siretExists(string $siret): array
    {
        $resultat = $this->repo->siretExists($siret);
        //verif erreur systeme
        if($errorSystem=$this->systemError($resultat,"Erreur système lors de la vérification du SIRET : ")){
            return $errorSystem;
        }
        return $resultat;
    }

    // Pour update entreprise exclure l'entreprise elle-même eviter le faux doublon
    public function siretExistsForOtherEntreprise(int $entrepriseId, string $siret): array 
    {
        $resultat = $this->repo->siretExistsExceptId($entrepriseId, $siret);
        if ($errorSystem=$this->systemError($resultat,"Erreur système lors de la vérification du SIRET : ")) {
            return $errorSystem;
        }
        return $resultat;
    }



    /* Créer entreprise + gestionnaire Inscription */
    public function createEntrepriseEtGestionnaire(array $entrepriseData, array $gestionnaireData): array
    {

        try {
            $this->pdo->beginTransaction();

            // 1) Création user gestionnaire
            $gestionnaireId = $this->authService->createUserSafe([
                                                            'prenom'       => $gestionnaireData['prenom'],
                                                            'nom'          => $gestionnaireData['nom'],
                                                            'email'        => $gestionnaireData['email'],
                                                            'mot_de_passe' => $gestionnaireData['mot_de_passe'],
                                                            'telephone'    => $gestionnaireData['telephone'],
                                                            'role'         => 'gestionnaire',
                                                            'entreprise_id'=> null
            ]);

            // 2) Création entreprise
            $entrepriseData['gestionnaire_id'] = $gestionnaireId['id'];
            $entrepriseId = $this->repo->createEntreprise($entrepriseData);
            // verif erreur systeme si echec création rollback
            if ($errorSystem= $this->systemError($entrepriseId, "Erreur système lors de la création de l'entreprise : ")) {
                $this->pdo->rollBack();
                return $errorSystem;
            }

            // 3) Lier user → entreprise
            $this->repo->attachUserToEntreprise($gestionnaireId['id'], $entrepriseId['id']);
            // verif erreur systeme si echec création rollback
            if ($errorSystem= $this->systemError($entrepriseId, "Erreur système lors de la liaison gestionnaire → entreprise : ")) {
                $this->pdo->rollBack();
                return $errorSystem;
            }

            $this->pdo->commit();
            return $this->success("Entreprise et gestionnaire créés avec succès.");

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'systemError' => true,
                'error' => "Erreur système lors de la création de l'entreprise et du gestionnaire.",
                'code' => $e->getCode()
            ];
        }
    }

    /** Modifier */
    public function updateEntreprise(int $id, array $data): array
    {
        $dataEntrepriseCanonique = [
            'nom'        => $data['nom'] ?? $data['nom_entreprise'] ?? null,
            'secteur_id' => $data['secteur_id'] ?? null,
            'adresse'    => $data['adresse'] ?? null,
            'code_postal'=> $data['code_postal'] ?? null,
            'ville'      => $data['ville'] ?? null,
            'pays'       => $data['pays'] ?? null,
            'telephone'  => $data['telephone'] ?? $data['telephone_entreprise'] ?? null,
            'email'      => $data['email'] ?? $data['email_entreprise'] ?? null,
            'siret'      => $data['siret'] ?? null,
            'site_web'   => $data['site_web'] ?? null,
            'taille'     => $data['taille'] ?? null,
            'description'=> $data['description'] ?? null,
            'logo'       => $data['logo'] ?? null,
        ];

        $oldEntreprise = $this->repo->find($id);
        if ($errorSystem = $this->systemError($oldEntreprise, "Erreur système lors de la récupération des données actuelles de l'entreprise : ")) {
            return $errorSystem;
        }
        if (empty($oldEntreprise['data'])) {
            return $this->fail("L'entreprise à mettre à jour n'existe pas.");
        }

        $oldEntrepriseData = $oldEntreprise['data'];
        // si données non fournies, garder les anciennes
        foreach ($dataEntrepriseCanonique as $key => $value) {
            if ($value === null) {
                $dataEntrepriseCanonique[$key] = $oldEntrepriseData[$key] ?? null;
            }
        } // fin
        
        // Validation données

        $valid = EntrepriseValidator::validateEntreprise($dataEntrepriseCanonique);
        if (!$valid['success']) return $valid;

        // Vérifier SIRET UNIQUE (entreprise)
        $siretCheck = $this->siretExistsForOtherEntreprise($id, $dataEntrepriseCanonique['siret']);
        if (isset($siretCheck['systemError']) && $siretCheck['systemError']) {
            // remonter l’erreur système
            return $siretCheck;
        }
        if (($siretCheck['success'] ?? false) && ($siretCheck['exists'] ?? false)) {
            // le SIRET est déjà utilisé par une autre entreprise
            return $this->fail("Ce SIRET est déjà enregistré.");
        }



        $ok = $this->repo->updateEntreprise($id, $dataEntrepriseCanonique);
        // verif erreur systeme
        if ($errorsystem=$this->systemError($ok,"Erreur système lors de la mise à jour de l'entreprise : ")) {
            return $errorsystem;
        }

        return $this->success("Entreprise mise à jour avec succès.");
    }


    
    /** Supprimer */
    public function deleteEntreprise(int $id): array
    {
        $result = $this->repo->deleteEntreprise($id);
        if($errorSystem=$this->systemError($result,"Erreur système lors de la suppression de l'entreprise : ")){
            return $errorSystem;
        }
        return  $this->success("Entreprise supprimée avec succès.");
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

    private function systemError($result,$msg){ // Vérification erreur système dans les retours des repository
        if(!$result['success']){
            return [
                'success' => false,
                'systemError'=>true,
                'error'=>($msg.$result['error'] )?? 'Erreur système inconnue',
                'code'=>$result['code']
            ];
        }
    }
    
}