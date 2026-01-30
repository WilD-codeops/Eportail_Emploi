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
        if ($this->systemError($resultat)) {//verif erreur systeme  
            return $this->systemError($resultat);
        }
        return $resultat;
    }

    /** Secteurs */
    public function listSecteurs(): array
    {
        $resultat = $this->repo->getSecteurs();
        if ($this->systemError($resultat)) {//verif erreur systeme
            return $this->systemError($resultat);
        }
        return $resultat;
    }

    /** Trouver entreprise */
    public function findEntreprise(int $id): ?array
    {
        $resultat = $this->repo->find($id);
        if ($this->systemError($resultat)) {
            return $this->systemError($resultat);
        }
        return $resultat;
    }
    

    /** SIRET deja enreistré en base*/
    public function siretExists(string $siret): array
    {
        $resultat = $this->repo->siretExists($siret);
        if ($this->systemError($resultat)) {
            return $this->systemError($resultat);
        }
        return $resultat;
    }

    // Pour update entreprise exclure l'entreprise elle-même eviter le faux doublon
    public function siretExistsForOtherEntreprise(int $entrepriseId, string $siret): array 
    {
        $resultat = $this->repo->siretExistsExceptId($entrepriseId, $siret);
        if ($this->systemError($resultat)) {
            return $this->systemError($resultat);
        }
        return $resultat;
    }



    /* Créer entreprise + gestionnaire Inscription */
    public function createEntrepriseEtGestionnaire(array $entrepriseData, array $gestionnaireData): array
    {

        try {
            $this->pdo->beginTransaction();

            // 1) Création user gestionnaire
            $gestionnaireId = $this->authService->createUser([
                'prenom'       => $gestionnaireData['prenom'],
                'nom'          => $gestionnaireData['nom'],
                'email'        => $gestionnaireData['email'],
                'mot_de_passe' => $gestionnaireData['mot_de_passe'],
                'role'         => 'gestionnaire',
                'entreprise_id'=> null
            ]);

            // 2) Création entreprise
            $entrepriseData['gestionnaire_id'] = $gestionnaireId;
            $entrepriseId = $this->repo->createEntreprise($entrepriseData);

            // 3) Lier user → entreprise
            $this->repo->attachUserToEntreprise($gestionnaireId, $entrepriseId['id']);

            $this->pdo->commit();
            return $this->success("Entreprise et gestionnaire créés avec succès.");

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            return $this->fail("Erreur lors de la création de l'entreprise et du gestionnaire : " . $e->getMessage());
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

        $valid = EntrepriseValidator::validateEntreprise($dataEntrepriseCanonique);
        if (!$valid['success']) return $valid;

        // Vérifier SIRET UNIQUE (entreprise)
        if ($this->siretExistsForOtherEntreprise($id, $dataEntrepriseCanonique['siret'])) {
            return $this->fail("Ce SIRET est déjà enregistré.");
        }

        $ok = $this->repo->updateEntreprise($id, $dataEntrepriseCanonique);

        if ($this->systemError($ok)) {
            return $this->systemError($ok);
        }

        return $this->success("Entreprise mise à jour avec succès.");
    }


    
    /** Supprimer */
    public function deleteEntreprise(int $id): array
    {
        $result = $this->repo->deleteEntreprise($id);
        if ($this->systemError($result)) {
            return $this->systemError($result);
        }
        return  $this->success("Entreprise supprimée avec succès.");
    }
    
    
    private function fail(string $msg): array
    {
        return ['success' => false, 'error' => $msg];
    }

    private function success(string $msg): array
    {
        return ['success' => true,'message'=>$msg];
    }

    private function systemError($result){
        if(!$result['success']){
            return [
                'success' => false,
                'systemError'=>true,
                'error'=>$result['error'] ?? 'Erreur système inconnue',
                'code'=>$result['code']
            ];
        }
    }
    
}