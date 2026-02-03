<?php use App\Core\Security; ?>
<?php

$kpi = $kpi ?? [];
$kpi = $kpi + [
    'total' => 0,
    'sectorsCount' => 0
];

?>

<!-- ===========================
     HEADER + KPI CARDS
=========================== -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="h4 mb-1 text-primary">
            <i class="bi bi-building-check me-2" aria-hidden="true"></i>Liste des entreprises
        </h2>
        <div class="text-muted small">
            <i class="bi bi-shield-check me-1" aria-hidden="true"></i>Administration • gestion des partenaires • <?= (int)$kpi['total'] ?> entreprise(s)
        </div>
    </div>

    <a href="/admin/entreprises/create" class="btn btn-primary btn-sm btn-lift">
        <i class="bi bi-plus-lg me-1"></i> Créer une entreprise
    </a>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-5 border-primary" style="border-radius: var(--radius-md);">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted mb-1">
                            <i class="bi bi-building me-1" aria-hidden="true"></i> 
                            <span>Total entreprises</span>
                        </div>
                        <div class="fs-3 fw-bold text-primary">
                            <?= (int)$kpi['total'] ?>
                        </div>
                    </div>
                    <div class="fs-1 text-primary opacity-25">
                        <i class="bi bi-building" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-5 border-success" style="border-radius: var(--radius-md);">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="small text-muted mb-1">
                            <i class="bi bi-diagram-3 me-1" aria-hidden="true"></i> 
                            <span>Secteurs représentés</span>
                        </div>
                        <div class="fs-3 fw-bold text-success">
                            <?= (int)$kpi['sectorsCount'] ?>
                        </div>
                    </div>
                    <div class="fs-1 text-success opacity-25">
                        <i class="bi bi-diagram-3" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/_filters.php"; ?>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-entreprises-table">
                <thead class="table-light admin-entreprises-head">
                    <tr>
                        <th scope="col">Nom</th>
                        <th scope="col" class="d-none d-md-table-cell">Secteur</th>
                        <th scope="col">Localisation</th>
                        <th scope="col" class="d-none d-lg-table-cell">Gestionnaire</th>
                        <th scope="col" class="d-none d-lg-table-cell">Date d'inscription</th>
                        <th scope="col" class="d-none d-xl-table-cell">Email</th>
                        <th scope="col" class="d-none d-xl-table-cell">Téléphone</th>
                        <th scope="col" class="d-none d-lg-table-cell">SIRET</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($entreprises as $e): ?>
                    <tr class="admin-entreprise-row">
                        <!-- Nom entreprise -->
                        <td class="fw-semibold">
                            <i class="bi bi-building text-primary me-2" aria-hidden="true"></i>
                            <span><?= htmlspecialchars($e['nom']) ?></span>
                        </td>

                        <!-- Secteur -->
                        <td class="d-none d-md-table-cell">
                            <?php if (!empty($e['secteur'])): ?>
                                <span class="badge bg-primary-light text-primary fw-normal">
                                    <i class="bi bi-tag me-1" aria-hidden="true"></i>
                                    <?= htmlspecialchars($e['secteur']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted fst-italic">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Localisation -->
                        <td>
                            <i class="bi bi-geo-alt text-info me-1" aria-hidden="true"></i>
                            <span><?= htmlspecialchars($e['ville'] ?? '—') ?></span>
                            <?php if (!empty($e['pays'])): ?>
                                <span class="text-muted small">(<?= htmlspecialchars($e['pays']) ?>)</span>
                            <?php endif; ?>
                        </td>

                        <!-- Gestionnaire -->
                        <td class="d-none d-lg-table-cell">
                            <i class="bi bi-person-circle text-secondary me-1" aria-hidden="true"></i>
                            <span><?= htmlspecialchars(($e['gestionnaire'])) ?></span>
                        </td>

                        <!-- Date d'inscription -->
                        <td class="d-none d-lg-table-cell">
                            <i class="bi bi-calendar-event text-muted me-1" aria-hidden="true"></i>
                            <span><?= htmlspecialchars((new DateTime($e['date_inscription'] ?? ''))->format('d/m/Y')) ?></span>
                        </td>

                        <!-- Email -->
                        <td class="d-none d-xl-table-cell">
                            <i class="bi bi-envelope text-danger me-1" aria-hidden="true"></i>
                            <?php if (!empty($e['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($e['email']) ?>" class="text-decoration-none text-danger-emphasis" title="Envoyer un email à <?= htmlspecialchars($e['email']) ?>">
                                    <?= htmlspecialchars($e['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted fst-italic">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Téléphone -->
                        <td class="d-none d-xl-table-cell">
                            <i class="bi bi-telephone text-warning me-1" aria-hidden="true"></i>
                            <?php if (!empty($e['telephone'])): ?>
                                <a href="tel:<?= htmlspecialchars($e['telephone']) ?>" class="text-decoration-none text-warning-emphasis" title="Appeler <?= htmlspecialchars($e['telephone']) ?>">
                                    <?= htmlspecialchars($e['telephone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted fst-italic">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- SIRET -->
                        <td class="d-none d-lg-table-cell">
                            <span class="badge bg-light text-dark border border-secondary" title="Numéro SIRET">
                                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>
                                <?= htmlspecialchars($e['siret'] ?? '—') ?>
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions sur l'entreprise <?= htmlspecialchars($e['nom']) ?>">
                                <!-- Bouton détails -->
                                <button 
                                    class="btn btn-primary btn-sm btn-icon btn-soft-primary js-details-btn"
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
                                    data-date_inscription="<?= htmlspecialchars((new DateTime($e['date_inscription'] ?? ''))->format('d/m/Y')) ?>"
                                    data-description="<?= htmlspecialchars($e['description'] ?? '') ?>"
                                    data-site="<?= htmlspecialchars($e['site_web'] ?? '') ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#entrepriseModal"
                                    aria-label="Voir les détails de l'entreprise <?= htmlspecialchars($e['nom']) ?>"
                                    title="Voir les détails"
                                >
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                    <span class="visually-hidden">Détails</span>
                                </button>

                                <!-- Modifier -->
                                <a href="/admin/entreprises/edit?id=<?= (int)$e['id'] ?>"
                                   class="btn btn-primary btn-sm btn-icon btn-soft-primary"
                                   aria-label="Modifier l'entreprise <?= htmlspecialchars($e['nom']) ?>"
                                   title="Modifier">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    <span class="visually-hidden">Modifier</span>
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
                                            class="btn btn-danger btn-sm btn-icon btn-soft-danger"
                                            aria-label="Supprimer l'entreprise <?= htmlspecialchars($e['nom']) ?>"
                                            title="Supprimer">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                        <span class="visually-hidden">Supprimer</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($entreprises)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-6 mb-2" aria-hidden="true"></i>
                            <p class="mb-0">Aucune entreprise enregistrée.</p>
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
<div class="modal fade" id="entrepriseModal" tabindex="-1" aria-hidden="true" aria-labelledby="entrepriseModalLabel">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title" id="entrepriseModalLabel">
            <i class="bi bi-building me-2" aria-hidden="true"></i>Détails de l'entreprise
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <div class="modal-body bg-light">

        <!-- LOGO ENTREPRISE (MVP : image statique) -->
        <div class="text-center mb-4">
            <div class="bg-white p-4 rounded-3 d-inline-block" style="border-radius: var(--radius-md);">
                <img 
                    src="/assets/img/logoEntreprise.png" 
                    alt="Logo entreprise"
                    class="img-fluid"
                    style="max-width: 140px;"
                >
            </div>
        </div>

        <div id="entrepriseModalContent" class="py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status" aria-label="Chargement en cours">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>

      </div>

      <div class="modal-footer border-top bg-white">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Fermer
        </button>
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
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    }

    bindDeleteConfirmations();
</script>
