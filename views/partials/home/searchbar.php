<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$localisations = $refs['localisations'] ?? [];
$typesOffres = $refs['typesOffres'] ?? [];
?>

<!-- Bloc recherche rapide -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="h4 text-center mb-4 fw-semibold">
            Rechercher une offre
        </h2>

        <form class="row g-1 justify-content-center" method="GET" action="/offres">
            <div class="col-md-4">
                <input type="text" class="form-control" name="keyword" placeholder="Poste, mot-clé…">
            </div>

            <div class="col-md-3">
                <select class="form-select" name="localisation_id">
                    <option value="">Localisation</option>
                    <?php foreach ($localisations as $l): ?>
                        <option value="<?= $e($l['id']) ?>">
                            <?= $e($l['ville']) ?><?= !empty($l['region']) ? ' • ' . $e($l['region']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <select class="form-select" name="type_offre_id">
                    <option value="">Type de contrat</option>
                    <?php foreach ($typesOffres as $t): ?>
                        <option value="<?= $e($t['id']) ?>">
                            <?= $e($t['description'] ?? $t['code'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">
                    Rechercher
                </button>
            </div>
        </form>
    </div>
</section>