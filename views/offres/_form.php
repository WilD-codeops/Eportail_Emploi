<?php
// Défaut "safe" : le partial ne doit jamais dépendre d'une variable obligatoire.
$input   = is_array($input ?? null) ? $input : [];
$offre   = is_array($offre ?? null) ? $offre : [];
$errors  = is_array($errors ?? null) ? $errors : [];
$refs    = is_array($refs ?? null) ? $refs : [];
$isAdmin = (bool)($isAdmin ?? false);
$csrf    = (string)($csrf ?? '');

// Escape HTML
$e = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');

// Récup valeur : priorité à l'input (après erreur), sinon l'offre (edit), sinon défaut
$value = static function (string $key, $default = '') use ($input, $offre) {
    if (array_key_exists($key, $input)) return $input[$key];
    if (array_key_exists($key, $offre)) return $offre[$key];
    return $default;
};

// Affichage erreur champ
$fieldError = static function (string $key) use ($errors): ?string {
    return !empty($errors[$key]) ? (string)$errors[$key] : null;
};

// Options statut
$statusOptions = [
    'active'   => "Active",
    'inactive' => "Inactive",
];
if ($isAdmin) {
    $statusOptions['archive'] = "Archivée";
}

$cancelUrl = $isAdmin ? '/admin/offres' : '/dashboard/offres';

