<?php
$isAdmin = $isAdmin ?? (\App\Core\Auth::role() === 'admin');
$refs    = $refs ?? [];
$errors  = $errors ?? [];
$input   = $input ?? [];
$csrf    = $csrf ?? '';
$offre   = $offre ?? [];

$action  = $isAdmin
    ? "/admin/offres/edit?id=" . urlencode((string)($offre['id'] ?? ''))
    : "/dashboard/offres/edit?id=" . urlencode((string)($offre['id'] ?? ''));
?>

<h2 class="h4 mb-3 text-primary"><?= htmlspecialchars($title ?? "Modifier une offre") ?></h2>

<form method="POST" action="<?= htmlspecialchars($action) ?>">
    <?php require __DIR__ . "/_form.php"; ?>
</form>
