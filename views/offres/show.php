<?php
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$titre = $offre['titre'] ?? "Offre d'emploi";
$logo  = $offre['logo'] ?? '/assets/img/company_logo_generique.png';
?>

<?php require __DIR__ . '/../partials/banniere.php'; ?>

<section class="container py-5">
    <div class="row gy-5">

        <!-- ===================== COLONNE GAUCHE ===================== -->
        <aside class="col-12 col-lg-4">

            <!-- Logo entreprise -->
            <div class="text-center mb-4">
                <a href="/entreprises/show?id=<?= (int)($offre['entreprise_id'] ?? 0) ?>" style="text-decoration: none;">
                    <img src="<?= $e($logo) ?>"
                         alt="Logo entreprise"
                         class="img-fluid rounded-circle shadow-sm"
                         style="width: 140px; height: 140px; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                         onmouseover="this.style.transform='scale(1.05)'"
                         onmouseout="this.style.transform='scale(1)'"
                         title="Voir le profil de l'entreprise">
                </a>
            </div>

            <!-- Carte entreprise -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h2 class="h5 text-secondary mb-3">Entreprise</h2>

                    <ul class="list-unstyled small">

                        <li class="mb-2">
                            <i class="bi bi-building text-primary me-1"></i>
                            <strong>Nom :</strong> <?= $e($offre['entreprise_nom']) ?>
                        </li>

                        <?php if (!empty($offre['localisation'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-geo-alt text-primary me-1"></i>
                                <strong>Localisation :</strong> <?= $e($offre['localisation']) ?>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($offre['salaire'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-cash text-primary me-1"></i>
                                <strong>Salaire :</strong> <?= $e($offre['salaire']) ?> €
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($offre['duree_contrat'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-clock text-primary me-1"></i>
                                <strong>Durée :</strong> <?= $e($offre['duree_contrat']) ?> mois
                            </li>
                        <?php endif; ?>

                        <li class="mb-2">
                            <i class="bi bi-flag text-primary me-1"></i>
                            <strong>Statut :</strong> <?= $e($offre['statut']) ?>
                        </li>

                    </ul>
                </div>
            </div>

        </aside>

        <!-- ===================== COLONNE DROITE ===================== -->
        <div class="col-12 col-lg-8">

            <!-- Titre + badges -->
            <div class="mb-4">
                <h2 class="h4 fw-bold mb-3"><?= $e($offre['titre']) ?></h2>

                <div class="d-flex flex-wrap gap-2">

                    <?php if (!empty($offre['type_offre_description'])): ?>
                        <span class="badge bg-primary-subtle text-primary">
                            <?= $e($offre['type_offre_description']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($offre['domaine_emploi'])): ?>
                        <span class="badge bg-info-subtle text-info">
                            <?= $e($offre['domaine_emploi']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($offre['niveau_qualification'])): ?>
                        <span class="badge bg-warning-subtle text-warning">
                            <?= $e($offre['niveau_qualification']) ?>
                        </span>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Bloc infos -->
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">

                    <h3 class="h5 text-secondary mb-3">Informations clés</h3>

                    <div class="row small g-3">

                        <?php if (!empty($offre['date_debut'])): ?>
                            <div class="col-md-6">
                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                <strong>Date de début :</strong><br>
                                <?= $e($offre['date_debut']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($offre['date_fin'])): ?>
                            <div class="col-md-6">
                                <i class="bi bi-calendar-check text-primary me-1"></i>
                                <strong>Date de fin :</strong><br>
                                <?= $e($offre['date_fin']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($offre['salaire'])): ?>
                            <div class="col-md-6">
                                <i class="bi bi-cash text-primary me-1"></i>
                                <strong>Salaire :</strong><br>
                                <?= $e($offre['salaire']) ?> €
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($offre['duree_contrat'])): ?>
                            <div class="col-md-6">
                                <i class="bi bi-clock text-primary me-1"></i>
                                <strong>Durée :</strong><br>
                                <?= $e($offre['duree_contrat']) ?> mois
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
            </section>

            <!-- Description -->
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h3 class="h5 text-secondary mb-3">Description du poste</h3>
                    <p class="text-readable sm">
                        <?= nl2br($e($offre['description'] ?? 'Aucune description fournie.')) ?>
                    </p>
                </div>
            </section>

            <!-- Bouton retour -->
            <div class="d-flex gap-2 flex-wrap">
                <a 
                    href="/" 
                    class="btn btn-primary px-4 py-2" 
                    aria-label="Retour à l'accueil"
                    style="border-radius: var(--radius-md);"
                >
                    <i class="bi bi-arrow-left-circle me-2"></i>
                    Retour à l'accueil
                </a>
                <a 
                    href="/offres" 
                    class="btn btn-primary px-4 py-2" 
                    aria-label="Retour à la liste des offres"
                    style="border-radius: var(--radius-md);"
                >
                    <i class="bi bi-list-ul me-2"></i>
                    Offres
                </a>
            </div>

        </div>

    </div>
</section>