<?php
declare(strict_types=1);

/**
 * ==========================================================
 *  LISTE OFFRES (DASHBOARD) - PAGE COMPLETE
 *  - Contient : header + filtres + conteneur résultats + JS AJAX
 *  - Les résultats (table + pagination) sont dans _results.php
 *  - _results.php est aussi rendu via endpoint "partial" (AJAX)
 * ==========================================================
 */

// Variables injectées par le controller
$mode       = $mode ?? 'entreprise'; // 'admin' | 'entreprise'
$title      = $title ?? ($mode === 'admin' ? 'Gestion des offres' : 'Mes offres');
$items      = $items ?? [];
$filters    = $filters ?? [];
$pagination = $pagination ?? [];
$refs       = $refs ?? [];

// Pour l’AJAX : endpoint qui renvoie uniquement _results.php
$partialUrl = ($mode === 'admin') ? '/admin/offres/partial' : '/dashboard/offres/partial';

// Path courant : utilisé pour action du form / reset
$basePath = ($mode === 'admin') ? '/admin/offres' : '/dashboard/offres';
?>

<!-- Header page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1"><?= htmlspecialchars($title) ?></h1>
        <div class="text-muted small">
            <?= $mode === 'admin'
                ? "Administration • gestion globale des offres"
                : "Espace entreprise • offres rattachées à votre entreprise"
            ?>
            • <?= (int)($pagination['total'] ?? count($items)) ?> résultat(s)
        </div>
    </div>

    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="<?= htmlspecialchars($mode === 'admin' ? '/admin/offres/create' : '/dashboard/offres/create') ?>">
            <i class="bi bi-plus-lg me-1"></i> Créer une offre
        </a>
    </div>
</div>

<!-- Filtres -->
<?php require __DIR__ . "/_filters.php"; ?>

<!-- Résultats (remplacés via AJAX) -->
<div id="offres-results">
    <?php require __DIR__ . "/_results.php"; ?>
</div>

<script>
/**
 * ==========================================================
 * AJAX FILTRES + PAGINATION
 * - Objectif : éviter de recharger la page entière
 * - On récupère HTML partial (/admin/offres/partial?... ou /dashboard/offres/partial?...)
 * - On remplace #offres-results
 * - On ré-attache SweetAlert delete (car le DOM change)
 * ==========================================================
 */

const partialEndpoint = <?= json_encode($partialUrl, JSON_UNESCAPED_SLASHES) ?>;

/** Bind SweetAlert delete sur les forms présents */
function bindDeleteConfirmations(root = document) {
  root.querySelectorAll('.js-delete-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      if (typeof Swal === 'undefined') return form.submit();

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
}

/** Fetch HTML puis remplace le bloc résultats */
async function loadResults(queryString) {
  const url = partialEndpoint + (queryString ? ('?' + queryString) : '');
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  const html = await res.text();

  const container = document.getElementById('offres-results');
  container.innerHTML = html;

  // Re-bind sur nouveau DOM
  bindDeleteConfirmations(container);
  bindAjaxPagination(container);
}

/** Intercepte les liens de pagination */
function bindAjaxPagination(root = document) {
  root.querySelectorAll('a[data-ajax-page="1"]').forEach(a => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      const qs = a.getAttribute('data-qs') || '';
      loadResults(qs);
      window.history.replaceState({}, '', <?= json_encode($basePath, JSON_UNESCAPED_SLASHES) ?> + (qs ? ('?' + qs) : ''));
    });
  });
}

/** Intercepte le submit du form filtre */
const filtreForm = document.getElementById('offres-filters-form');
if (filtreForm) {
  filtreForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const fd = new FormData(filtreForm);
    const qs = new URLSearchParams(fd).toString();

    loadResults(qs);
    window.history.replaceState({}, '', <?= json_encode($basePath, JSON_UNESCAPED_SLASHES) ?> + (qs ? ('?' + qs) : ''));
  });
}

// Bind initial
bindDeleteConfirmations(document);
bindAjaxPagination(document);
</script>
