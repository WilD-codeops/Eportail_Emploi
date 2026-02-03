<?php
/**
 * Partial : Filtres de recherche pour la liste des entreprises
 * Variables attendues :
 * - $secteurs (array)
 * - $gestionnaires (array)
 * - $_GET (pour conserver les valeurs sélectionnées)
 */
?>

<div class="card shadow-sm mb-4">
    <form method="GET" class="card-body">

        <div class="row g-2">

            <!-- Recherche par nom -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Nom</label>
                <input type="text"
                       name="nom"
                       class="form-control"
                       placeholder="Rechercher..."
                       value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>">
            </div>

            <!-- Secteur -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Secteur</label>
                <select name="secteur" class="form-select">
                    <option value="">Tous</option>
                    <?php foreach ($secteurs as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            <?= (($_GET['secteur'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ville -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Ville</label>
                <input type="text"
                       name="ville"
                       class="form-control"
                       placeholder="Ex : Paris"
                       value="<?= htmlspecialchars($_GET['ville'] ?? '') ?>">
            </div>


            <!-- Gestionnaire -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Gestionnaire</label>
                <input type="text"
                       name="gestionnaire"
                       class="form-control"
                       placeholder="Nom du gestionnaire"
                       value="<?= htmlspecialchars($_GET['gestionnaire'] ?? '') ?>">
            </div>

            <!-- Tri -->
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tri</label>
                <select name="tri" class="form-select">
                    <option value="">Aucun</option>
                    <option value="az" <?= (($_GET['tri'] ?? '') === 'az') ? 'selected' : '' ?>>Nom A → Z</option>
                    <option value="za" <?= (($_GET['tri'] ?? '') === 'za') ? 'selected' : '' ?>>Nom Z → A</option>
                    <option value="newest" <?= (($_GET['tri'] ?? '') === 'newest') ? 'selected' : '' ?>>Les plus récents</option>
                    <option value="oldest" <?= (($_GET['tri'] ?? '') === 'oldest') ? 'selected' : '' ?>>Les plus anciens</option>
                </select>
            </div>

            <!-- Boutons -->
            <div class="col-12 text-end mt-2">
                <button class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>

                <a href="/admin/entreprises" class="btn btn-secondary">
                    Réinitialiser
                </a>
            </div>

        </div>

    </form>
</div>