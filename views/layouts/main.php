<?php
/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'EPortailEmploi', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >

    <!-- Mon CSS-->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/alerts.php'; ?> <!-- GESTION DES ALERTES SWEETALERT -->

<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="flex-shrink-0">
<?php
    if (isset($content))
         {echo $content;
        }
?> 
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>

<!-- JS custom -->
<script src="/assets/js/app.js"></script>
</body>
</html>