<?php
/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Dashboard – EPortailEmploi', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (tu les utilises déjà : bi bi-...) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS custom -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>

<body class="dash">
    <?php include __DIR__ . '/../partials/alerts.php'; ?>

    <div class="dash-shell">

        <!-- Sidebar desktop -->
            <div class="d-none d-lg-block">
              <?php require __DIR__ . '/../partials/sidebar_dashboard.php'; ?>
            </div>
            
            <!-- Sidebar mobile/tablette -->
            
            <div class="offcanvas offcanvas-start dash-offcanvas"
                 tabindex="-1"
                 id="sidebarOffcanvas"
                 aria-labelledby="sidebarOffcanvasLabel">

              <!-- Bouton close overlay (pas de header => pas de doublon) -->
              <button type="button"
                      class="btn-close btn-close-white dash-offcanvas-close"
                      data-bs-dismiss="offcanvas"
                      aria-label="Close"></button>

              <div class="offcanvas-body p-0 dash-offcanvas-body">
                <?php require __DIR__ . '/../partials/sidebar_dashboard.php'; ?>
              </div>
            </div>




        <!-- Main -->
        <div class="dash-main">
            <header class="border-bottom bg-white px-3 px-md-4 py-3">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">

                  <div class="d-flex align-items-center gap-2">
                    <!-- bouton burger visible sur mobile -->
                    <button class="btn dash-burger d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#sidebarOffcanvas"
                        aria-controls="sidebarOffcanvas">
                        <i class="bi bi-list"></i>
                    </button>

                    <a href="/" class="btn btn-light btn-sm d-flex align-items-center gap-2 px-3 shadow-sm" style="border-radius: 999px;" aria-label="Retour à l'accueil">
                      <span class="d-inline-flex align-items-center justify-content-center bg-white border" style="width:28px;height:28px;border-radius:50%;">
                        <i class="bi bi-house text-primary"></i>
                      </span>
                      <span class="d-none d-md-inline fw-semibold text-primary">Accueil</span>
                    </a>


                    <div>
                      <h1 class="h5 mb-0"><?= htmlspecialchars($rubrique ?? 'Tableau de bord') ?></h1>
                      <div class="text-muted small">Administration EPortailEmploi</div>
                    </div>
                  </div>

                  <!-- bloc user : ne shrink jamais -->
                  <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
                    <div class="text-end d-none d-sm-block">
                      <div class="fw-semibold "><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Compte') ?></div>
                      <div class="text-muted small"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></div>
                    </div>
                    <div class="rounded-circle bg-secondary" style="width:38px;height:38px;">
                      <i class="bi bi-person-fill text-white fs-4 d-block text-center pt-"></i>
                    </div>
                  </div>

                </div>
            </header>


            <main class="dash-content">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/dashboard.js"></script>
</body>
</html>
