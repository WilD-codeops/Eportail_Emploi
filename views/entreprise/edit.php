<?php
// views/entreprise/edit.php
// Page de modification d'une entreprise.
// Cette vue prépare les variables nécessaires et inclut le partial _form.php.

use App\Core\Security;

// Variables fournies par le contrôleur :
// - $entreprise : données de l'entreprise à modifier (array)
// - $secteurs   : liste des secteurs disponibles
// - $error      : message d'erreur éventuel

if (empty($entreprise) || empty($entreprise['id'])) {
    // Sécurité : on évite un formulaire cassé si l'entreprise n'existe pas
    echo "<div class='alert alert-danger'>Entreprise introuvable.</div>";
    return;
}

// Token CSRF spécifique à l'action "entreprise_edit"
$csrfToken = Security::generateCsrfToken('entreprise_edit');
?>

<h2 class=" text-primary mb-4">Modifier une entreprise</h2>

<form method="post" action="/admin/entreprises/edit">
    <?php
    // 1) On passe le token au partial
    $csrf = $csrfToken;

    // 2) IMPORTANT : l'id doit être envoyé en POST (car le controller lit $_POST['id'])
    //    => évite les bugs si l'URL change ou si on nettoie les routes plus tard.
    ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$entreprise['id']) ?>">

    <?php
    $withGestionnaire = false; // en édition : on modifie seulement l'entreprise

    // 3) On inclut le formulaire réutilisable (create/edit)
    require __DIR__ . '/_form.php';
    ?>
</form>
