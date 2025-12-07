<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Entreprises partenaires</h1>
        <p class="text-muted mb-4">
            Découvrez les entreprises qui recrutent sur EPortailEmploi.
        </p>

        <div class="row g-4">
            <?php foreach ($entreprises as $e): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 card-title mb-1">
                                <?= htmlspecialchars($e['nom']) ?>
                            </h2>
                            <p class="small text-muted mb-2">
                                <?= htmlspecialchars($e['secteur'] ?? 'Secteur non renseigné') ?>
                            </p>
                            <p class="small mb-1">
                                <?= htmlspecialchars($e['ville'] ?? '') ?>
                                <?php if (!empty($e['pays'])): ?>
                                    (<?= htmlspecialchars($e['pays']) ?>)
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($e['taille'])): ?>
                                <p class="small text-muted mb-2">
                                    Taille : <?= htmlspecialchars($e['taille']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($e['description'])): ?>
                                <p class="small text-muted">
                                    <?= nl2br(htmlspecialchars(mb_strimwidth($e['description'], 0, 140, '…'))) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>

            <?php if (empty($entreprises)): ?>
                <div class="col-12">
                    <p class="text-muted">Aucune entreprise enregistrée pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>