<?php
$isAdmin = $isAdmin ?? (\App\Core\Auth::role() === 'admin');
$refs    = $refs ?? [];
$errors  = $errors ?? [];
$input   = $input ?? [];
$csrf    = $csrf ?? '';
$action  = $isAdmin ? "/admin/offres/create" : "/dashboard/offres/create";
?>

<h2 class="h4 mb-3 text-primary"><?= htmlspecialchars($title ?? "Créer une offre") ?></h2>

<form method="POST" action="<?= htmlspecialchars($action) ?>">
    <?php require __DIR__ . "/_form.php"; ?>
</form>
