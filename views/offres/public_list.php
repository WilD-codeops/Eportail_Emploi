<?php
// Variables fournies par le contrôleur :
// $offres : tableau d’offres
// $typesOffres, $domainesEmploi, $localisations : référentiels
// $page, $pages : pagination
// $filters : filtres GET
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

?>
<?php require __DIR__ . '/../partials/banniere.php'; ?>

<section class="py-5 bg-light" aria-labelledby="offres-heading">
  <div class="container">

    <?php
      $count = count($data['items']);
    ?>

    <div class="mb-4">
        <?php if ($count === 0): ?>
            <p class="text-muted fst-italic">Aucune Offre trouvée.</p>
        <?php elseif ($count === 1): ?>
            <p class="fw-semibold text-secondary">1 Offre trouvée</p>
        <?php else: ?>
            <p class="fw-semibold text-secondary"><?= $count ?> Offres trouvées</p>
        <?php endif; ?>
    </div>

    <!-- Formulaire de filtres -->
    <form method="GET" class="mb-4" id="entreprise-filters-form" aria-label="Filtres de recherche"></form>
    <!-- ===========================
         FILTRES
    ============================ -->
    <form method="GET" class="card shadow-sm mb-4" aria-label="Filtres de recherche">
      <div class="card-body">
        <div class="row g-3">

          <!-- Mot clé -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Mot-clé</label>
            <input type="text" name="keyword" class="form-control"
                   placeholder="Titre, description..."
                   value="<?= $e($filters['keyword'] ?? '') ?>">
          </div>

          <!-- Type d’offre -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Type d’offre</label>
            <select name="type_offre_id" class="form-select">
              <option value="">Tous</option>
              <?php foreach ($data['items']['typesOffres'] as $t): ?>
                <option value="<?= $e($t['id']) ?>"
                  <?= (($filters['type_offre_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                  <?= $e($t['description']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Localisation -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Localisation</label>
            <select name="localisation_id" class="form-select">
              <option value="">Toutes</option>
              <?php foreach ($localisations as $l): ?>
                <option value="<?= $e($l['id']) ?>"
                  <?= (($filters['localisation_id'] ?? '') == $l['id']) ? 'selected' : '' ?>>
                  <?= $e($l['ville']) ?> (<?= $e($l['pays']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Domaine -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Domaine</label>
            <select name="domaine_emploi_id" class="form-select">
              <option value="">Tous</option>
              <?php foreach ($domainesEmploi as $d): ?>
                <option value="<?= $e($d['id']) ?>"
                  <?= (($filters['domaine_emploi_id'] ?? '') == $d['id']) ? 'selected' : '' ?>>
                  <?= $e($d['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Tri -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Tri</label>
            <select name="tri" class="form-select">
              <option value="">Aucun</option>
              <option value="newest" <?= (($filters['tri'] ?? '') === 'newest') ? 'selected' : '' ?>>Plus récentes</option>
              <option value="oldest" <?= (($filters['tri'] ?? '') === 'oldest') ? 'selected' : '' ?>>Plus anciennes</option>
            </select>
          </div>

          <!-- Boutons -->
          <div class="col-12 text-end mt-2">
            <button class="btn btn-primary">
              <i class="bi bi-search"></i> Filtrer
            </button>
            <a href="/offres" class="btn btn-secondary">Réinitialiser</a>
          </div>

        </div>
      </div>
    </form>

    <!-- ===========================
         LISTE DES OFFRES
    ============================ -->
    <div class="row g-4">

      <?php foreach ($data['items'] as $o): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100 shadow-sm" aria-label="Fiche offre">
            <div class="card-body d-flex flex-column">

              <!-- Logo + entreprise -->
              <div class="d-flex align-items-center mb-3">
                <?php
                  $logo = $o['logo'] ?? '/assets/img/company_logo_generique.png';
                ?>
                <img src="<?= $e($logo) ?>"
                     alt="Logo <?= $e($o['entreprise_nom']) ?>"
                     class="rounded-circle me-3"
                     style="width: 55px; height: 55px; object-fit: cover;">
                <div>
                  <h2 class="h6 mb-0"><?= $e($o['entreprise_nom']) ?></h2>
                  <small class="text-muted"><?= $e($o['localisation'] ?? '') ?></small>
                </div>
              </div>

              <!-- Titre -->
              <h3 class="h5 mb-2"><?= $e($o['titre']) ?></h3>

              <!-- Infos -->
              <ul class="list-unstyled text-muted small mb-3">
                <li><i class="bi bi-briefcase me-1"></i> <?= $e($o['type_offre_description'] ?? '') ?></li>
                <?php if (!empty($o['date_debut'])): ?>
                  <li><i class="bi bi-calendar-event me-1"></i> Début : <?= $e($o['date_debut']) ?></li>
                <?php endif; ?>
                <?php if (!empty($o['date_fin'])): ?>
                  <li><i class="bi bi-calendar-check me-1"></i> Fin : <?= $e($o['date_fin']) ?></li>
                <?php endif; ?>
              </ul>

              <!-- Bouton -->
              <a href="/offres/show?id=<?= (int)$o['id'] ?>"
                 class="btn btn-primary mt-auto"
                 aria-label="Voir l’offre <?= $e($o['titre']) ?>">
                Voir l’offre
              </a>

            </div>
          </article>
        </div>
      <?php endforeach; ?>

      <?php if (empty($data['items'])): ?>
        <div class="col-12">
          <p class="text-muted text-center">Aucune offre ne correspond à vos critères.</p>
        </div>
      <?php endif; ?>

    </div>

    <!-- Pagination -->
    <?php include __DIR__ . '/../partials/pagination.php'; ?>

  </div>
</section>