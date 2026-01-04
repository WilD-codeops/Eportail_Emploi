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
      $b  = $badge((string)($offre['statut'] ?? ''));

      // CSRF unique par offre
      $csrfKey = "offres_delete_" . $id;
      $csrfDel = Security::generateCsrfToken($csrfKey);

      $editUrl = $editBase . "?id=" . urlencode((string)$id);
      $delUrl  = $delBase  . "?id=" . urlencode((string)$id);

      $modNom  = $offre['modifie_nom'] ?? null;
      $modRole = $offre['modifie_role'] ?? null;
      $modifiePar = $modNom ? trim((string)$modNom . ($modRole ? " • " . ucfirst((string)$modRole) : "")) : '-';
    ?>
    <tr>
      <td class="text-muted"><?= $id ?></td>

      <td>
        <div class="fw-semibold"><?= htmlspecialchars($offre['titre'] ?? '') ?></div>
        <div class="text-muted small"><?= htmlspecialchars($offre['domaine_emploi'] ?? '') ?></div>
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
