<?php
// Variables fournies par le contrôleur :
// $data['items'] (liste d'offres), $data['pagination'], $filters, $refs
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

// Référentiels
$typesOffres    = $refs['typesOffres']    ?? [];
$domainesEmploi = $refs['domainesEmploi'] ?? [];
$localisations  = $refs['localisations']  ?? [];
$entreprises    = $refs['entreprises']    ?? [];

// Pagination
$page  = $data['pagination']['page']       ?? 1;
$pages = $data['pagination']['totalPages'] ?? 1;
?>
<?php require __DIR__ . '/../partials/banniere.php'; ?>

<section class="py-5 bg-light" aria-labelledby="offres-heading">
  <div class="container">

    <?php
      $count = count($data['items']);
    ?>

    <!-- ===========================
       FILTRES
    ============================ -->
    <form method="GET" class="card shadow-sm mb-4" aria-label="Filtres de recherche" id="offres-public-filters-form">
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
              <?php foreach ($typesOffres as $t): ?>
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

          <!-- Entreprise -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Entreprise</label>
            <select name="entreprise_id" class="form-select">
              <option value="">Toutes</option>
              <?php foreach ($entreprises as $ent): ?>
                <option value="<?= $e($ent['id']) ?>"
                  <?= (($filters['entreprise_id'] ?? '') == $ent['id']) ? 'selected' : '' ?>>
                  <?= $e($ent['nom']) ?>
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
         LISTE DES OFFRES (AJAX)
    ============================ -->
    <div id="offres-public-results">
      <?php
        $items = $data['items'] ?? [];
        $pagination = $data['pagination'] ?? [];
        $filters = $filters ?? [];
        include __DIR__ . '/_public_results.php';
      ?>
    </div>

  </div>
</section>
