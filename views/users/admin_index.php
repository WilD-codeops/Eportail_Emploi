<?php

use App\Core\Security;

$users      = $users      ?? [];
$filters    = $filters    ?? [];
$page       = $page       ?? 1;
$pages      = $pages      ?? 1;
$perPage    = $perPage    ?? ($_GET['perPage'] ?? 10);
$kpi        = $kpi        ?? [];
$entreprises = $entreprises ?? [];

$filters = $filters + [
    'nom'           => null,
    'email'         => null,
    'role'          => null,
    'entreprise_id' => null,
    'dernier_acces' => null,
];

$action = "/admin/users";

?>

<!-- ===========================
     HEADER
=========================== -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 admin-users-header">
    <div>
        <h2 class="h4 mb-1 text-primary">Liste des utilisateurs</h2>
        <div class="text-muted small">
            Administration • gestion globale des comptes • <?= (int)$kpi['total'] ?> utilisateur(s)
        </div>
    </div>

    <a href="/admin/users/create" class="btn btn-primary btn-sm btn-lift">
        <i class="bi bi-plus-lg me-1"></i> Créer un utilisateur
    </a>
</div>

<!-- ===========================
     KPI CARDS (DASHBOARD STYLE)
=========================== -->
<div class="row g-3 mb-4">

    <!-- Total -->
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 admin-kpi-card" style="border-radius: var(--radius-md);">
            <div class="card-body text-center py-3">
                <div class="small text-muted mb-1">Total utilisateurs</div>
                <div class="fs-3 fw-bold" style="color: var(--color-primary-purple-dark);">
                    <?= (int)$kpi['total'] ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Admins -->
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 admin-kpi-card" style="border-radius: var(--radius-md);">
            <div class="card-body text-center py-3">
                <div class="small text-muted mb-1">Administrateurs</div>
                <div class="fs-3 fw-bold" style="color: var(--color-secondary-red-dark);">
                    <?= (int)$kpi['admins'] ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestionnaires -->
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 admin-kpi-card" style="border-radius: var(--radius-md);">
            <div class="card-body text-center py-3">
                <div class="small text-muted mb-1">Gestionnaires</div>
                <div class="fs-3 fw-bold" style="color: var(--color-primary-purple);">
                    <?= (int)$kpi['gestionnaires'] ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recruteurs -->
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 admin-kpi-card" style="border-radius: var(--radius-md);">
            <div class="card-body text-center py-3">
                <div class="small text-muted mb-1">Recruteurs</div>
                <div class="fs-3 fw-bold text-primary">
                    <?= (int)$kpi['recruteurs'] ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ===========================
     FILTRES
