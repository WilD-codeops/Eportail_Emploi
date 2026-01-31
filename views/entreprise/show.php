<?php
// Page de détail d'une entreprise (espace public).
// Cette vue présente les informations d’une entreprise ainsi que ses offres
// d’emploi dans une mise en page inspirée de la maquette Figma. Le layout
// utilisé est le layout principal (main). Les variables fournies par le
// contrôleur sont :
// - $entreprise : tableau associatif contenant les données de l’entreprise
// - $offres     : tableau de tableaux représentant les offres d’emploi de cette entreprise



// Fonction utilitaire pour échapper les valeurs affichées
$e = static fn ($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

// Déterminer le titre du bandeau et le slogan
$titre   = $entreprise['nom'] ?? 'Entreprise';
$slogan  = $entreprise['slogan'] ?? '';

?>

<main role="main" aria-label="Profil entreprise">

  <!-- Bannière -->
  <section class="py-5 text-center bg-primary-gradient text-white">
    <div class="container">
      <h1 class="hero-title mb-2"><?= $e($titre) ?></h1>

      <?php if (!empty($slogan)): ?>
        <p class="hero-subtitle mb-0"><?= $e($slogan) ?></p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Contenu principal -->
  <section class="container py-5">
    <div class="row gy-5">

      <!-- Colonne gauche -->
      <aside class="col-12 col-lg-4">
        <div class="text-center mb-4">
          <img src="<?= $e($entreprise['logo'] ?? '/assets/img/company_logo_generique.png') ?>"
               alt="Logo de l’entreprise"
               class="img-fluid rounded-circle shadow-sm entreprise-logo">
        </div>

        <h2 class="h5 text-secondary mb-3">Informations clés</h2>

        <ul class="list-unstyled snippet mb-4">
          <?php if (!empty($entreprise['secteur'])): ?>
            <li><strong>Secteur :</strong> <?= $e($entreprise['secteur']) ?></li>
          <?php endif; ?>

          <?php if (!empty($entreprise['taille'])): ?>
            <li><strong>Taille :</strong> <?= $e($entreprise['taille']) ?> salariés</li>
          <?php endif; ?>

          <?php if (!empty($entreprise['ville']) || !empty($entreprise['pays'])): ?>
            <li><strong>Localisation :</strong>
              <?= $e($entreprise['ville'] ?? '') ?>
              <?= (!empty($entreprise['ville']) && !empty($entreprise['pays'])) ? ' - ' : '' ?>
              <?= $e($entreprise['pays'] ?? '') ?>
            </li>
          <?php endif; ?>

          <?php if (!empty($entreprise['site_web'])): ?>
            <li><strong>Site web :</strong>
              <a href="<?= $e($entreprise['site_web']) ?>" target="_blank" rel="noopener noreferrer">
                <?= $e($entreprise['site_web']) ?>
              </a>
            </li>
          <?php endif; ?>

          <?php if (!empty($entreprise['email'])): ?>
            <li><strong>Email :</strong>
              <a href="mailto:<?= $e($entreprise['email']) ?>"><?= $e($entreprise['email']) ?></a>
            </li>
          <?php endif; ?>

          <?php if (!empty($entreprise['telephone'])): ?>
            <li><strong>Téléphone :</strong> <?= $e($entreprise['telephone']) ?></li>
          <?php endif; ?>
        </ul>

        <?php if (!empty($offres)): ?>
          <a href="#offers" class="btn btn-primary w-100">Voir les offres (<?= count($offres) ?>)</a>
        <?php endif; ?>
      </aside>

      <!-- Colonne droite -->
      <div class="col-12 col-lg-8">

        <!-- À propos -->
        <section class="mb-5">
          <h2 class="h4 text-secondary mb-3">À propos</h2>
          <p class="snippet"><?= nl2br($e($entreprise['description'] ?? 'Pas de description disponible.')) ?></p>
        </section>

        <!-- Offres -->
        <section id="offers">
          <h2 class="h4 text-secondary mb-3">Offres d’emploi</h2>

          <?php if (empty($offres)): ?>
            <p class="text-muted">Aucune offre pour le moment.</p>

          <?php else: ?>
            <div class="row g-4">

              <?php foreach ($offres as $offre): ?>
                <div class="col-12 col-md-6">
                  <article class="offer-card-modern h-100 d-flex flex-column">

                    <header>
                      <h3 class="offer-title mb-2"><?= $e($offre['titre']) ?></h3>

                      <p class="snippet mb-2 offer-snippet">
                        <?= $e(mb_strimwidth($offre['description'] ?? '', 0, 120, '…')) ?>
                      </p>

                      <div class="d-flex flex-wrap gap-2 mb-3">

                        <?php if (!empty($offre['offre_code'])): ?>
                          <span class="badge-contract"><?= $e($offre['offre_code']) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($offre['niveau_qualification'])): ?>
                          <span class="badge-contract"><?= $e($offre['niveau_qualification']) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($offre['localisation'])): ?>
                          <span class="badge-contract"><?= $e($offre['localisation']) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($offre['salaire'])): ?>
                          <span class="badge-contract"><?= $e($offre['salaire']) ?> €</span>
                        <?php endif; ?>

                      </div>
                    </header>

                    <footer class="mt-auto">
                      <a href="/offres/<?= $offre['id'] ?>" class="btn-offer">Voir l’offre</a>
                    </footer>

                  </article>
                </div>
              <?php endforeach; ?>

            </div>
          <?php endif; ?>
        </section>

      </div>
    </div>
  </section>
</main>