<?php
declare(strict_types=1);

use App\Core\Security;

/**
 * TABLE OFFRES
 * Attend :
 * - $items, $isAdmin
 * - $badge (callable), $fmtDate (callable)
 * - $editBase, $delBase
 */
?>

<table class="table table-hover align-middle mb-0 admin-offres-table">
  <thead class="table-light admin-offres-head">
    <tr>
      <th scope="col">Titre</th>
      <th scope="col" class="d-none d-md-table-cell">Type</th>
      <th scope="col" class="d-none d-lg-table-cell">Localisation</th>
      <th scope="col">Statut</th>
      <th scope="col" class="d-none d-xl-table-cell">Créée</th>
      <th scope="col" class="text-end">Actions</th>
    </tr>
  </thead>

  <tbody>
  <?php if (empty($items)): ?>
    <tr>
      <td colspan="6" class="text-center text-muted py-4">
        <i class="bi bi-inbox display-6 mb-2" aria-hidden="true"></i>
        <p class="mb-0">Aucune offre ne correspond aux filtres.</p>
      </td>
    </tr>
  <?php else: ?>
    <?php foreach ($items as $offre): ?>
      <?php
        $id = (int)($offre['id'] ?? 0);
        $b  = $badge((string)($offre['statut'] ?? ''));

        // CSRF unique par offre
        $csrfKey = "offres_delete_" . $id;
        $csrfDel = Security::generateCsrfToken($csrfKey);

        $editUrl = $editBase . "?id=" . urlencode((string)$id);
        $delUrl  = $delBase  . "?id=" . urlencode((string)$id);
        
        $titre = htmlspecialchars($offre['titre'] ?? '');
        $domaine = htmlspecialchars($offre['domaine_emploi'] ?? '');
      ?>
      <tr class="admin-offre-row">
        <td>
          <div class="fw-semibold">
            <i class="bi bi-briefcase text-primary me-2" aria-hidden="true"></i><?= $titre ?>
          </div>
          <div class="text-muted small">
            <i class="bi bi-tag me-1" aria-hidden="true"></i><?= $domaine ?>
          </div>
        </td>

        <td class="d-none d-md-table-cell">
          <span class="badge bg-info-light text-info">
            <i class="bi bi-box me-1" aria-hidden="true"></i><?= htmlspecialchars($offre['type_offre_code'] ?? '—') ?>
          </span>
        </td>

        <td class="d-none d-lg-table-cell">
          <i class="bi bi-geo-alt text-info me-1" aria-hidden="true"></i><?= htmlspecialchars($offre['localisation'] ?? '—') ?>
        </td>

        <td>
          <span class="badge <?= htmlspecialchars($b['class']) ?>">
            <i class="bi bi-check-circle me-1" aria-hidden="true"></i><?= htmlspecialchars($b['label']) ?>
          </span>
        </td>

        <td class="d-none d-xl-table-cell text-muted small">
          <i class="bi bi-calendar-event me-1" aria-hidden="true"></i><?= htmlspecialchars($fmtDate($offre['date_creation'] ?? null)) ?>
        </td>

        <td class="text-end">
          <div class="btn-group btn-group-sm" role="group" aria-label="Actions sur l'offre <?= $titre ?>">
            <!-- Modifier -->
            <a href="<?= htmlspecialchars($editUrl) ?>"
               class="btn btn-primary btn-sm btn-icon btn-soft-primary"
               aria-label="Modifier offre <?= $titre ?>"
               title="Modifier">
              <i class="bi bi-pencil-square" aria-hidden="true"></i>
              <span class="visually-hidden">Modifier</span>
            </a>

            <!-- Supprimer -->
            <form method="POST"
                  action="<?= htmlspecialchars($delUrl) ?>"
                  class="d-inline js-delete-form">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfDel) ?>">
              <input type="hidden" name="csrf_key" value="<?= htmlspecialchars($csrfKey) ?>">

              <button type="submit"
                      class="btn btn-danger btn-sm btn-icon btn-soft-danger"
                      aria-label="Supprimer offre <?= $titre ?>"
                      title="Supprimer">
                <i class="bi bi-trash" aria-hidden="true"></i>
                <span class="visually-hidden">Supprimer</span>
              </button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>
