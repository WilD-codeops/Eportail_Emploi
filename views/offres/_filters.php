<?php
declare(strict_types=1);

/**
 * ==========================================================
 *  PARTIAL FILTRES OFFRES (GET)
 *  - Réutilisable admin & entreprise
 *  - Le submit est intercepté en JS (AJAX) mais reste compatible sans JS
 * ==========================================================
 */

$mode    = $mode ?? 'entreprise';
$filters = $filters ?? ['keyword'=>null, 'statut'=>null, 'type_offre_id'=>null];
$refs    = $refs ?? [];

$action = ($mode === 'admin') ? '/admin/offres' : '/dashboard/offres';

$keyword = trim((string)($filters['keyword'] ?? ''));
$statut  = trim((string)($filters['statut'] ?? ''));
$typeId  = (int)($filters['type_offre_id'] ?? 0);

$perPage = (int)($_GET['perPage'] ?? ($pagination['perPage'] ?? 10));
$perPageOptions = [10, 20, 50];

// Statut : archive seulement admin
$statusOptions = [
  '' => 'Tous',
  'active' => 'Active',
  'inactive' => 'Inactive',
];
if ($mode === 'admin') $statusOptions['archive'] = 'Archivée';
?>

<form id="offres-filters-form" method="GET" action="<?= htmlspecialchars($action) ?>" class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">

      <div class="col-12 col-md-5">
        <label class="form-label small mb-1">
          <i class="bi bi-search me-1" aria-hidden="true"></i>Recherche
        </label>
        <input class="form-control" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Titre, description...">
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label small mb-1">
          <i class="bi bi-check-circle me-1" aria-hidden="true"></i>Statut
        </label>
        <select class="form-select" name="statut">
          <?php foreach ($statusOptions as $k => $label): ?>
            <option value="<?= htmlspecialchars($k) ?>" <?= $statut===$k?'selected':'' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label small mb-1">
          <i class="bi bi-box me-1" aria-hidden="true"></i>Type
        </label>
        <select class="form-select" name="type_offre_id">
          <option value="0">Tous</option>
          <?php foreach (($refs['typesOffres'] ?? []) as $t): ?>
            <?php $id = (int)($t['id'] ?? 0); ?>
            <option value="<?= $id ?>" <?= $typeId===$id?'selected':'' ?>>
              <?= htmlspecialchars(($t['code'] ?? '') . ' — ' . ($t['description'] ?? '')) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-1">
        <label class="form-label small mb-1">
          <i class="bi bi-list-ul me-1" aria-hidden="true"></i>Par page
        </label>
        <select class="form-select" name="perPage">
          <?php foreach ($perPageOptions as $n): ?>
            <option value="<?= $n ?>" <?= $perPage===$n?'selected':'' ?>><?= $n ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- à chaque filtre on revient en page 1 -->
      <input type="hidden" name="page" value="1">

      <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a class="btn btn-outline-secondary btn-sm btn-ghost" href="<?= htmlspecialchars($action) ?>">
          <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>Réinitialiser
        </a>
        <button class="btn btn-primary btn-sm btn-lift" type="submit">
          <i class="bi bi-funnel me-1" aria-hidden="true"></i> Filtrer
        </button>
      </div>

    </div>
  </div>
</form>
