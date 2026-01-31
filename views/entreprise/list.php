<?php use App\Core\Security; ?>
<?php include __DIR__ . "/_filters.php"; ?>
<div class="card shadow-sm">
    <div class="card-body">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-0">Entreprises</h2>
                <p class="small text-muted mb-0">
                    Vue d’ensemble des entreprises partenaires et de leurs gestionnaires.
                </p>
            </div>

            <a href="/admin/entreprises/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Créer une entreprise
            </a>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th class="d-none d-md-table-cell">Secteur</th>
                        <th>Localisation</th>
                        <th class="d-none d-lg-table-cell">Gestionnaire</th>
                        <th class="d-none d-xl-table-cell">Email</th>
                        <th class="d-none d-xl-table-cell">Téléphone</th>
                        <th class="d-none d-lg-table-cell">SIRET</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($entreprises as $e): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($e['nom']) ?></td>

                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($e['secteur'] ?? '—') ?></td>

                        <td>
                            <?= htmlspecialchars($e['ville'] ?? '—') ?>
                            <?php if (!empty($e['pays'])): ?>
                                <span class="text-muted small">(<?= htmlspecialchars($e['pays']) ?>)</span>
                            <?php endif; ?>
                        </td>

                        <td class="d-none d-lg-table-cell">
                            <?= htmlspecialchars(($e['gestionnaire'])) ?>
                        </td>

                        <td class="d-none d-xl-table-cell"><?= htmlspecialchars($e['email'] ?? '—') ?></td>

                        <td class="d-none d-xl-table-cell"><?= htmlspecialchars($e['telephone'] ?? '—') ?></td>

                        <td class="d-none d-lg-table-cell"><?= htmlspecialchars($e['siret'] ?? '—') ?></td>

                        <td class="text-end">

                            <!-- Bouton détails -->
                            <button 
                                class="btn btn-info btn-sm js-details-btn"
                                data-id="<?= (int)$e['id'] ?>"
                                data-nom="<?= htmlspecialchars($e['nom']) ?>"
                                data-secteur="<?= htmlspecialchars($e['secteur'] ?? '') ?>"
                                data-ville="<?= htmlspecialchars($e['ville'] ?? '') ?>"
                                data-pays="<?= htmlspecialchars($e['pays'] ?? '') ?>"
                                data-email="<?= htmlspecialchars($e['email'] ?? '') ?>"
                                data-tel="<?= htmlspecialchars($e['telephone'] ?? '') ?>"
                                data-siret="<?= htmlspecialchars($e['siret'] ?? '') ?>"
                                data-adresse="<?= htmlspecialchars($e['adresse'] ?? '') ?>"
                                data-cp="<?= htmlspecialchars($e['code_postal'] ?? '') ?>"
                                data-gestionnaire="<?= htmlspecialchars($e['gestionnaire'] ?? '') ?>"
                                data-description="<?= htmlspecialchars($e['description'] ?? '') ?>"
                                data-site="<?= htmlspecialchars($e['site_web'] ?? '') ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#entrepriseModal"
                                aria-label="Voir les détails de l’entreprise <?= htmlspecialchars($e['nom']) ?>"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                            <!-- Modifier -->
                            <a href="/admin/entreprises/edit?id=<?= (int)$e['id'] ?>"
                               class="btn btn-primary btn-sm"
                               aria-label="Modifier l’entreprise <?= htmlspecialchars($e['nom']) ?>">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <!-- Supprimer -->
                            <?php
                                $csrfKey = "entreprise_delete_" . (int)$e['id'];
                                $csrfDel = Security::generateCsrfToken($csrfKey);
                                ?>

                                <form method="POST"
                                      action="/admin/entreprises/delete"
                                      class="d-inline js-delete-form">

                                    <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfDel) ?>">
                                    <input type="hidden" name="csrf_key" value="<?= htmlspecialchars($csrfKey) ?>">

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            aria-label="Supprimer l’entreprise <?= htmlspecialchars($e['nom']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($entreprises)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Aucune entreprise enregistrée.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <?php include __DIR__ . '/../partials/pagination.php';  ?>
    </div>
</div>

<!-- Modal détails entreprise -->
<div class="modal fade" id="entrepriseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Détails de l’entreprise</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- LOGO ENTREPRISE (MVP : image statique) -->
        <div class="text-center mb-4">
            <img 
                src="/assets/img/logoEntreprise.png" 
                alt="Logo entreprise"
                class="img-fluid"
                style="max-width: 140px; border-radius: var(--radius-md);"
            >
        </div>

        <div id="entrepriseModalContent" class="py-3">
            <p class="text-muted text-center">Chargement...</p>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>

    </div>
  </div>
</div>

<script>
    function bindDeleteConfirmations(root = document) {
  root.querySelectorAll('.js-delete-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      if (typeof Swal === 'undefined') return form.submit();

      Swal.fire({
        icon: 'warning',
        title: 'Supprimer cette entreprise ?',
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

bindDeleteConfirmations();
</script>