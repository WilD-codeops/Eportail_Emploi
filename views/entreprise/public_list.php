<?php
// ======================================================================
// Vue : Liste des entreprises (espace public)
// ----------------------------------------------------------------------
// Cette page affiche la liste des entreprises partenaires de manière
// responsive et accessible. Les données suivantes sont fournies par
// le contrôleur :
//   - $entreprises : tableau d’entreprises (chaque élément est un tableau
//     associatif avec notamment les clés id, nom, secteur, ville, pays,
//     description, logo, taille). Toutes les valeurs peuvent être nulles.
//   - $secteurs    : tableau des secteurs disponibles pour filtrer (id et libelle).
//   - $page        : numéro de la page courante pour la pagination.
//   - $pages       : nombre total de pages.
// Les filtres utilisent la méthode GET afin de conserver l’URL partageable
// et d’être indexables par les moteurs de recherche.
// ----------------------------------------------------------------------

// Petite fonction utilitaire pour échapper proprement les sorties HTML.
$e = static fn ($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

?>

<?php require __DIR__ . '/../partials/banniere.php'; ?>

<section class="py-5 bg-light" aria-labelledby="entreprises-heading">
  <div class="container">

    <?php
      $count = count($entreprises);
    ?>

    <div class="mb-4">
        <?php if ($count === 0): ?>
            <p class="text-muted fst-italic">Aucune entreprise trouvée.</p>
        <?php elseif ($count === 1): ?>
            <p class="fw-semibold text-secondary">1 entreprise trouvée</p>
        <?php else: ?>
            <p class="fw-semibold text-secondary"><?= $count ?> entreprises trouvées</p>
        <?php endif; ?>
    </div>

    <!-- Formulaire de filtres -->
    <form method="GET" class="mb-4" id="entreprise-filters-form" aria-label="Filtres de recherche"></form>
    <!-- Formulaire de filtres -->
    <form method="GET" class="mb-4" id="entreprise-filters-form" aria-label="Filtres de recherche">
      <div class="row g-3">
        <!-- Filtre par nom -->
        <div class="col-md-4">
          <label for="filter-nom" class="form-label fw-semibold">Nom</label>
          <input
            type="text"
            id="filter-nom"
            name="nom"
            class="form-control"
            placeholder="Rechercher..."
            value="<?= $e($_GET['nom'] ?? '') ?>"
          >
        </div>

        <!-- Filtre par secteur -->
        <div class="col-md-4">
          <label for="filter-secteur" class="form-label fw-semibold">Secteur</label>
          <select id="filter-secteur" name="secteur" class="form-select">
            <option value="">Tous les secteurs</option>
            <?php foreach ($secteurs as $s): ?>
              <option value="<?= $e($s['id']) ?>" <?= (($_GET['secteur'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                <?= $e($s['libelle']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Filtre par ville -->
        <div class="col-md-4">
          <label for="filter-ville" class="form-label fw-semibold">Ville</label>
          <input
            type="text"
            id="filter-ville"
            name="ville"
            class="form-control"
            placeholder="Ex : Paris"
            value="<?= $e($_GET['ville'] ?? '') ?>"
          >
        </div>

        <!-- Filtre de tri -->
        <div class="col-md-4">
          <label for="filter-tri" class="form-label fw-semibold">Tri</label>
          <select id="filter-tri" name="tri" class="form-select">
            <option value="">Aucun</option>
            <option value="az" <?= (($_GET['tri'] ?? '') === 'az') ? 'selected' : '' ?>>Nom A → Z</option>
            <option value="za" <?= (($_GET['tri'] ?? '') === 'za') ? 'selected' : '' ?>>Nom Z → A</option>
            <option value="newest" <?= (($_GET['tri'] ?? '') === 'newest') ? 'selected' : '' ?>>Les plus récents</option>
            <option value="oldest" <?= (($_GET['tri'] ?? '') === 'oldest') ? 'selected' : '' ?>>Les plus anciens</option>
          </select>
        </div>

        <!-- Boutons -->
        <div class="col-12 text-end mt-2">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i>
            <span class="visually-hidden">Lancer la recherche</span>
            Filtrer
          </button>
          <a href="/entreprises" class="btn btn-secondary">Réinitialiser</a>
        </div>
      </div>
    </form>

    <!-- Grille d’entreprises -->
    <div class="row g-4">
      <?php foreach ($entreprises as $entreprise): ?>
        <div class="col-12 col-md-6 col-lg-4">
          <!-- Carte entreprise -->
          <article class="card h-100 shadow-sm" aria-label="Fiche entreprise">
            <div class="card-body d-flex flex-column">
              <!-- En-tête avec logo et nom -->
              <div class="d-flex align-items-center mb-3">
                <?php
                  // Utilisation d’une image générique si aucun logo n’est fourni.
                  $logo = $entreprise['logo'] ?? '/assets/img/company_logo_generique.png';
                ?>
                <img
                  src="<?= $e($logo) ?>"
                  alt="Logo <?= $e($entreprise['nom'] ?? 'entreprise') ?>"
                  class="rounded-circle me-3"
                  style="width: 60px; height: 60px; object-fit: cover;"
                >
                <div>
                  <h2 class="h6 mb-0"><?= $e($entreprise['nom'] ?? 'Nom manquant') ?></h2>
                  <?php if (!empty($entreprise['secteur'])): ?>
                    <small class="text-muted"><?= $e($entreprise['secteur']) ?></small>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Description courte -->
              <p class="mb-3 text-muted medium">
                <?= $e(mb_strimwidth($entreprise['description'] ?? '', 0, 120, '…')) ?>
              </p>

              <!-- Informations complémentaires -->
              <ul class="list-unstyled mb-3">
                <?php if (!empty($entreprise['ville']) || !empty($entreprise['pays'])): ?>
                  <li>
                    <i class="bi bi-geo-alt me-1"></i>
                    <?= $e($entreprise['ville'] ?? '') ?>
                    <?= (!empty($entreprise['ville']) && !empty($entreprise['pays'])) ? ', ' : '' ?>
                    <?= $e($entreprise['pays'] ?? '') ?>
                  </li>
                <?php endif; ?>
                <?php if (!empty($entreprise['taille'])): ?>
                  <li>
                    <i class="bi bi-people me-1"></i>
                    <?= $e($entreprise['taille']) ?> salarié<?= $entreprise['taille'] > 1 ? 's' : '' ?>
                  </li>
                <?php endif; ?>
              </ul>

              <!-- Bouton voir plus -->
              <a
                href="/entreprises/show?id=<?= (int)($entreprise['id'] ?? 0) ?>"
                class="btn btn-primary mt-auto"
                aria-label="Voir les détails de <?= $e($entreprise['nom'] ?? 'cette entreprise') ?>"
              >
                Voir l’entreprise
              </a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>

      <?php if (empty($entreprises)): ?>
        <div class="col-12">
          <p class="text-muted text-center">Aucune entreprise ne correspond à vos critères.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php
      // On inclut la pagination à la fin, elle utilise $page, $pages et $_GET
      include __DIR__ . '/../partials/pagination.php';
    ?>
  </div>
</section>