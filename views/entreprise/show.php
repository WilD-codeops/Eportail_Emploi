<?php
$e = static fn ($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
$titre = $entreprise['nom'] ?? 'Entreprise';
?>

<?php require __DIR__ . '/../partials/banniere.php'; ?>

<section class="container py-5">
    <div class="row gy-5">

        <!-- ===================== COLONNE GAUCHE ===================== -->
        <aside class="col-12 col-lg-4">

            <!-- Logo -->
            <div class="text-center mb-4">
                <img src="<?= $e($entreprise['logo'] ?? '/assets/img/company_logo_generique.png') ?>"
                     alt="Logo de l’entreprise"
                     class="img-fluid rounded-circle shadow-sm"
                     style="width: 140px; height: 140px; object-fit: cover;">
            </div>

            <!-- Bloc infos entreprise -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h2 class="h5 text-secondary mb-3">Informations clés</h2>

                    <ul class="list-unstyled small">

                        <?php if (!empty($entreprise['secteur'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-briefcase text-primary me-1"></i>
                                <strong>Secteur :</strong> <?= $e($entreprise['secteur']) ?>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($entreprise['taille'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-people text-primary me-1"></i>
                                <strong>Taille :</strong> <?= $e($entreprise['taille']) ?> salariés
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($entreprise['ville']) || !empty($entreprise['pays'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-geo-alt text-primary me-1"></i>
                                <strong>Localisation :</strong>
                                <?= $e($entreprise['ville'] ?? '') ?>
                                <?= (!empty($entreprise['ville']) && !empty($entreprise['pays'])) ? ' - ' : '' ?>
                                <?= $e($entreprise['pays'] ?? '') ?>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($entreprise['site_web'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-globe text-primary me-1"></i>
                                <strong>Site web :</strong>
                                <a href="<?= $e($entreprise['site_web']) ?>" target="_blank" rel="noopener noreferrer">
                                    <?= $e($entreprise['site_web']) ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($entreprise['email'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-envelope text-primary me-1"></i>
                                <strong>Email :</strong>
                                <a href="mailto:<?= $e($entreprise['email']) ?>"><?= $e($entreprise['email']) ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($entreprise['telephone'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-telephone text-primary me-1"></i>
                                <strong>Téléphone :</strong> <?= $e($entreprise['telephone']) ?>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($entreprise['siret'])): ?>
                            <li class="mb-2">
                                <i class="bi bi-building text-primary me-1"></i>
                                <strong>SIRET :</strong> <?= $e($entreprise['siret']) ?>
                            </li>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>

            <?php if (!empty($offres)): ?>
                <a href="/offres?entreprise_id=<?= (int)($entreprise['id'] ?? 0) ?>" class="btn btn-primary w-100">Voir les offres (<?= count($offres) ?>)</a>
            <?php endif; ?>

        </aside>

        <!-- ===================== COLONNE DROITE ===================== -->
        <div class="col-12 col-lg-8">

            <!-- À propos -->
            <section class="mb-5">
                <h2 class="h4 text-secondary mb-3">À propos</h2>
                <p class="text-readable"><?= nl2br($e($entreprise['description'] ?? 'Pas de description disponible.')) ?></p>
            </section>

            <!-- ===================== OFFRES ===================== -->
            <section id="offers">
                <h2 class="h4 text-secondary mb-3">Offres d’emploi</h2>

                <?php if (empty($offres)): ?>
                    <p class="text-muted">Aucune offre pour le moment.</p>

                <?php else: ?>
                    <div class="row g-4">

                        <?php foreach ($offres as $offre): ?>
                            <div class="col-12 col-md-6">

                                <!-- ======= CARTE OFFRE (STYLE ENTREPRISE) ======= -->
                                <article class="card h-100 shadow-sm border-0 offer-card">
                                    <div class="card-body d-flex flex-column">

                                        <!-- Titre -->
                                        <h3 class="h6 fw-bold mb-2"><?= $e($offre['titre']) ?></h3>

                                        <!-- Description courte -->
                                        <p class="text-muted small mb-3">
                                            <?= $e(mb_strimwidth($offre['description'] ?? '', 0, 120, '…')) ?>
                                        </p>

                                        <!-- Badges -->
                                        <div class="d-flex flex-wrap gap-2 mb-3">

                                            <?php if (!empty($offre['offre_code'])): ?>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    <?= $e($offre['offre_code']) ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($offre['niveau_qualification'])): ?>
                                                <span class="badge bg-info-subtle text-info">
                                                    <?= $e($offre['niveau_qualification']) ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($offre['localisation'])): ?>
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    <?= $e($offre['localisation']) ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($offre['salaire'])): ?>
                                                <span class="badge bg-success-subtle text-success">
                                                    <?= $e($offre['salaire']) ?> €
                                                </span>
                                            <?php endif; ?>

                                        </div>

                                        <!-- Bouton -->
                                        <a href="/offres/show?id=<?= $offre['id'] ?>"
                                           class="btn btn-primary btn-sm mt-auto"
                                           aria-label="Voir l’offre <?= $e($offre['titre']) ?>">
                                            Voir l’offre
                                        </a>

                                    </div>
                                </article>

                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php endif; ?>
            </section>

        </div>
    </div>
</section>