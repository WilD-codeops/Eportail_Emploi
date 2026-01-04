<?php
declare(strict_types=1);

/**
 * ==========================================================
 *  PARTIAL : FILTRES OFFRES (GET)
 *  Dépend des variables fournies par list.php :
 *  - $basePath, $keyword, $statut, $typeId, $perPage
 *  - $statusOptions, $perPageOptions, $refs
 * ==========================================================
 */
?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="<?= htmlspecialchars($basePath) ?>" class="row g-2 align-items-end">

            <!-- Keyword : recherche dans titre + description -->
            <div class="col-12 col-md-5">
                <label class="form-label mb-1">Recherche</label>
                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="Titre ou description…"
                    value="<?= htmlspecialchars($keyword) ?>"
                >
            </div>

            <!-- Statut : archive visible seulement admin (géré via $statusOptions) -->
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Statut</label>
                <select class="form-select" name="statut">
                    <?php foreach ($statusOptions as $k => $label): ?>
                        <option value="<?= htmlspecialchars($k) ?>" <?= ($statut === $k) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Type offre : alimenté via référentiel typesOffres -->
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Type</label>
                <select class="form-select" name="type_offre_id">
                    <option value="0">Tous types</option>
                    <?php foreach (($refs['typesOffres'] ?? []) as $t): ?>
                        <?php $id = (int)($t['id'] ?? 0); ?>
                        <option value="<?= $id ?>" <?= ($typeId === $id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($t['code'] ?? '') . ' — ' . ($t['description'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- PerPage : nombre de résultats par page -->
            <div class="col-12 col-md-1">
                <label class="form-label mb-1">Par page</label>
                <select class="form-select" name="perPage">
                    <?php foreach ($perPageOptions as $n): ?>
                        <option value="<?= $n ?>" <?= ($perPage === $n) ? 'selected' : '' ?>>
                            <?= $n ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- À chaque filtre -> revenir page 1 -->
            <input type="hidden" name="page" value="1">

            <div class="col-12 d-flex justify-content-end gap-2">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-funnel me-1"></i> Filtrer
                </button>

                <!-- Réinitialiser : retourne sur basePath sans query string -->
                <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($basePath) ?>">
                    Réinitialiser
                </a>
            </div>

        </form>
    </div>
</div>
