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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- CSS custom -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="dashboard-body">
    <?php include __DIR__ . '/../partials/alerts.php'; ?> <!-- GESTION DES ALERTES SWEETALERT -->

<div class="d-flex min-vh-100">
    <?php require __DIR__ . '/../partials/sidebar_dashboard.php'; ?>

    <div class="flex-grow-1 d-flex flex-column bg-light">
        <!-- Top bar -->
        <header class="border-bottom bg-white px-4 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h5 mb-0"><?= htmlspecialchars($title ?? 'Tableau de bord') ?></h1>
                <div class="text-muted small">Administration EPortailEmploi</div>
            </div>
            <!-- mettre ici recherche, notifications, etc. -->
        </header>

        <!-- Contenu principal -->
        <main class="p-4">
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>