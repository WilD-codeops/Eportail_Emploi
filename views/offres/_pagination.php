<?php
declare(strict_types=1);

/**
 * PAGINATION OFFRES
 * Attend :
 * - $page, $totalPages
 * - $basePath (fallback non-AJAX)
 * - $buildQuery(int $page) => string (query string)
 */
?>

<?php if ($totalPages > 1): ?>
  <?php
    $start = max(1, $page - 2);
    $end   = min($totalPages, $page + 2);
  ?>

  <nav class="mt-3" aria-label="Pagination offres">
    <ul class="pagination justify-content-end mb-0">

      <?php
        $prevQs = $buildQuery(max(1, $page - 1));
        $nextQs = $buildQuery(min($totalPages, $page + 1));
      ?>

      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link"
           href="<?= htmlspecialchars($basePath . '?' . $prevQs) ?>"
           data-ajax-page="1"
           data-qs="<?= htmlspecialchars($prevQs) ?>">Précédent</a>
      </li>

      <?php if ($start > 1): ?>
        <?php $qs1 = $buildQuery(1); ?>
        <li class="page-item">
          <a class="page-link"
             href="<?= htmlspecialchars($basePath . '?' . $qs1) ?>"
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
             href="<?= htmlspecialchars($basePath . '?' . $qsp) ?>"
             data-ajax-page="1"
             data-qs="<?= htmlspecialchars($qsp) ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?>
          <li class="page-item disabled"><span class="page-link">…</span></li>
        <?php endif; ?>
        <?php $qsl = $buildQuery($totalPages); ?>
        <li class="page-item">
          <a class="page-link"
             href="<?= htmlspecialchars($basePath . '?' . $qsl) ?>"
             data-ajax-page="1"
             data-qs="<?= htmlspecialchars($qsl) ?>"><?= $totalPages ?></a>
        </li>
      <?php endif; ?>

      <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
        <a class="page-link"
           href="<?= htmlspecialchars($basePath . '?' . $nextQs) ?>"
           data-ajax-page="1"
           data-qs="<?= htmlspecialchars($nextQs) ?>">Suivant</a>
      </li>

    </ul>
  </nav>
<?php endif; ?>
