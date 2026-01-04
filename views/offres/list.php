<?php
declare(strict_types=1);

use App\Core\Security;

/**
 * =============================
 *  LISTE OFFRES (DASHBOARD)
 *  - Admin : voit toutes les offres
 *  - Entreprise : voit seulement ses offres
 *  + Filtres (GET) + Pagination + Actions (edit/delete)
 *
 *  NOTE FUTUR REFACTOR :
 *  - _filters.php (form filtre)
 *  - _table.php   (table)
 *  - _pagination.php
 * =============================
 */

$isAdmin   = ($mode ?? '') === 'admin';
$title     = $title ?? ($isAdmin ? 'Gestion des offres' : 'Mes offres');

// Routes selon le contexte (admin / entreprise)
$createUrl = $isAdmin ? "/admin/offres/create" : "/dashboard/offres/create";
$editBase  = $isAdmin ? "/admin/offres/edit"   : "/dashboard/offres/edit";
$delBase   = $isAdmin ? "/admin/offres/delete" : "/dashboard/offres/delete";

// Data
$items      = $items ?? [];
$refs       = $refs ?? [];             // refs['typesOffres'] attendu
$filters    = $filters ?? [];          // keyword, statut, type_offre_id
$pagination = $pagination ?? [];       // page, perPage, total, totalPages

// --- Helpers UI ---
$badge = function (?string $statut): array {
    return match ($statut) {
        'active'   => ['label' => 'Active',   'class' => 'bg-success'],
        'inactive' => ['label' => 'Inactive', 'class' => 'bg-secondary'],
        'archive'  => ['label' => 'Archivée', 'class' => 'bg-dark'],
        default    => ['label' => '—',        'class' => 'bg-light text-dark'],
    };
};

$fmtDate = function (?string $dt): string {
    if (!$dt) return '-';
    $ts = strtotime($dt);
    return $ts ? date('d/m/Y H:i', $ts) : $dt;
};

// --- Filtres (GET) ---
$keyword = trim((string)($filters['keyword'] ?? ($_GET['keyword'] ?? '')));
$statut  = trim((string)($filters['statut']  ?? ($_GET['statut']  ?? '')));
$typeId  = (int)($filters['type_offre_id']   ?? ($_GET['type_offre_id'] ?? 0));

// Pagination
$page       = (int)($pagination['page'] ?? ($_GET['page'] ?? 1));
$perPage    = (int)($pagination['perPage'] ?? ($_GET['perPage'] ?? 10));
$total      = (int)($pagination['total'] ?? count($items));
$totalPages = (int)($pagination['totalPages'] ?? 1);

// Path courant (pour reconstruire les URLs de pagination)
$basePath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: ($isAdmin ? '/admin/offres' : '/dashboard/offres');

/**
 * Construit une URL en conservant les filtres actuels (GET)
 * et en appliquant des overrides (ex: page => 2).
 */
$buildUrl = function(array $overrides = []) use ($basePath, $keyword, $statut, $typeId, $perPage, $page): string {
    $qs = [
        'keyword'       => $keyword !== '' ? $keyword : null,
        'statut'        => $statut !== '' ? $statut : null,
        'type_offre_id' => $typeId > 0 ? $typeId : null,
        'perPage'       => $perPage ?: 10,
        'page'          => $page ?: 1,
    ];

    foreach ($overrides as $k => $v) {
        $qs[$k] = $v;
    }

    // Nettoyage : enlever null/""/0 non pertinents
    $qs = array_filter($qs, fn($v) => !($v === null || $v === '' || $v === 0));

    return $basePath . (empty($qs) ? '' : ('?' . http_build_query($qs)));
};

// Options Statut (filtre)
$statusOptions = [
    ''         => 'Tous statuts',
    'active'   => 'Active',
    'inactive' => 'Inactive',
    'archive'  => 'Archivée',
];

// Options perPage
$perPageOptions = [10, 20, 50];
?>

<!-- Header page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1"><?= htmlspecialchars($title) ?></h1>
        <div class="text-muted small">
            <?= $isAdmin ? "Administration • gestion globale des offres" : "Espace entreprise • offres rattachées à votre entreprise" ?>
            • <?= $total ?> résultat<?= $total > 1 ? 's' : '' ?>
            <?php if ($totalPages > 1): ?>
                • page <?= max(1, $page) ?> / <?= max(1, $totalPages) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="<?= htmlspecialchars($createUrl) ?>">
            <i class="bi bi-plus-lg me-1"></i> Créer une offre
        </a>
    </div>
</div>

