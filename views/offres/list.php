<?php
use App\Core\Security;

$isAdmin   = ($mode ?? '') === 'admin';
$title     = $title ?? ($isAdmin ? 'Gestion des offres' : 'Mes offres');

$createUrl = $isAdmin ? "/admin/offres/create" : "/dashboard/offres/create";
$editBase  = $isAdmin ? "/admin/offres/edit"   : "/dashboard/offres/edit";
$delBase   = $isAdmin ? "/admin/offres/delete" : "/dashboard/offres/delete";

$items = $items ?? [];

// Helpers
$badge = function (?string $statut): array {
    return match ($statut) {
        'active'   => ['label' => 'Active',   'class' => 'bg-success'],
        'inactive' => ['label' => 'Inactive', 'class' => 'bg-secondary'],
        'archive'  => ['label' => 'Archivée', 'class' => 'bg-dark'],
        default    => ['label' => '—',        'class' => 'bg-light text-dark'],
    };
};

$fmtDate = function (?string $dt): string {
    if (!$dt) return '—';
    // Si tu as déjà format YYYY-MM-DD HH:ii:ss : on affiche sans casser
    return htmlspecialchars($dt);
};

// Flash via reason (SweetAlert dans partial alerts.php)
$reason = $_GET['reason'] ?? null;
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1"><?= htmlspecialchars($title) ?></h1>
        <div class="text-muted small">
            <?= $isAdmin ? "Administration • gestion globale des offres" : "Espace entreprise • offres rattachées à votre entreprise" ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="<?= htmlspecialchars($createUrl) ?>">
            <i class="bi bi-plus-lg me-1"></i> Créer une offre
        </a>
    </div>
</div>

<?php if (empty($items)): ?>
    <div class="alert alert-info mb-0">
        Aucune offre pour le moment.
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
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
                            <th class="text-end" style="width:170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $offre): ?>
                        <?php
                        $id = (int)($offre['id'] ?? 0);

                        $statut = (string)($offre['statut'] ?? '');
                        $b = $badge($statut);

                        // CSRF delete 1 token par offre (important, token one-time)
                        $csrfKey = "offres_delete_" . $id;
                        $csrfDel = Security::generateCsrfToken($csrfKey);

                        $editUrl = $editBase . "?id=" . urlencode((string)$id);
                        $delUrl  = $delBase  . "?id=" . urlencode((string)$id);
                        ?>
                        <tr>
                            <td class="text-muted"><?= htmlspecialchars((string)$id) ?></td>

                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($offre['titre'] ?? '') ?></div>
                                <div class="text-muted small">
                                    <?= htmlspecialchars($offre['domaine_emploi'] ?? '') ?>
                                </div>
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

                            <td><?= $fmtDate($offre['date_creation'] ?? null) ?></td>
                            <td><?= $fmtDate($offre['date_modification'] ?? null) ?></td>

                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($editUrl) ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form method="POST" action="<?= htmlspecialchars($delUrl) ?>" class="d-inline js-delete-form">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfDel) ?>">
                                    <input type="hidden" name="csrf_key" value="<?= htmlspecialchars($csrfKey) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Confirmation delete via SweetAlert2 (chargé dans dashboard layout)
    document.querySelectorAll('.js-delete-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
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
    </script>
<?php endif; ?>
