<?php
/**
 * ==========================================================
 *  PARTIAL RESULTATS OFFRES (PUBLIC)
 *  - Utilisé par : public_list.php + /offres/partial (AJAX)
 * ==========================================================
 */

$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$items      = $items ?? [];
$pagination = $pagination ?? [];
$filters    = $filters ?? [];

$count = count($items);

$page       = max(1, (int)($pagination['page'] ?? 1));
$pages      = max(1, (int)($pagination['totalPages'] ?? 1));
$perPage    = min(50, max(1, (int)($pagination['perPage'] ?? 10)));

$keyword       = trim((string)($filters['keyword'] ?? ''));
$localisation  = (int)($filters['localisation_id'] ?? 0);
$typeId        = (int)($filters['type_offre_id'] ?? 0);
$domaineId     = (int)($filters['domaine_emploi_id'] ?? 0);
$tri           = trim((string)($filters['tri'] ?? ''));

$buildQuery = function(int $targetPage) use ($keyword, $localisation, $typeId, $domaineId, $tri, $perPage): string {
    $qs = [
        'keyword'           => $keyword !== '' ? $keyword : null,
        'localisation_id'   => $localisation > 0 ? $localisation : null,
        'type_offre_id'     => $typeId > 0 ? $typeId : null,
        'domaine_emploi_id' => $domaineId > 0 ? $domaineId : null,
        'tri'               => $tri !== '' ? $tri : null,
        'perPage'           => $perPage,
        'page'              => $targetPage,
    ];

    $qs = array_filter($qs, fn($v) => !($v === null || $v === '' || $v === 0));
    return http_build_query($qs);
};
?>

<div class="mb-4">
    <?php if ($count === 0): ?>
        <p class="text-muted fst-italic">Aucune Offre trouvée.</p>
    <?php elseif ($count === 1): ?>
        <p class="fw-semibold text-secondary">1 Offre trouvée</p>
    <?php else: ?>
        <p class="fw-semibold text-secondary"><?= $count ?> Offres trouvées</p>
    <?php endif; ?>
</div>

<div class="row g-4">

  <?php foreach ($items as $o): ?>
    <div class="col-12 col-md-6 col-lg-4">
      <article class="card h-100 shadow-sm" aria-label="Fiche offre">
        <div class="card-body d-flex flex-column">

          <div class="d-flex align-items-center mb-3">
            <?php
              $logo = $o['logo'] ?? '/assets/img/company_logo_generique.png';
            ?>
            <img src="<?= $e($logo) ?>"
                 alt="Logo <?= $e($o['entreprise_nom']) ?>"
                 class="rounded-circle me-3"
                 style="width: 55px; height: 55px; object-fit: cover;">
            <div>
              <h2 class="h6 mb-0"><?= $e($o['entreprise_nom']) ?></h2>
              <small class="text-muted"><?= $e($o['localisation'] ?? '') ?></small>
            </div>
          </div>

          <h3 class="h5 mb-2"><?= $e($o['titre']) ?></h3>

          <ul class="list-unstyled text-muted small mb-3">
            <li><i class="bi bi-briefcase me-1"></i> <?= $e($o['type_offre_description'] ?? '') ?></li>
            <?php if (!empty($o['date_debut'])): ?>
              <li><i class="bi bi-calendar-event me-1"></i> Début : <?= $e($o['date_debut']) ?></li>
            <?php endif; ?>
            <?php if (!empty($o['date_fin'])): ?>
              <li><i class="bi bi-calendar-check me-1"></i> Fin : <?= $e($o['date_fin']) ?></li>
            <?php endif; ?>
          </ul>

          <a href="/offres/show?id=<?= (int)$o['id'] ?>"
             class="btn btn-primary mt-auto"
             aria-label="Voir l’offre <?= $e($o['titre']) ?>">
            Voir l’offre
          </a>

        </div>
      </article>
    </div>
  <?php endforeach; ?>

  <?php if (empty($items)): ?>
    <div class="col-12">
      <p class="text-muted text-center">Aucune offre ne correspond à vos critères.</p>
    </div>
  <?php endif; ?>

</div>

<?php if ($pages > 1): ?>
  <?php
    $start = max(1, $page - 2);
    $end   = min($pages, $page + 2);

    $prevQs = $buildQuery(max(1, $page - 1));
    $nextQs = $buildQuery(min($pages, $page + 1));
  ?>

  <nav class="mt-3" aria-label="Pagination offres">
    <ul class="pagination justify-content-center mb-0">

      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link"
           href="/offres?<?= htmlspecialchars($prevQs) ?>"
           data-ajax-page="1"
           data-qs="<?= htmlspecialchars($prevQs) ?>">&laquo;</a>
      </li>

      <?php if ($start > 1): ?>
        <?php $qs1 = $buildQuery(1); ?>
        <li class="page-item">
          <a class="page-link"
             href="/offres?<?= htmlspecialchars($qs1) ?>"
             data-ajax-page="1"
             data-qs="<?= htmlspecialchars($qs1) ?>">1</a>
        </li>
        <?php if ($start > 2): ?>
          <li class="page-item disabled"><span class="page-link">…</span></li>
        <?php endif; ?>
      <?php endif; ?>

      <?php for ($p = $start; $p <= $end; $p++): ?>
        <?php $qsp = $buildQuery($p); ?>
        <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
          <a class="page-link"
             href="/offres?<?= htmlspecialchars($qsp) ?>"
             data-ajax-page="1"
             data-qs="<?= htmlspecialchars($qsp) ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($end < $pages): ?>
        <?php if ($end < $pages - 1): ?>
          <li class="page-item disabled"><span class="page-link">…</span></li>
        <?php endif; ?>
        <?php $qsl = $buildQuery($pages); ?>
        <li class="page-item">
          <a class="page-link"
             href="/offres?<?= htmlspecialchars($qsl) ?>"
             data-ajax-page="1"
             data-qs="<?= htmlspecialchars($qsl) ?>"><?= $pages ?></a>
        </li>
      <?php endif; ?>

      <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
        <a class="page-link"
           href="/offres?<?= htmlspecialchars($nextQs) ?>"
           data-ajax-page="1"
           data-qs="<?= htmlspecialchars($nextQs) ?>">&raquo;</a>
      </li>

    </ul>
  </nav>
<?php endif; ?>
