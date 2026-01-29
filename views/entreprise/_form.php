<?php
// Ce partial affiche le formulaire de création/modification d'une entreprise.
// Il est réutilisable pour la création et l'édition afin d'éviter la duplication de code.
// Les variables attendues sont :
// - $entreprise : tableau de données de l'entreprise (null en création)
// - $errors     : tableau associatif des messages d'erreur par champ
// - $secteurs   : liste des secteurs disponibles pour la sélection
// - $csrf       : jeton CSRF à inclure dans le formulaire

// On s'assure que les variables existent et sont du bon type
$entreprise = is_array($entreprise ?? null) ? $entreprise : [];
$errors     = is_array($errors ?? null)     ? $errors     : [];
$secteurs   = is_array($secteurs ?? null)   ? $secteurs   : [];
$csrf       = $csrf ?? '';

// Afficher ou non le bloc Gestionnaire (true en create admin, false en edit entreprise)
$withGestionnaire = $withGestionnaire ?? false;

// Fonction utilitaire pour échapper les valeurs affichées
$e = static fn($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

// Récupérer la valeur d'un champ : saisie en POST prioritaire, puis valeur existante, sinon vide
$value = static function (string $key, $default = '') use ($entreprise) {
    return $entreprise[$key] ?? $default;
};

// Récupérer l'erreur associée à un champ
$fieldError = static function (string $key) use ($errors): ?string {
    return !empty($errors[$key]) ? (string)$errors[$key] : null;
};
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        Merci de corriger les erreurs ci-dessous.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">

        <!-- Informations sur l'entreprise -->
        <h3 class="mb-3 fw-semibold">Informations entreprise</h3>

        <div class="mb-3">
            <label for="nom" class="form-label">Nom de l’entreprise *</label>
            <input
                type="text"
                class="form-control <?= $fieldError('nom') ? 'is-invalid' : '' ?>"
                id="nom"
                name="nom"
                value="<?= $e($value('nom')) ?>"
                maxlength="150"
                required
            >
            <?php if ($msg = $fieldError('nom')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="secteur_id" class="form-label">Secteur d’activité *</label>
            <select
                class="form-select <?= $fieldError('secteur_id') ? 'is-invalid' : '' ?>"
                id="secteur_id"
                name="secteur_id"
                required
            >
                <option value="">— Choisir un secteur —</option>
                <?php foreach ($secteurs as $s): ?>
                    <?php $selected = ((int)$value('secteur_id') === (int)$s['id']) ? 'selected' : ''; ?>
                    <option value="<?= $e($s['id']) ?>" <?= $selected ?>>
                        <?= $e($s['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($msg = $fieldError('secteur_id')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse *</label>
            <input
                type="text"
                class="form-control <?= $fieldError('adresse') ? 'is-invalid' : '' ?>"
                id="adresse"
                name="adresse"
                value="<?= $e($value('adresse')) ?>"
                required
            >
            <?php if ($msg = $fieldError('adresse')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="code_postal" class="form-label">Code postal *</label>
                <input
                    type="text"
                    class="form-control <?= $fieldError('code_postal') ? 'is-invalid' : '' ?>"
                    id="code_postal"
                    name="code_postal"
                    value="<?= $e($value('code_postal')) ?>"
                    maxlength="5"
                    required
                >
                <?php if ($msg = $fieldError('code_postal')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-4 mb-3">
                <label for="ville" class="form-label">Ville *</label>
                <input
                    type="text"
                    class="form-control <?= $fieldError('ville') ? 'is-invalid' : '' ?>"
                    id="ville"
                    name="ville"
                    value="<?= $e($value('ville')) ?>"
                    required
                >
                <?php if ($msg = $fieldError('ville')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-4 mb-3">
                <label for="pays" class="form-label">Pays *</label>
                <input
                    type="text"
                    class="form-control <?= $fieldError('pays') ? 'is-invalid' : '' ?>"
                    id="pays"
                    name="pays"
                    value="<?= $e($value('pays')) ?>"
                    required
                >
                <?php if ($msg = $fieldError('pays')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone professionnel</label>
            <input
                type="text"
                class="form-control <?= $fieldError('telephone') ? 'is-invalid' : '' ?>"
                id="telephone"
                name="telephone"
                value="<?= $e($value('telephone')) ?>"
            >
            <?php if ($msg = $fieldError('telephone')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email entreprise</label>
            <input
                type="email"
                class="form-control <?= $fieldError('email') ? 'is-invalid' : '' ?>"
                id="email"
                name="email"
                value="<?= $e($value('email')) ?>"
            >
            <?php if ($msg = $fieldError('email')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="siret" class="form-label">SIRET *</label>
            <input
                type="text"
                class="form-control <?= $fieldError('siret') ? 'is-invalid' : '' ?>"
                id="siret"
                name="siret"
                value="<?= $e($value('siret')) ?>"
                maxlength="14"
                required
            >
            <?php if ($msg = $fieldError('siret')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="site_web" class="form-label">Site web</label>
            <input
                type="url"
                class="form-control <?= $fieldError('site_web') ? 'is-invalid' : '' ?>"
                id="site_web"
                name="site_web"
                value="<?= $e($value('site_web')) ?>"
                placeholder="https://monsite.fr"
            >
            <?php if ($msg = $fieldError('site_web')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="taille" class="form-label">Taille de l’entreprise</label>
            <select
                class="form-select <?= $fieldError('taille') ? 'is-invalid' : '' ?>"
                id="taille"
                name="taille"
            >
            <option value="">— Choisir —</option>
            <?php
                // Options  : adapter si changement plus tard.
                $tailleOptions = ['1-10', '11-50', '51-250', '250+'];
                foreach ($tailleOptions as $opt):
                    $selected = ($value('taille') === $opt) ? 'selected' : '';
                ?>
                    <option value="<?= $e($opt) ?>" <?= $selected ?>><?= $e($opt) ?> salariés</option>
            <?php endforeach; ?>
             </select>

           <?php if ($msg = $fieldError('taille')): ?>
               <div class="invalid-feedback"><?= $e($msg) ?></div>
           <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description de l’entreprise</label>
            <textarea
                class="form-control <?= $fieldError('description') ? 'is-invalid' : '' ?>"
                id="description"
                name="description"
                rows="4"
            ><?= $e($value('description')) ?></textarea>
            <?php if ($msg = $fieldError('description')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

       <?php if ($withGestionnaire): ?>

        
    <!-- Bloc gestionnaire (affiché seulement en création entreprise + gestionnaire) -->
    <h3 class="mt-4 mb-3 fw-semibold">Gestionnaire du compte</h3>

    <div class="mb-3">
        <label for="prenom" class="form-label">Prénom *</label>
        <input
            type="text"
            class="form-control <?= $fieldError('prenom') ? 'is-invalid' : '' ?>"
            id="prenom"
            name="prenom"
            value="<?= $e($value('prenom')) ?>"
            required
        >
        <?php if ($msg = $fieldError('prenom')): ?>
            <div class="invalid-feedback"><?= $e($msg) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="nom_gestionnaire" class="form-label">Nom *</label>
        <input
            type="text"
            class="form-control <?= $fieldError('nom_gestionnaire') ? 'is-invalid' : '' ?>"
            id="nom_gestionnaire"
            name="nom_gestionnaire"
            value="<?= $e($value('nom_gestionnaire')) ?>"
            required
        >
        <?php if ($msg = $fieldError('nom_gestionnaire')): ?>
            <div class="invalid-feedback"><?= $e($msg) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="email_gestionnaire" class="form-label">Email professionnel *</label>
        <input
            type="email"
            class="form-control <?= $fieldError('email_gestionnaire') ? 'is-invalid' : '' ?>"
            id="email_gestionnaire"
            name="email_gestionnaire"
            value="<?= $e($value('email_gestionnaire')) ?>"
            autocomplete="email"
            required
        >
        <?php if ($msg = $fieldError('email_gestionnaire')): ?>
            <div class="invalid-feedback"><?= $e($msg) ?></div>
        <?php endif; ?>
    </div>

    <!-- ✅ NOUVEAU : téléphone gestionnaire (optionnel) -->
    <div class="mb-3">
        <label for="telephone_gestionnaire" class="form-label">Téléphone</label>
        <input
            type="text"
            class="form-control <?= $fieldError('telephone_gestionnaire') ? 'is-invalid' : '' ?>"
            id="telephone_gestionnaire"
            name="telephone_gestionnaire"
            value="<?= $e($value('telephone_gestionnaire')) ?>"
        >
        <?php if ($msg = $fieldError('telephone_gestionnaire')): ?>
            <div class="invalid-feedback"><?= $e($msg) ?></div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Mot de passe *</label>
            <input
                type="password"
                class="form-control <?= $fieldError('password') ? 'is-invalid' : '' ?>"
                id="password"
                name="password"
                value=""
                autocomplete="new-password"
                required
            >
            <?php if ($msg = $fieldError('password')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <!-- ✅ NOUVEAU : confirmation du mot de passe -->
        <div class="col-md-6 mb-3">
            <label for="password_confirm" class="form-label">Confirmation *</label>
            <input
                type="password"
                class="form-control <?= $fieldError('password_confirm') ? 'is-invalid' : '' ?>"
                id="password_confirm"
                name="password_confirm"
                value=""
                autocomplete="new-password"
                required
            >
            <?php if ($msg = $fieldError('password_confirm')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Boutons de validation / annulation -->
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/entreprises" class="btn btn-secondary">Annuler</a>
        </div>
</div>