// Valeurs (normalisées en string pour les inputs)
$currentStatut = (string)$value('statut', 'active');
?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        Merci de corriger les erreurs ci-dessous.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">

        <div class="mb-3">
            <label class="form-label" for="titre">Titre</label>
            <input
                type="text"
                class="form-control <?= $fieldError('titre') ? 'is-invalid' : '' ?>"
                id="titre"
                name="titre"
                value="<?= $e($value('titre')) ?>"
                maxlength="150"
                required
            >
            <?php if ($msg = $fieldError('titre')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea
                class="form-control <?= $fieldError('description') ? 'is-invalid' : '' ?>"
                id="description"
                name="description"
                rows="5"
            ><?= $e($value('description')) ?></textarea>
            <?php if ($msg = $fieldError('description')): ?>
                <div class="invalid-feedback"><?= $e($msg) ?></div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="date_debut">Date début</label>
                <input
                    type="date"
                    class="form-control <?= $fieldError('date_debut') ? 'is-invalid' : '' ?>"
                    id="date_debut"
                    name="date_debut"
                    value="<?= $e($value('date_debut')) ?>"
                >
                <?php if ($msg = $fieldError('date_debut')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label" for="date_fin">Date fin</label>
                <input
                    type="date"
                    class="form-control <?= $fieldError('date_fin') ? 'is-invalid' : '' ?>"
                    id="date_fin"
                    name="date_fin"
                    value="<?= $e($value('date_fin')) ?>"
                >
                <?php if ($msg = $fieldError('date_fin')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label" for="duree_contrat">Durée du contrat (mois)</label>
                <input
                    type="number"
                    class="form-control <?= $fieldError('duree_contrat') ? 'is-invalid' : '' ?>"
                    id="duree_contrat"
                    name="duree_contrat"
                    min="1"
                    value="<?= $e($value('duree_contrat')) ?>"
                >
                <?php if ($msg = $fieldError('duree_contrat')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label" for="salaire">Salaire</label>
                <input
                    type="number"
                    step="0.01"
                    class="form-control <?= $fieldError('salaire') ? 'is-invalid' : '' ?>"
                    id="salaire"
                    name="salaire"
                    value="<?= $e($value('salaire')) ?>"
                >
                <?php if ($msg = $fieldError('salaire')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label" for="statut">Statut</label>
                <select
                    class="form-select <?= $fieldError('statut') ? 'is-invalid' : '' ?>"
                    id="statut"
                    name="statut"
                >
                    <?php foreach ($statusOptions as $key => $label): ?>
                        <option value="<?= $e($key) ?>" <?= ($currentStatut === $key) ? 'selected' : '' ?>>
                            <?= $e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($msg = $fieldError('statut')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="type_offre_id">Type d'offre</label>
                <select
                    class="form-select <?= $fieldError('type_offre_id') ? 'is-invalid' : '' ?>"
                    id="type_offre_id"
                    name="type_offre_id"
                >
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (($refs['typesOffres'] ?? []) as $t): ?>
                        <?php
                        $tid = (int)($t['id'] ?? 0);
                        $selected = ((int)$value('type_offre_id') === $tid) ? 'selected' : '';
                        ?>
                        <option value="<?= $e($tid) ?>" <?= $selected ?>>
                            <?= $e($t['code'] ?? '') ?> - <?= $e($t['description'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($msg = $fieldError('type_offre_id')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label" for="niveau_qualification_id">Niveau de qualification</label>
                <select
                    class="form-select <?= $fieldError('niveau_qualification_id') ? 'is-invalid' : '' ?>"
                    id="niveau_qualification_id"
                    name="niveau_qualification_id"
                >
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (($refs['niveauxQualification'] ?? []) as $n): ?>
                        <?php
                        $nid = (int)($n['id'] ?? 0);
                        $selected = ((int)$value('niveau_qualification_id') === $nid) ? 'selected' : '';
                        ?>
                        <option value="<?= $e($nid) ?>" <?= $selected ?>>
                            <?= $e($n['libelle'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($msg = $fieldError('niveau_qualification_id')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="domaine_emploi_id">Domaine d'emploi</label>
                <select
                    class="form-select <?= $fieldError('domaine_emploi_id') ? 'is-invalid' : '' ?>"
                    id="domaine_emploi_id"
                    name="domaine_emploi_id"
                >
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (($refs['domainesEmploi'] ?? []) as $d): ?>
                        <?php
                        $did = (int)($d['id'] ?? 0);
                        $selected = ((int)$value('domaine_emploi_id') === $did) ? 'selected' : '';
                        ?>
                        <option value="<?= $e($did) ?>" <?= $selected ?>>
                            <?= $e($d['nom'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($msg = $fieldError('domaine_emploi_id')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label" for="localisation_id">Localisation</label>
                <select
                    class="form-select <?= $fieldError('localisation_id') ? 'is-invalid' : '' ?>"
                    id="localisation_id"
                    name="localisation_id"
                >
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (($refs['localisations'] ?? []) as $l): ?>
                        <?php
                        $lid = (int)($l['id'] ?? 0);
                        $selected = ((int)$value('localisation_id') === $lid) ? 'selected' : '';
                        $label = trim(($l['ville'] ?? '') . (!empty($l['pays']) ? ' - ' . $l['pays'] : ''));
                        ?>
                        <option value="<?= $e($lid) ?>" <?= $selected ?>>
                            <?= $e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($msg = $fieldError('localisation_id')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <div class="mb-3">
                <label class="form-label" for="entreprise_id">Entreprise</label>
                <select
                    class="form-select <?= $fieldError('entreprise_id') ? 'is-invalid' : '' ?>"
                    id="entreprise_id"
                    name="entreprise_id"
                >
                    <option value="">-- Sélectionner --</option>
                    <?php foreach (($refs['entreprises'] ?? []) as $eRow): ?>
                        <?php
                        $eid = (int)($eRow['id'] ?? 0);
                        $selected = ((int)$value('entreprise_id') === $eid) ? 'selected' : '';
                        ?>
                        <option value="<?= $e($eid) ?>" <?= $selected ?>>
                            <?= $e($eRow['nom'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($msg = $fieldError('entreprise_id')): ?>
                    <div class="invalid-feedback"><?= $e($msg) ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= $e($csrf) ?>">

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="<?= $e($cancelUrl) ?>" class="btn btn-secondary">Annuler</a>
        </div>

    </div>
</div>