=========================== -->
<form method="GET" action="<?= htmlspecialchars($action) ?>" class="card shadow-sm mb-3 admin-users-filters">
    <div class="card-body">
        <div class="row g-2 align-items-end">

            <div class="col-12 col-md-4">
                <label class="form-label small mb-1">Nom / prénom</label>
                <input class="form-control" name="nom" value="<?= htmlspecialchars((string)$filters['nom']) ?>">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small mb-1">Email</label>
                <input class="form-control" name="email" value="<?= htmlspecialchars((string)$filters['email']) ?>">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Rôle</label>
                <select class="form-select" name="role">
                    <option value="">Tous</option>
                    <option value="admin"        <?= $filters['role']==='admin'?'selected':'' ?>>Admin</option>
                    <option value="gestionnaire" <?= $filters['role']==='gestionnaire'?'selected':'' ?>>Gestionnaire</option>
                    <option value="recruteur"    <?= $filters['role']==='recruteur'?'selected':'' ?>>Recruteur</option>
                    <option value="candidat"     <?= $filters['role']==='candidat'?'selected':'' ?>>Candidat</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">ID entreprise</label>
                <select class="form-select" name="entreprise_id" value="<?= htmlspecialchars((string)$filters['entreprise_id']) ?>">
                <option value="">Sélectionner</option>
                        <?php foreach ($entreprises as $e): ?>
                            <option 
                                value="<?= (int)$e['id'] ?>"
                                <?= (isset($old['entreprise_id']) && (string)$old['entreprise_id'] === (string)$e['id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Dernier accès après</label>
                <input class="form-control" type="date" name="dernier_acces" value="<?= htmlspecialchars((string)$filters['dernier_acces']) ?>">
            </div>

            <!-- Per page -->
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Par page</label>
                <select class="form-select" name="perPage">
                    <?php foreach ([10,20,50,100] as $n): ?>
                        <option value="<?= $n ?>" <?= $perPage==$n?'selected':'' ?>><?= $n ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="page" value="1">

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a class="btn btn-outline-secondary btn-sm btn-ghost" href="<?= htmlspecialchars($action) ?>">
                    Réinitialiser
                </a>
                <button class="btn btn-primary btn-sm btn-lift" type="submit">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>
            </div>

        </div>
    </div>
</form>

<!-- ===========================
     TABLEAU UTILISATEURS
=========================== -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-users-table">
                <thead class="table-light admin-users-head">
                    <tr>
                        <th>Nom</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th>Rôle</th>
                        <th class="d-none d-lg-table-cell">Entreprise</th>
                        <th class="d-none d-lg-table-cell">Dernier accès</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Aucun utilisateur ne correspond aux filtres.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <?php
                            $id   = (int)$u['id'];
                            $name = trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''));
                            $csrfKey = "user_delete_" . $id;
                            $csrfDel = Security::generateCsrfToken($csrfKey);

                            // Badge couleur selon rôle
                            $role = $u['role'] ?? '';
                            $badgeClass = match ($role) {
                                'admin'        => 'bg-danger',
                                'gestionnaire' => 'bg-purple text-white',
                                'recruteur'    => 'bg-primary',
                                'candidat'     => 'bg-success',
                                default        => 'bg-secondary',
                            };
                        ?>
                        <tr class="admin-user-row">
                            <td class="fw-semibold"><?= htmlspecialchars($name ?: '—') ?></td>

                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($u['email'] ?? '—') ?></td>

                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($role) ?>
                                </span>
                            </td>

                            <td class="d-none d-lg-table-cell"><?= htmlspecialchars($u['entreprise'] ?? '—') ?></td>

                            <td class="d-none d-lg-table-cell"><?= htmlspecialchars($u['dernier_acces'] ?? '—') ?></td>

                            <td class="text-end">

                                <!-- Voir (œil) -->
                                <button
                                    class="btn btn-primary btn-sm btn-icon btn-soft-primary js-user-details"
                                    data-id="<?= $id ?>"
                                    data-nom="<?= htmlspecialchars($name) ?>"
                                    data-email="<?= htmlspecialchars($u['email']) ?>"
                                    data-role="<?= htmlspecialchars($role) ?>"
                                    data-entreprise="<?= htmlspecialchars($u['entreprise'] ?? '—') ?>"
                                    data-acces="<?= htmlspecialchars($u['dernier_acces'] ?? '—') ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#userModal"
                                    aria-label="Voir les détails de <?= htmlspecialchars($name) ?>"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Modifier -->
                                <a href="/admin/users/edit?id=<?= $id ?>"
                                   class="btn btn-primary btn-sm btn-icon btn-soft-primary"
                                   aria-label="Modifier <?= htmlspecialchars($name) ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Supprimer -->
                                <form method="POST"
                                      action="/admin/users/delete"
                                      class="d-inline js-delete-form">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfDel) ?>">
                                    <input type="hidden" name="csrf_key" value="<?= htmlspecialchars($csrfKey) ?>">

                                    <button type="submit"
                                            class="btn btn-danger btn-sm btn-icon btn-soft-danger"
                                            aria-label="Supprimer <?= htmlspecialchars($name) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="card-footer">
        <nav aria-label="Pagination utilisateurs">
            <ul class="pagination justify-content-center mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&perPage=<?= $perPage ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</div>

<!-- ===========================
     MODAL DETAILS UTILISATEUR
=========================== -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Détails utilisateur</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="userModalContent" class="py-2 text-center text-muted">
            Chargement...
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>

    </div>
  </div>
</div>

<script>
/* SweetAlert delete */
function bindDeleteConfirmations(root = document) {
  root.querySelectorAll('.js-delete-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      if (typeof Swal === 'undefined') return form.submit();

      Swal.fire({
        icon: 'warning',
        title: 'Supprimer cet utilisateur ?',
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

/* Modal détails utilisateur */
document.querySelectorAll('.js-user-details').forEach(btn => {
    btn.addEventListener('click', () => {
        const content = `
            <div class="text-start">
                <p><strong>Nom :</strong> ${btn.dataset.nom}</p>
                <p><strong>Email :</strong> ${btn.dataset.email}</p>
                <p><strong>Rôle :</strong> ${btn.dataset.role}</p>
                <p><strong>Entreprise :</strong> ${btn.dataset.entreprise}</p>
                <p><strong>Dernier accès :</strong> ${btn.dataset.acces}</p>
            </div>
        `;
        document.getElementById('userModalContent').innerHTML = content;
    });
});
</script>
