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
        return $this->repo->getAll();
    }

    /** Secteurs */
    public function listSecteurs(): array
    {
        return $this->repo->getSecteurs();
    }

    /** Trouver entreprise */
    public function findEntreprise(int $id): ?array
    {
        return $this->repo->find($id);
    }
    

    /** SIRET deja enreistré en base*/
    public function siretExists(string $siret): bool
    {
        return $this->repo->siretExists($siret);
    }

    // Pour update entreprise exclure l'entreprise elle-même eviter le faux doublon
    public function siretExistsForOtherEntreprise(int $entrepriseId, string $siret): bool 
    {
        return $this->repo->siretExistsExceptId($entrepriseId, $siret);
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
            $this->repo->attachUserToEntreprise($gestionnaireId, $entrepriseId);

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
        if ($this->siretExists($dataEntrepriseCanonique['siret'])) {
            return $this->fail("Ce SIRET est déjà enregistré.");
        }

        $ok = $this->repo->updateEntreprise($id, $dataEntrepriseCanonique);

        return $ok ? $this->success("Entreprise mise à jour avec succès.") : $this->fail("Erreur lors de la mise à jour de l'entreprise.");
    }


    
    /** Supprimer */
    public function deleteEntreprise(int $id): array
    {
        $ok = $this->repo->deleteEntreprise($id);
        return $ok ? $this->success("Entreprise supprimée avec succès.") : $this->fail("Erreur lors de la suppression de l'entreprise.");
    }
    
    private function fail(string $msg): array
    {
        return ['success' => false, 'error' => $msg];
    }

    private function success(string $msg): array
    {
        return ['success' => true,'message'=>$msg];
    }
}