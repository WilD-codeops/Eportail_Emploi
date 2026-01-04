<?php
declare(strict_types=1);

use App\Core\Security;

/**
 * ==========================================================
 *  PARTIAL RESULTATS OFFRES
 *  - Utilisé par :
 *     - list.php (page complète)
 *     - /admin/offres/partial et /dashboard/offres/partial (AJAX)
 *  => Donc il DOIT définir les variables nécessaires à _table/_pagination
 * ==========================================================
 */

$mode       = $mode ?? 'entreprise';
$isAdmin    = ($mode === 'admin');

$items      = $items ?? [];
$pagination = $pagination ?? [];
$filters    = $filters ?? [];

// Routes selon contexte
$editBase  = $isAdmin ? "/admin/offres/edit"   : "/dashboard/offres/edit";
$delBase   = $isAdmin ? "/admin/offres/delete" : "/dashboard/offres/delete";

/** Badge statut */
$badge = function (?string $statut): array {
  return match ($statut) {
    'active'   => ['label' => 'Active',   'class' => 'bg-success'],
    'inactive' => ['label' => 'Inactive', 'class' => 'bg-secondary'],
    'archive'  => ['label' => 'Archivée', 'class' => 'bg-dark'],
    default    => ['label' => '—',        'class' => 'bg-light text-dark'],
  };
};

/** Format date safe */
$fmtDate = function (?string $dt): string {
  if (!$dt) return '-';
  $ts = strtotime($dt);
  return $ts ? date('d/m/Y H:i', $ts) : $dt;
};

// Pagination values
$page       = max(1, (int)($pagination['page'] ?? 1));
$perPage    = min(50, max(1, (int)($pagination['perPage'] ?? 10)));
$totalPages = max(1, (int)($pagination['totalPages'] ?? 1));

// Pour construire les liens (mode GET fallback + data-qs AJAX)
$basePath = $isAdmin ? '/admin/offres' : '/dashboard/offres';

$keyword = trim((string)($filters['keyword'] ?? ($_GET['keyword'] ?? '')));
$statut  = trim((string)($filters['statut'] ?? ($_GET['statut'] ?? '')));
$typeId  = (int)($filters['type_offre_id'] ?? ($_GET['type_offre_id'] ?? 0));

$buildQuery = function(int $targetPage) use ($keyword, $statut, $typeId, $perPage): string {
  $qs = [
    'keyword' => $keyword !== '' ? $keyword : null,
    'statut'  => $statut !== '' ? $statut : null,
    'type_offre_id' => $typeId > 0 ? $typeId : null,
    'perPage' => $perPage,
    'page'    => $targetPage,
  ];
  $qs = array_filter($qs, fn($v) => !($v === null || $v === '' || $v === 0));
  return http_build_query($qs);
};
?>

<?php if (empty($items)): ?>
  <div class="alert alert-info mb-0">
    Aucune offre ne correspond aux filtres.
  </div>
<?php else: ?>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <?php require __DIR__ . "/_table.php"; ?>
      </div>
    </div>
  </div>

  <?php require __DIR__ . "/_pagination.php"; ?>

<?php endif; ?>
