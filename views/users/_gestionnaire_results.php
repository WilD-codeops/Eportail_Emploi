<?php 
use App\Core\Security;

/** @var array $users */
?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 admin-users-table">
                <thead class="table-light admin-users-head">
                    <tr>
                        <th>Nom</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th>Rôle</th>
                        <th class="d-none d-lg-table-cell">Dernier accès</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Aucun membre ne correspond aux filtres.
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
                                'gestionnaire' => 'bg-purple text-white',
                                'recruteur'    => 'bg-primary',
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

                            <td class="d-none d-lg-table-cell"><?= htmlspecialchars($u['dernier_acces'] ?? '—') ?></td>

                            <td class="text-end">

                                <!-- Voir -->
                                <button
                                    class="btn btn-primary btn-sm btn-icon btn-soft-primary js-user-details"
                                    data-id="<?= $id ?>"
                                    data-nom="<?= htmlspecialchars($name) ?>"
                                    data-email="<?= htmlspecialchars($u['email']) ?>"
                                    data-role="<?= htmlspecialchars($role) ?>"
                                    data-acces="<?= htmlspecialchars($u['dernier_acces'] ?? '—') ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#userModal"
                                    aria-label="Voir les détails de <?= htmlspecialchars($name) ?>"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Modifier -->
                                <a href="/dashboard/equipe/edit?id=<?= $id ?>"
                                   class="btn btn-primary btn-sm btn-icon btn-soft-primary"
                                   aria-label="Modifier <?= htmlspecialchars($name) ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- Supprimer -->
                                <form method="POST"
                                      action="/dashboard/equipe/delete"
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
                <p><strong>Dernier accès :</strong> ${btn.dataset.acces}</p>
            </div>
        `;
        document.getElementById('userModalContent').innerHTML = content;
    });
});
</script>