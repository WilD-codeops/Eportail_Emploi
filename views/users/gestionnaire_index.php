<?php 
use App\Core\Security;
use App\Core\Auth;

/** @var array $entreprise */
/** @var array $users */
/** @var array $kpi */
?>

<!-- ===========================
     ENTREPRISE 
=========================== -->
<div class="card shadow-sm mb-4" style="border-radius: var(--radius-md);">
    <div class="card-body">

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">

            <!-- Logo entreprise -->
            <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">

                <div class="rounded p-2 d-flex align-items-center justify-content-center"
                     style="width: 70px; height: 70px; background: var(--color-light-100); border: 1px solid var(--color-light-300); border-radius: var(--radius-md);">

                    <img src="/assets/img/company_logo_generique.png"
                         alt="Logo de l’entreprise <?= htmlspecialchars($entreprise['nom']) ?>"
                         class="img-fluid"
                         style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>

                <div>
                    <h2 class="h5 mb-1"><?= htmlspecialchars($entreprise['nom']) ?></h2>
                    <div class="text-muted small">
                        Entreprise ID : <?= (int)$entreprise['id'] ?>
                    </div>
                </div>

            </div>

            <!-- Boutons -->
            <div class="d-flex gap-2">
                <a href="/dashboard/entreprise/edit" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Modifier
                </a>

                <a href="/dashboard/equipe/create" class="btn btn-success btn-sm">
                    <i class="bi bi-person-plus me-1"></i> Ajouter un membre
                </a>
            </div>

        </div>

        <hr class="my-3">

        <!-- Infos entreprise -->
        <div class="row g-3">

            <div class="col-md-6">
                <div><strong>SIRET :</strong> <?= htmlspecialchars($entreprise['siret']) ?></div>
                <div><strong>Secteur :</strong> <?= htmlspecialchars($entreprise['secteur'] ?? '—') ?></div>
                <div><strong>Email :</strong> <?= htmlspecialchars($entreprise['email'] ?? '—') ?></div>
            </div>

            <div class="col-md-6">
                <div>
                    <strong>Adresse :</strong>
                    <?= htmlspecialchars($entreprise['adresse']) ?>,
                    <?= htmlspecialchars($entreprise['code_postal']) ?>
                    <?= htmlspecialchars($entreprise['ville']) ?>
                </div>
                <div><strong>Pays :</strong> <?= htmlspecialchars($entreprise['pays']) ?></div>
                <div><strong>Taille :</strong> <?= (int)$entreprise['taille'] ?> salarié<?= $entreprise['taille'] > 1 ? 's' : '' ?></div>    
            </div>

        </div>

    </div>
</div>


<!-- ===========================
     KPI CARDS (GESTIONNAIRE)
=========================== -->
<div class="row g-3 mb-4">

    <!-- Total membres -->
    <div class="col-6 col-md-4">
        <div class="card shadow-sm border-0 admin-kpi-card" style="border-radius: var(--radius-md);">
            <div class="card-body text-center py-3">
                <div class="small text-muted mb-1">Total membres</div>
                <div class="fs-3 fw-bold" style="color: var(--color-primary-purple-dark);">
                    <?= (int)$kpi['total'] ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestionnaires -->
    <div class="col-6 col-md-4">
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
    <div class="col-6 col-md-4">
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
     LISTE DES UTILISATEURS
=========================== -->
<?php require __DIR__ . "/_gestionnaire_results.php"; ?>