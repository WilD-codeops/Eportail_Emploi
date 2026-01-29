<?php
// Page de création d'une entreprise (partie administrateur).
// Cette vue prépare les variables nécessaires et inclut le partial _form.php.

use App\Core\Security;

// Variables fournies par le contrôleur :
// - $secteurs : liste des secteurs disponibles
// - $errors   : tableau d'erreurs (peut être vide)

// Génération d'un token CSRF
$csrfToken  = Security::generateCsrfToken("entreprise_create");
// En création, il n'y a pas encore de données d'entreprise
$entreprise = null;

?>
<h1 class="mb-4">Créer une entreprise</h1>

<form method="post" action="/admin/entreprises/create">
    <?php
    // Les variables passées au partial
    $csrf = $csrfToken;
    $withGestionnaire = true; // en création admin : entreprise + gestionnaire
    require __DIR__ . '/_form.php';
    ?>
</form>
