<?php

namespace App\Modules\Offres;

use PDO;

class OffresRepository
{
    public function __construct(private PDO $pdo) {}

    /** Offres publiques avec filtres */
    public function getPublic(array $filters, int $limit, int $offset): array
    {
        $sql = "SELECT o.*, e.nom AS entreprise_nom, l.ville AS localisation,
                       t.code AS type_offre_code, t.description AS type_offre_description
                FROM offres o
                JOIN entreprises e ON e.id = o.entreprise_id
                LEFT JOIN localisations l ON l.id = o.localisation_id
                LEFT JOIN types_offres t ON t.id = o.type_offre_id
                WHERE o.statut = 'active'";

        $params = [];

        if (!empty($filters['keyword'])) {
            $sql .= " AND (o.titre LIKE :kw1 OR o.description LIKE :kw2)";
            $params[':kw1'] = '%' . $filters['keyword'] . '%';
            $params[':kw2'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['localisation_id'])) {
            $sql .= " AND o.localisation_id = :loc";
            $params[':loc'] = (int)$filters['localisation_id'];
        }

        if (!empty($filters['type_offre_id'])) {
            $sql .= " AND o.type_offre_id = :type";
            $params[':type'] = (int)$filters['type_offre_id'];
        }

        if (!empty($filters['entreprise_id'])) {
            $sql .= " AND o.entreprise_id = :eid";
            $params[':eid'] = (int)$filters['entreprise_id'];
        }

        if (!empty($filters['domaine_emploi_id'])) {
            $sql .= " AND o.domaine_emploi_id = :dom";
            $params[':dom'] = (int)$filters['domaine_emploi_id'];
        }

        // MySQL n'aime pas toujours les placeholders nommés pour LIMIT/OFFSET avec emulate_prepares désactivé.
        // On insère des entiers castés pour éviter HY093.
        $orderBy = "o.date_debut DESC, o.id DESC";
        if (($filters['tri'] ?? '') === 'oldest') {
            $orderBy = "o.date_debut ASC, o.id ASC";
        } elseif (($filters['tri'] ?? '') === 'newest') {
            $orderBy = "o.date_debut DESC, o.id DESC";
        }

        $sql .= " ORDER BY {$orderBy}
                  LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if (in_array($key, [':loc', ':type', ':eid', ':dom'], true)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Compte pour pagination publique */
    public function countPublic(array $filters): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM offres o
                WHERE o.statut = 'active'";

        $params = [];

        if (!empty($filters['keyword'])) {
            $sql .= " AND (o.titre LIKE :kw1 OR o.description LIKE :kw2)";
            $params[':kw1'] = '%' . $filters['keyword'] . '%';
            $params[':kw2'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['localisation_id'])) {
            $sql .= " AND o.localisation_id = :loc";
            $params[':loc'] = (int)$filters['localisation_id'];
        }

        if (!empty($filters['type_offre_id'])) {
            $sql .= " AND o.type_offre_id = :type";
            $params[':type'] = (int)$filters['type_offre_id'];
        }

        if (!empty($filters['entreprise_id'])) {
            $sql .= " AND o.entreprise_id = :eid";
            $params[':eid'] = (int)$filters['entreprise_id'];
        }

        if (!empty($filters['domaine_emploi_id'])) {
            $sql .= " AND o.domaine_emploi_id = :dom";
            $params[':dom'] = (int)$filters['domaine_emploi_id'];
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if (in_array($key, [':loc', ':type', ':eid', ':dom'], true)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }


    /** Toutes les offres (admin) */
    public function getAll(): array
    {
        $sql = "SELECT 
                    o.*, 
                    e.nom AS entreprise_nom, 
                    l.ville AS localisation,
                    t.code AS type_offre_code, 
                    t.description AS type_offre_description,

                    ua.prenom AS auteur_nom,
                    ua.role   AS auteur_role,

                    um.prenom AS modifie_nom,
                    um.role   AS modifie_role

                FROM offres o
                JOIN entreprises e ON e.id = o.entreprise_id
                LEFT JOIN localisations l ON l.id = o.localisation_id
                LEFT JOIN types_offres t ON t.id = o.type_offre_id

                LEFT JOIN users ua ON ua.id = o.auteur_id
                LEFT JOIN users um ON um.id = o.modifie_par

                ORDER BY o.date_debut DESC, o.id DESC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }



    /** Offres par entreprise (gestionnaire/recruteur) */
    public function getByEntreprise(int $entrepriseId): array
    {
        $sql = "SELECT 
                    o.*, 
                    e.nom AS entreprise_nom, 
                    l.ville AS localisation,
                    t.code AS type_offre_code, 
                    t.description AS type_offre_description,

                    ua.prenom AS auteur_nom,
                    ua.role   AS auteur_role,

                    um.prenom AS modifie_nom,
                    um.role   AS modifie_role

                FROM offres o
                JOIN entreprises e ON e.id = o.entreprise_id
                LEFT JOIN localisations l ON l.id = o.localisation_id
                LEFT JOIN types_offres t ON t.id = o.type_offre_id

                LEFT JOIN users ua ON ua.id = o.auteur_id
                LEFT JOIN users um ON um.id = o.modifie_par

                WHERE o.entreprise_id = :eid
                ORDER BY o.date_debut DESC, o.id DESC " ;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['eid' => $entrepriseId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /** Trouver une offre (détail complet) */
    public function find(int $id): ?array
    {
        $sql = "SELECT o.*, e.nom AS entreprise_nom, l.ville AS localisation,
                       t.code AS type_offre_code, t.description AS type_offre_description,
                       nq.libelle AS niveau_qualification, d.nom AS domaine_emploi
                FROM offres o
                JOIN entreprises e ON e.id = o.entreprise_id
                LEFT JOIN localisations l ON l.id = o.localisation_id
                LEFT JOIN types_offres t ON t.id = o.type_offre_id
                LEFT JOIN niveaux_qualification nq ON nq.id = o.niveau_qualification_id
                LEFT JOIN domaines_emploi d ON d.id = o.domaine_emploi_id
                WHERE o.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);


        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    /** Trouver une offre active (public) */
    public function findActive(int $id): ?array
    {
        $sql = "SELECT o.*, e.nom AS entreprise_nom, l.ville AS localisation,
                       t.code AS type_offre_code, t.description AS type_offre_description,
                       nq.libelle AS niveau_qualification, d.nom AS domaine_emploi
                FROM offres o
                JOIN entreprises e ON e.id = o.entreprise_id
                LEFT JOIN localisations l ON l.id = o.localisation_id
                LEFT JOIN types_offres t ON t.id = o.type_offre_id
                LEFT JOIN niveaux_qualification nq ON nq.id = o.niveau_qualification_id
                LEFT JOIN domaines_emploi d ON d.id = o.domaine_emploi_id
                WHERE o.id = :id AND o.statut = 'active'
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    /** Créer une offre */
    public function create(array $data): int
    {
        $sql = "INSERT INTO offres
                (auteur_id, entreprise_id, type_offre_id, niveau_qualification_id, domaine_emploi_id, localisation_id,
                 titre, description, date_debut, date_fin, duree_contrat, salaire, statut, date_creation)
                VALUES
                (:auteur_id, :entreprise_id, :type_offre_id, :niveau_qualification_id, :domaine_emploi_id, :localisation_id,
                 :titre, :description, :date_debut, :date_fin, :duree_contrat, :salaire, :statut, NOW())";

        $stmt = $this->pdo->prepare($sql);

        $params = [
            'auteur_id'               => $data['auteur_id'],
            'entreprise_id'           => $data['entreprise_id'],
            'type_offre_id'           => $data['type_offre_id'],
            'niveau_qualification_id' => $data['niveau_qualification_id'],
            'domaine_emploi_id'       => $data['domaine_emploi_id'],
            'localisation_id'         => $data['localisation_id'],
            'titre'                   => $data['titre'],
            'description'             => $data['description'],
            'date_debut'              => $data['date_debut'],
            'date_fin'                => $data['date_fin'],
            'duree_contrat'           => $data['duree_contrat'],
            'salaire'                 => $data['salaire'],
            'statut'                  => $data['statut'],
        ];

        $stmt->execute($params);

        return (int)$this->pdo->lastInsertId();
    }


    /** Mettre à jour une offre (admin) */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE offres SET
                    entreprise_id = :entreprise_id,
                    type_offre_id = :type_offre_id,
                    niveau_qualification_id = :niveau_qualification_id,
                    domaine_emploi_id = :domaine_emploi_id,
                    localisation_id = :localisation_id,
                    titre = :titre,
                    description = :description,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    duree_contrat = :duree_contrat,
                    salaire = :salaire,
                    statut = :statut,
                    modifie_par = :modifie_par,
                    date_modification = :date_modification
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $params = [
            'id'                      => $id,
            'entreprise_id'           => $data['entreprise_id'],
            'type_offre_id'           => $data['type_offre_id'],
            'niveau_qualification_id' => $data['niveau_qualification_id'],
            'domaine_emploi_id'       => $data['domaine_emploi_id'],
            'localisation_id'         => $data['localisation_id'],
            'titre'                   => $data['titre'],
            'description'             => $data['description'],
            'date_debut'              => $data['date_debut'],
            'date_fin'                => $data['date_fin'],
            'duree_contrat'           => $data['duree_contrat'],
            'salaire'                 => $data['salaire'],
            'statut'                  => $data['statut'],
            'modifie_par'             => $data['modifie_par'],
            'date_modification'       => $data['date_modification'],
        ];

        return $stmt->execute($params);
    }


    /** Mettre à jour une offre appartenant à une entreprise */
    public function updateOwned(int $id, int $entrepriseId, array $data): bool // mise à jour avec restriction par entreprise
    {
        $sql = "UPDATE offres SET
                    type_offre_id = :type_offre_id,
                    niveau_qualification_id = :niveau_qualification_id,
                    domaine_emploi_id = :domaine_emploi_id,
                    localisation_id = :localisation_id,
                    titre = :titre,
                    description = :description,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    duree_contrat = :duree_contrat,
                    salaire = :salaire,
                    statut = :statut,
                    modifie_par = :modifie_par,
                    date_modification = :date_modification
                WHERE id = :id AND entreprise_id = :eid";

        $stmt = $this->pdo->prepare($sql);

        $params = [
            'id'                      => $id,
            'eid'                     => $entrepriseId,
            'type_offre_id'           => $data['type_offre_id'],
            'niveau_qualification_id' => $data['niveau_qualification_id'],
            'domaine_emploi_id'       => $data['domaine_emploi_id'],
            'localisation_id'         => $data['localisation_id'],
            'titre'                   => $data['titre'],
            'description'             => $data['description'],
            'date_debut'              => $data['date_debut'],
            'date_fin'                => $data['date_fin'],
            'duree_contrat'           => $data['duree_contrat'],
            'salaire'                 => $data['salaire'],
            'statut'                  => $data['statut'],
            'modifie_par'             => $data['modifie_par'],
            'date_modification'       => $data['date_modification'],
        ];

        return $stmt->execute($params);
    }


    /** Supprimer une offre (admin) */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM offres WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }


    /** Supprimer une offre appartenant à une entreprise */
    public function deleteOwned(int $id, int $entrepriseId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM offres WHERE id = :id AND entreprise_id = :eid");
        return $stmt->execute([
            'id'  => $id,
            'eid' => $entrepriseId
        ]);
    }

//REFERENTIELS
    /** Référentiel types d'offres */
    public function getTypesOffres(): array
    {
        return $this->pdo
            ->query("SELECT id, code, description FROM types_offres ORDER BY code ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Référentiel niveaux de qualification */
    public function getNiveauxQualification(): array
    {
        return $this->pdo
            ->query("SELECT id, libelle FROM niveaux_qualification ORDER BY libelle ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Référentiel domaines d'emploi */
    public function getDomainesEmploi(): array
    {
        return $this->pdo
            ->query("SELECT id, nom FROM domaines_emploi ORDER BY nom ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Référentiel localisations */
    public function getLocalisations(): array
    {
        return $this->pdo
            ->query("SELECT id, ville, departement, region, pays FROM localisations ORDER BY ville ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Référentiel entreprises (admin) */
    public function getEntreprises(): array
    {
        return $this->pdo
            ->query("SELECT id, nom FROM entreprises ORDER BY nom ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }



    /** ===========================
     *  DASHBOARD LIST (PAGINATED)
     *  =========================== */

    /**
 * baseSelect :
 * - SELECT commun aux listes paginées (admin & entreprise)
 * - inclut les joins nécessaires à l'affichage dashboard
 */
    private function baseSelect(): string
    {
        return "SELECT 
                    o.*,
                    e.nom AS entreprise_nom,
                    l.ville AS localisation,
                    t.code AS type_offre_code,
                    t.description AS type_offre_description,
    
                    d.nom AS domaine_emploi,  -- utile pour l'affichage sous le titre
    
                    ua.prenom AS auteur_nom,
                    ua.role   AS auteur_role,
    
                    um.prenom AS modifie_nom,
                    um.role   AS modifie_role
    
                FROM offres o
                JOIN entreprises e ON e.id = o.entreprise_id
                LEFT JOIN localisations l ON l.id = o.localisation_id
                LEFT JOIN types_offres t ON t.id = o.type_offre_id
                LEFT JOIN domaines_emploi d ON d.id = o.domaine_emploi_id
    
                LEFT JOIN users ua ON ua.id = o.auteur_id
                LEFT JOIN users um ON um.id = o.modifie_par";
    }



    /**
     * Construit le WHERE + params en fonction des filtres (hors entreprise_id).
     * $alias = 'o' pour offres.
     */
    private function buildWhere(array $filters, array &$params): string
    {
        $where = [];

        // Keyword sur titre + description (placeholders distincts pour PDO/MySQL)
        if (!empty($filters['keyword'])) {
            $where[] = "(o.titre LIKE :kw1 OR o.description LIKE :kw2)";
            $kw = '%' . trim((string)$filters['keyword']) . '%';
            $params[':kw1'] = $kw;
            $params[':kw2'] = $kw;
        }

        // Statut (active/inactive/archive)
        if (!empty($filters['statut'])) {
            $where[] = "o.statut = :statut";
            $params[':statut'] = trim((string)$filters['statut']);
        }

        // Type offre
        if (!empty($filters['type_offre_id'])) {
            $where[] = "o.type_offre_id = :type";
            $params[':type'] = (int)$filters['type_offre_id'];
        }

        return $where ? (" WHERE " . implode(" AND ", $where)) : "";
    }

    private function bindParams(\PDOStatement $stmt, array $params): void
    {
        foreach ($params as $k => $v) {
            if (is_int($v)) {
                $stmt->bindValue($k, $v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, (string)$v, PDO::PARAM_STR);
            }
        }
    }

    /** Admin: liste paginée */
    public function getAllPaginated(array $filters, int $limit, int $offset): array
    {
        $params = [];
        $where = $this->buildWhere($filters, $params);

        $sql = $this->baseSelect()
            . $where
            . " ORDER BY o.date_creation DESC, o.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        $this->bindParams($stmt, $params);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Admin: count paginé */
    public function countAll(array $filters): int
    {
        $params = [];
        $where = $this->buildWhere($filters, $params);

        $sql = "SELECT COUNT(*) 
                FROM offres o" . $where;

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /** Entreprise: liste paginée */
    public function getByEntreprisePaginated(int $entrepriseId, array $filters, int $limit, int $offset): array
    {
        $params = [];
        $where = $this->buildWhere($filters, $params);

        // On force entreprise_id en plus
        if ($where === '') {
            $where = " WHERE o.entreprise_id = :eid";
        } else {
            $where .= " AND o.entreprise_id = :eid";
        }
        $params[':eid'] = $entrepriseId;

        $sql = $this->baseSelect()
            . $where
            . " ORDER BY o.date_creation DESC, o.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        $this->bindParams($stmt, $params);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Entreprise: count paginé */
    public function countByEntreprise(int $entrepriseId, array $filters): int
    {
        $params = [];
        $where = $this->buildWhere($filters, $params);

        if ($where === '') {
            $where = " WHERE o.entreprise_id = :eid";
        } else {
            $where .= " AND o.entreprise_id = :eid";
        }
        $params[':eid'] = $entrepriseId;

        $sql = "SELECT COUNT(*)
                FROM offres o" . $where;

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }


}
