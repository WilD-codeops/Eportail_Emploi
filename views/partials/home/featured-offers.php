

<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$latestOffers = $latestOffers ?? [];
?>

<!-- ===================== LAST OFFERS SECTION ===================== -->
<section class="featured-offers-hero py-5" aria-labelledby="featured-offers-title">
    <div class="container text-center text-dark mb-5">
        <h2 class="fw-bold mb-2">Offres à la une</h2>
        <p class="opacity-75 small">
            Les opportunités les plus récentes sélectionnées pour vous.
        </p>
    </div>

    <div class="container">
        <div class="row g-4 justify-content-center">

            <?php if (empty($latestOffers)): ?>
                <div class="col-12">
                    <p class="text-muted text-center">Aucune offre disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($latestOffers as $o): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="card h-100 shadow-sm" aria-label="Fiche offre">
                            <div class="card-body d-flex flex-column">

                                <div class="d-flex align-items-center mb-3">
                                    <?php $logo = $o['logo'] ?? '/assets/img/company_logo_generique.png'; ?>
                                    <img src="<?= $e($logo) ?>"
                                         alt="Logo <?= $e($o['entreprise_nom']) ?>"
                                         class="rounded-circle me-3"
                                         style="width: 55px; height: 55px; object-fit: cover;">
                                    <div>
                                        <h3 class="h6 mb-0"><?= $e($o['entreprise_nom']) ?></h3>
                                        <small class="text-muted"><?= $e($o['localisation'] ?? '') ?></small>
                                    </div>
                                </div>

                                <h4 class="h5 mb-2"><?= $e($o['titre']) ?></h4>

                                <ul class="list-unstyled text-muted small mb-3">
                                    <li><i class="bi bi-briefcase me-1"></i> <?= $e($o['type_offre_description'] ?? '') ?></li>
                                    <?php if (!empty($o['salaire'])): ?>
                                        <li><i class="bi bi-cash-stack me-1"></i> <?= $e($o['salaire']) ?></li>
                                    <?php endif; ?>
                                </ul>

                                <a href="/offres/show?id=<?= (int)$o['id'] ?>"
                                   class="btn btn-primary mt-auto"
                                   aria-label="Voir l’offre <?= $e($o['titre']) ?>">
                                    Voir l’offre
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>