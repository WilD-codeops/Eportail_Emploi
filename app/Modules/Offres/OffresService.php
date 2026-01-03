<?php

namespace App\Modules\Offres;

class OffresService
{
    public function __construct(
        private OffresRepository $repo,
        private OffresValidator $validator
    ) {}

    /** Liste publique paginée */
    public function listPublic(array $filters, int $page = 1, int $perPage = 10): array
    {
        $page = max(1, (int)$page);
        $perPage = min(50, max(1, (int)$perPage));

        $limit  = $perPage;
        $offset = ($page - 1) * $perPage;

        $items = $this->repo->getPublic($filters, $limit, $offset);
        $total = $this->repo->countPublic($filters);

        $totalPages = max(1, (int)ceil($total / $perPage));

        return [
            'success' => true,
            'data'    => [
                'items'      => $items,
                'pagination' => [
                    'page'       => $page,
                    'perPage'    => $perPage,
                    'total'      => $total,
                    'totalPages' => $totalPages
                ]
            ]
        ];
    }


    /** Détail public d'une offre active */
    public function showPublic(int $id): array
    {
        $offre = $this->repo->findActive($id);
        if (!$offre) {
            return ['success' => false, 'error' => 'Offre introuvable'];
        }

        return ['success' => true, 'data' => ['offre' => $offre]];
    }


    /** Liste admin (toutes offres) */
    public function listAdmin(): array
    {
        $items = $this->repo->getAll();
        return ['success' => true, 'data' => ['items' => $items]];
    }


    /** Liste par entreprise (gestionnaire/recruteur) */
    public function listByEntreprise(int $entrepriseId): array
    {
        $items = $this->repo->getByEntreprise($entrepriseId);
        return ['success' => true, 'data' => ['items' => $items]];
    }


    /** Données de référence (types, niveaux...) */
    public function getReferenceData(bool $isAdmin): array
    {
        $data = [
            'typesOffres'           => $this->repo->getTypesOffres(),
            'niveauxQualification'  => $this->repo->getNiveauxQualification(),
            'domainesEmploi'        => $this->repo->getDomainesEmploi(),
            'localisations'         => $this->repo->getLocalisations(),
        ];

        if ($isAdmin) {
            $data['entreprises'] = $this->repo->getEntreprises();
        }

        return ['success' => true, 'data' => $data];
    }


    /** Création d'offre */
    public function createOffre(array $data, int $auteurId, bool $isAdmin, int $entrepriseIdContext): array
    {
        $v = $this->validator->validate($data, $isAdmin);
        if (!$v['isValid']) {
            return [
                'success' => false,
                'error'   => 'Validation',
                'errors'  => $v['errors'],
                'data'    => ['input' => $v['clean']]
            ];
        }

        $clean = $v['clean'];

        // Contrôle de rattachement / restriction par entreprise
        if ($isAdmin) {
            $entrepriseId = isset($data['entreprise_id']) ? (int)$data['entreprise_id'] : 0;
            if ($entrepriseId <= 0) {
                return ['success' => false, 'error' => "entreprise_id requis pour admin"];
            }
        } else {
            $entrepriseId = $entrepriseIdContext;
        }

        $payload = [
            'auteur_id'               => $auteurId,
            'entreprise_id'           => $entrepriseId,
            'type_offre_id'           => $clean['type_offre_id'],
            'niveau_qualification_id' => $clean['niveau_qualification_id'],
            'domaine_emploi_id'       => $clean['domaine_emploi_id'],
            'localisation_id'         => $clean['localisation_id'],
            'titre'                   => $clean['titre'],
            'description'             => $clean['description'],
            'date_debut'              => $clean['date_debut'],
            'date_fin'                => $clean['date_fin'],
            'duree_contrat'           => $clean['duree_contrat'],
            'salaire'                 => $clean['salaire'],
            'statut'                  => $clean['statut'],
        ];

        $newId = $this->repo->create($payload);

        return ['success' => true, 'data' => ['id' => $newId]];
    }


    /** Mise à jour d'offre */
    public function updateOffre(int $id, array $data, int $auteurId, bool $isAdmin, int $entrepriseIdContext): array
    {
        $existing = $this->repo->find($id);
        if (!$existing) {
            return ['success' => false, 'error' => 'Offre introuvable'];
        }

        $v = $this->validator->validate($data, $isAdmin);
        if (!$v['isValid']) {
            return [
                'success' => false,
                'error'   => 'Validation',
                'errors'  => $v['errors'],
                'data'    => ['input' => $v['clean']]
            ];
        }

        $clean = $v['clean'];

        if ($isAdmin) {
            $entrepriseId = isset($data['entreprise_id']) ? (int)$data['entreprise_id'] : 0;
            if ($entrepriseId <= 0) {
                return ['success' => false, 'error' => "entreprise_id requis pour admin"];
            }

            $payload = [
                'auteur_id'               => $auteurId,
                'entreprise_id'           => $entrepriseId,
                'type_offre_id'           => $clean['type_offre_id'],
                'niveau_qualification_id' => $clean['niveau_qualification_id'],
                'domaine_emploi_id'       => $clean['domaine_emploi_id'],
                'localisation_id'         => $clean['localisation_id'],
                'titre'                   => $clean['titre'],
                'description'             => $clean['description'],
                'date_debut'              => $clean['date_debut'],
                'date_fin'                => $clean['date_fin'],
                'duree_contrat'           => $clean['duree_contrat'],
                'salaire'                 => $clean['salaire'],
                'statut'                  => $clean['statut'],
            ];

            $ok = $this->repo->update($id, $payload);
        } else {
            $payload = [
                'type_offre_id'           => $clean['type_offre_id'],
                'niveau_qualification_id' => $clean['niveau_qualification_id'],
                'domaine_emploi_id'       => $clean['domaine_emploi_id'],
                'localisation_id'         => $clean['localisation_id'],
                'titre'                   => $clean['titre'],
                'description'             => $clean['description'],
                'date_debut'              => $clean['date_debut'],
                'date_fin'                => $clean['date_fin'],
                'duree_contrat'           => $clean['duree_contrat'],
                'salaire'                 => $clean['salaire'],
                'statut'                  => $clean['statut'],
            ];

            // Restriction par entreprise / contrôle de rattachement
            $ok = $this->repo->updateOwned($id, $entrepriseIdContext, $payload);

            if (!$ok) {
                return ['success' => false, 'error' => 'Mise à jour impossible ou non autorisée'];
            }
        }

        return $ok ? ['success' => true] : ['success' => false, 'error' => 'Échec mise à jour'];
    }


    /** Suppression d'offre */
    public function deleteOffre(int $id, bool $isAdmin, int $entrepriseIdContext): array
    {
        $existing = $this->repo->find($id);
        if (!$existing) {
            return ['success' => false, 'error' => 'Offre introuvable'];
        }

        if ($isAdmin) {
            $ok = $this->repo->delete($id);
        } else {
            // Restriction par entreprise / contrôle de rattachement
            $ok = $this->repo->deleteOwned($id, $entrepriseIdContext);
        }

        if (!$ok) {
            return ['success' => false, 'error' => 'Suppression impossible ou non autorisée'];
        }

        return ['success' => true];
    }
}
