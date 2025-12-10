<h1>Créer une entreprise</h1>

<?php use App\Core\Security;
$csrfToken = Security::generateCsrfToken('entreprise_create');
 ?>

<form method="post" action="/admin/entreprises/create" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <h2>Informations entreprise</h2>

    <label>Nom</label>
    <input name="nom" required>

    <label>Secteur</label>
    <select name="secteur_id" required>
        <?php foreach ($secteurs as $s): ?>
            <option value="<?= $s['id'] ?>"><?= $s['libelle'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Adresse</label>
    <input name="adresse" required>

    <label>Code postal</label>
    <input name="code_postal">

    <label>Ville</label>
    <input name="ville">

    <label>Pays</label>
    <input name="pays">

    <label>Téléphone</label>
    <input name="telephone">

    <label>Email entreprise</label>
    <input name="email">

    <label>SIRET</label>
    <input name="siret" required>

    <label>Site web</label>
    <input name="site_web">

    <h2>Gestionnaire principal</h2>

    <label>Prénom</label>
    <input name="prenom" required>

    <label>Nom</label>
    <input name="nom_gestionnaire" required>

    <label>Email professionnel</label>
    <input name="email_gestionnaire" type="email" required>

    <label>Mot de passe</label>
    <input name="mot_de_passe" type="password" required>

    <button class="btn btn-primary">Créer l’entreprise</button>
</form>
