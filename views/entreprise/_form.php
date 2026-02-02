<?php
use App\Core\Auth;
// Partial formulaire entreprise (create/edit).

// Les variables attendues sont :
// - $entreprise : tableau de données de l'entreprise (null en création)
// - $secteurs   : liste des secteurs disponibles pour la sélection
// - $csrf       : jeton CSRF à inclure dans le formulaire

// On s'assure que les variables existent et sont du bon type


$entreprise = is_array($entreprise ?? null) ? $entreprise : [];
$secteurs   = is_array($secteurs ?? null)   ? $secteurs   : [];
$csrf       = $csrf ?? '';

// Afficher ou non le bloc Gestionnaire (true en create admin, false en edit entreprise)
$withGestionnaire = $withGestionnaire ?? false;

// Fonction utilitaire pour échapper les valeurs affichées
$e = static fn($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

// Récupérer la valeur d'un champ (valeurs existantes)
$value = static function (string $key, $default = '') use ($entreprise) {
    return $entreprise[$key] ?? $default;
};
?>

<div class="card">
    <div class="card-body">
        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">

        <!-- Informations sur l'entreprise -->
        <h3 class="mb-3 fw-semibold">Informations entreprise</h3>

        <div class="mb-3">
            <label for="nom" class="form-label">Nom de l’entreprise *</label>
            <input
                type="text"
                class="form-control"
                id="nom"
                name="nom"
                value="<?= $e($value('nom', '')) ?>"
                maxlength="150"
                required
            >
        </div>

        <div class="mb-3">
            <label for="secteur_id" class="form-label">Secteur d’activité *</label>
            <select
                class="form-select"
                id="secteur_id"
                name="secteur_id"
                required
            >
                <option value="">— Choisir un secteur —</option>
                <?php foreach ($secteurs as $s): ?>
                    <?php $selected = ((int)$value('secteur_id', '') === (int)$s['id']) ? 'selected' : ''; ?>
                    <option value="<?= $e($s['id']) ?>" <?= $selected ?>>
                        <?= $e($s['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse *</label>
            <input
                type="text"
                class="form-control"
                id="adresse"
                name="adresse"
                value="<?= $e($value('adresse', '')) ?>"
                required
            >
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="code_postal" class="form-label">Code postal *</label>
                <input
                    type="text"
                    class="form-control"
                    id="code_postal"
                    name="code_postal"
                    value="<?= $e($value('code_postal', '')) ?>"
                    maxlength="5"
                    required
                >
            </div>
            <div class="col-md-4 mb-3">
                <label for="ville" class="form-label">Ville *</label>
                <input
                    type="text"
                    class="form-control"
                    id="ville"
                    name="ville"
                    value="<?= $e($value('ville', '')) ?>"
                    required
                >
            </div>
            <div class="col-md-4 mb-3">
                <label for="pays" class="form-label">Pays *</label>
                <input
                    type="text"
                    class="form-control"
                    id="pays"
                    name="pays"
                    value="<?= $e($value('pays', '')) ?>"
                    required
                >
            </div>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone professionnel</label>
            <input
                type="text"
                class="form-control"
                id="telephone"
                name="telephone"
                value="<?= $e($value('telephone', '')) ?>"
            >
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email entreprise</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="<?= $e($value('email', '')) ?>"
            >
        </div>

        <div class="mb-3">
            <label for="siret" class="form-label">SIRET *</label>
            <input
                type="text"
                class="form-control"
                id="siret"
                name="siret"
                value="<?= $e($value('siret', '')) ?>"
                maxlength="14"
                required
            >
        </div>

        <div class="mb-3">
            <label for="site_web" class="form-label">Site web</label>
            <input
                type="url"
                class="form-control"
                id="site_web"
                name="site_web"
                value="<?= $e($value('site_web', '')) ?>"
                placeholder="https://monsite.fr"
            >
        </div>

        <div class="mb-3">
            <label for="taille" class="form-label">Taille de l’entreprise</label>
            <select
                class="form-select"
                id="taille"
                name="taille"
            >
                <option value="">— Choisir —</option>
                <?php
                $tailleOptions = ['1-10', '11-50', '51-250', '250+'];
                foreach ($tailleOptions as $opt):
                    $selected = ($value('taille', '') === $opt) ? 'selected' : '';
                ?>
                    <option value="<?= $e($opt) ?>" <?= $selected ?>><?= $e($opt) ?> salariés</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description de l’entreprise</label>
            <textarea
                class="form-control"
                id="description"
                name="description"
                rows="4"
            ><?= $e($value('description', '')) ?></textarea>
        </div>

        <?php if ($withGestionnaire): ?>

            <!-- Bloc gestionnaire (affiché seulement en création entreprise + gestionnaire) -->
            <h3 class="mt-4 mb-3 fw-semibold">Gestionnaire du compte</h3>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom *</label>
                <input
                    type="text"
                    class="form-control"
                    id="prenom"
                    name="prenom"
                    value="<?= $e($value('prenom', '')) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="nom_gestionnaire" class="form-label">Nom *</label>
                <input
                    type="text"
                    class="form-control"
                    id="nom_gestionnaire"
                    name="nom_gestionnaire"
                    value="<?= $e($value('nom_gestionnaire', '')) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="email_gestionnaire" class="form-label">Email professionnel *</label>
                <input
                    type="email"
                    class="form-control"
                    id="email_gestionnaire"
                    name="email_gestionnaire"
                    value="<?= $e($value('email_gestionnaire', '')) ?>"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="telephone_gestionnaire" class="form-label">Téléphone</label>
                <input
                    type="text"
                    class="form-control"
                    id="telephone_gestionnaire"
                    name="telephone_gestionnaire"
                    value="<?= $e($value('telephone_gestionnaire', '')) ?>"
                >
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Mot de passe *</label>
                    <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        value=""
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                        <i class="bi bi-eye"></i>
                    </div>
                    <div class="password-strength mt-2">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <small class="text-muted">8+ chars, 1 maj, 1 min, 1 chiffre, 1 spécial</small>
                        <div class="checks mt-1 small">
                            <div class="check-item"><i class="bi bi-x"></i> 8+ caractères</div>
                            <div class="check-item"><i class="bi bi-x"></i> Majuscule</div>
                            <div class="check-item"><i class="bi bi-x"></i> Chiffre</div>
                            <div class="check-item"><i class="bi bi-x"></i> Spécial</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirm" class="form-label">Confirmation *</label>
                    <div class="input-group">
                    <input
                        type="password"
                        class="form-control"
                        id="password_confirm"
                        name="password_confirm"
                        value=""
                        autocomplete="new-password"
                        required
                    >
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirm">
                        <i class="bi bi-eye"></i>
                    </button>    
                </div>
            </div>

        <?php endif; ?>

        <!-- Boutons de validation / annulation -->
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href=<?= Auth::role()=='admin' ? "/admin/entreprises" : "/dashboard/equipe" ?> class="btn btn-secondary">Annuler</a>
        </div>
    </div>
</div>