<!-- Filtres (upgrade sans casser ton style) -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= htmlspecialchars($basePath) ?>" class="row g-2 align-items-end">

            <!-- Keyword -->
            <div class="col-12 col-md-5">
                <label class="form-label mb-1">Recherche</label>
                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Titre ou description…"
                    value="<?= htmlspecialchars($keyword) ?>"
                >
            </div>

            <!-- Statut -->
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Statut</label>
                <select class="form-select" name="statut">
                    <?php foreach ($statusOptions as $k => $label): ?>
                        <option value="<?= htmlspecialchars($k) ?>" <?= ($statut === $k) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Type offre -->
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Type</label>
                <select class="form-select" name="type_offre_id">
                    <option value="0">Tous types</option>
                    <?php foreach (($refs['typesOffres'] ?? []) as $t): ?>
                        <?php $id = (int)($t['id'] ?? 0); ?>
                        <option value="<?= $id ?>" <?= ($typeId === $id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($t['code'] ?? '') . ' — ' . ($t['description'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- PerPage -->
            <div class="col-12 col-md-1">
                <label class="form-label mb-1">Par page</label>
                <select class="form-select" name="perPage">
                    <?php foreach ($perPageOptions as $n): ?>
                        <option value="<?= $n ?>" <?= ($perPage === $n) ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- À chaque filtre -> on revient page 1 -->
            <input type="hidden" name="page" value="1">

            <div class="col-12 d-flex justify-content-end gap-2">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>

                <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($basePath) ?>">
                    Réinitialiser
                </a>
            </div>

        </form>
    </div>
</div>

<!-- Résultats -->
<?php if (empty($items)): ?>
    <div class="alert alert-info mb-0">
        Aucune offre ne correspond aux filtres.
    </div>

<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px;">#</th>
                            <th>Titre</th>
                            <?php if ($isAdmin): ?><th>Entreprise</th><?php endif; ?>
                            <th style="width:120px;">Type</th>
                            <th style="width:160px;">Localisation</th>
                            <th style="width:110px;">Statut</th>
                            <th style="width:170px;">Créée</th>
                            <th style="width:170px;">Modifiée</th>
                            <th>Modifié par</th>
                            <th class="text-end" style="width:170px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($items as $offre): ?>
                        <?php
                        $id = (int)($offre['id'] ?? 0);

                        $statutRow = (string)($offre['statut'] ?? '');
                        $b = $badge($statutRow);

                        /**
                         * CSRF delete : on garde ta stratégie "one-time token par offre"
                         * => clé unique : offres_delete_{id}
                         */
                        $csrfKey = "offres_delete_" . $id;
                        $csrfDel = Security::generateCsrfToken($csrfKey);

                        $editUrl = $editBase . "?id=" . urlencode((string)$id);
                        $delUrl  = $delBase  . "?id=" . urlencode((string)$id);

                        // Affichage "Modifié par" (si présent)
                        $modNom  = $offre['modifie_nom'] ?? null;
                        $modRole = $offre['modifie_role'] ?? null;

                        $modifiePar = $modNom
                            ? trim((string)$modNom . ($modRole ? " • " . ucfirst((string)$modRole) : ""))
                            : '-';
                        ?>
                        <tr>
                            <td class="text-muted"><?= htmlspecialchars((string)$id) ?></td>

                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($offre['titre'] ?? '') ?></div>
                                <div class="text-muted small">
                                    <?= htmlspecialchars($offre['domaine_emploi'] ?? '') ?>
                                </div>
                            </td>

                            <?php if ($isAdmin): ?>
                                <td><?= htmlspecialchars($offre['entreprise_nom'] ?? '') ?></td>
                            <?php endif; ?>

                            <td><?= htmlspecialchars($offre['type_offre_code'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($offre['localisation'] ?? '—') ?></td>

                            <td>
                                <span class="badge <?= htmlspecialchars($b['class']) ?>">
                                    <?= htmlspecialchars($b['label']) ?>
                                </span>
                            </td>

                            <td><?= htmlspecialchars($fmtDate($offre['date_creation'] ?? null)) ?></td>
                            <td><?= htmlspecialchars($fmtDate($offre['date_modification'] ?? null)) ?></td>

                            <td><?= htmlspecialchars($modifiePar) ?></td>

                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($editUrl) ?>" title="Modifier">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form method="POST" action="<?= htmlspecialchars($delUrl) ?>" class="d-inline js-delete-form">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfDel) ?>">
                                    <input type="hidden" name="csrf_key" value="<?= htmlspecialchars($csrfKey) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <?php
        // Fenêtre courte : ... 1 ... (page-2..page+2) ... N
        $start = max(1, $page - 2);
        $end   = min($totalPages, $page + 2);
        ?>
        <nav class="mt-3" aria-label="Pagination offres">
            <ul class="pagination justify-content-end mb-0">

                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($buildUrl(['page' => max(1, $page - 1)])) ?>">Précédent</a>
                </li>

                <?php if ($start > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= htmlspecialchars($buildUrl(['page' => 1])) ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($p = $start; $p <= $end; $p++): ?>
                    <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars($buildUrl(['page' => $p])) ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= htmlspecialchars($buildUrl(['page' => $totalPages])) ?>"><?= $totalPages ?></a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($buildUrl(['page' => min($totalPages, $page + 1)])) ?>">Suivant</a>
                </li>

            </ul>
        </nav>
    <?php endif; ?>

    <script>
    /**
     * Confirmation delete (SweetAlert2) — on conserve ta base.
     * IMPORTANT : si le user confirme, on submit le form => CSRF vérifié côté serveur.
     */
    document.querySelectorAll('.js-delete-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Supprimer cette offre ?',
                text: "Cette action est irréversible.",
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
    </script>
<?php endif; ?>
