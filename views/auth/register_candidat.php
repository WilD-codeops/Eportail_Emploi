<?php

use App\Core\Security;

$old = is_array($old ?? null) ? $old : [];
$error = $error ?? null;

// Escape HTML
$e = static fn($v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

// Helper : récupérer value old
$value = static function (string $key, $default = '') use ($old, $e) {
    return $e($old[$key] ?? $default);
};

$csrfToken = Security::generateCsrfToken('register_candidat');

?>
<div class="auth-form-wrapper mt-4">

    <h1 class="auth-page-title">Créer un compte candidat</h1>
    <p class="auth-page-subtitle">
        Créez votre compte en 30 secondes. Vous pourrez compléter votre profil plus tard.
    </p>

    <!-- Toggle Candidat / Recruteur (visuel) -->
    <div class="auth-role-toggle" aria-label="Choix du type de compte">
        <a href="/register/candidat"><button type="button" class="active">Candidat</button></a>
        <a href="/register/entreprise"><button type="button">Recruteur</button></a>
    </div>

    <!-- Zone d’erreurs (ARIA) -->
    <div id="form-errors" class="alert alert-danger <?= $error ? '' : 'd-none' ?>" aria-live="assertive">
        <?= $error ? $e($error) : '' ?>
    </div>

    <form id="candidatForm" method="post" action="/register/candidat" class="auth-form mt-4" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $e($csrfToken) ?>">

        <!-- ============================================================
             STEP 1 : Compte candidat
        ============================================================= -->
        <div id="step-account">
            <h3 class="mb-3 fw-semibold">Informations du compte</h3>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <input id="prenom" type="text" name="prenom" class="form-control"
                           value="<?= $value('prenom') ?>" required autocomplete="given-name">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input id="nom" type="text" name="nom" class="form-control"
                           value="<?= $value('nom') ?>" required autocomplete="family-name">
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <input id="email" type="email" name="email" class="form-control"
                       value="<?= $value('email') ?>" required autocomplete="email">
            </div>

            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone (optionnel)</label>
                <input id="telephone" type="tel" name="telephone" class="form-control"
                       value="<?= $value('telephone') ?>" autocomplete="tel"
                       aria-describedby="telHelp">
                <div id="telHelp" class="form-text">
                    Exemple : 06XXXXXXXX ou +33XXXXXXXXX
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Mot de passe *</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password" aria-label="Afficher le mot de passe">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <!-- UI force mdp (identique au registre entreprise) -->
                    <div class="password-strength mt-2">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <small class="text-muted">8+ chars, 1 maj, 1 min, 1 chiffre, 1 spécial</small>
                        <div class="checks mt-1 small" aria-hidden="true">
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
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control" required autocomplete="new-password">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirm" aria-label="Afficher la confirmation">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="cgu" name="cgu" required>
                <label for="cgu" class="form-check-label">
                    J’accepte les Conditions Générales d’Utilisation
                </label>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="/login" class="btn btn-outline-secondary">Annuler</a>
                <button type="button" id="btnNextToProfile" class="btn btn-primary">
                    Continuer
                </button>
            </div>
        </div>

        <!-- ============================================================
             STEP 2 : Profil candidat (optionnel)
        ============================================================= -->
        <div id="step-profile" style="display:none;">
            <h3 class="mb-2 fw-semibold">Profil candidat (optionnel)</h3>
            <p class="text-muted small mb-4">
                Vous pouvez remplir ces infos maintenant ou plus tard depuis votre espace candidat.
            </p>

            <div class="mb-3">
                <label for="poste_recherche" class="form-label">Poste recherché</label>
                <input id="poste_recherche" type="text" name="poste_recherche" class="form-control"
                       value="<?= $value('poste_recherche') ?>" placeholder="Ex : Développeur web junior">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                          placeholder="Quelques mots sur vous..."><?= $value('description') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="disponibilite" class="form-label">Disponibilité</label>
                    <select id="disponibilite" name="disponibilite" class="form-select">
                        <option value="">— Choisir —</option>
                        <option value="immediate" <?= ($value('disponibilite') === 'immediate' ? 'selected' : '') ?>>Immédiate</option>
                        <option value="1_month"   <?= ($value('disponibilite') === '1_month' ? 'selected' : '') ?>>Sous 1 mois</option>
                        <option value="2_months"  <?= ($value('disponibilite') === '2_months' ? 'selected' : '') ?>>Sous 2 mois</option>
                        <option value="later"     <?= ($value('disponibilite') === 'later' ? 'selected' : '') ?>>Plus tard</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="mobilite" class="form-label">Mobilité</label>
                    <input id="mobilite" type="text" name="mobilite" class="form-control"
                           value="<?= $value('mobilite') ?>" placeholder="Ex : Paris / Remote / France">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="annee_experience" class="form-label">Années d'expérience</label>
                    <input id="annee_experience" type="number" min="0" max="60"
                           name="annee_experience" class="form-control"
                           value="<?= $value('annee_experience') ?>" placeholder="Ex : 1">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="niveau_etudes" class="form-label">Niveau d’études</label>
                    <input id="niveau_etudes" type="text" name="niveau_etudes" class="form-control"
                           value="<?= $value('niveau_etudes') ?>" placeholder="Ex : Bac+2">
                </div>
            </div>

            <div class="mb-3">
                <label for="statut_actuel" class="form-label">Statut actuel</label>
                <input id="statut_actuel" type="text" name="statut_actuel" class="form-control"
                       value="<?= $value('statut_actuel') ?>" placeholder="Ex : En recherche / En poste / Étudiant">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="btnBackToAccount" class="btn btn-outline-secondary">
                    Retour
                </button>

                <div class="d-flex gap-2">
                    <!-- Skip profil -->
                    <button type="button" id="btnSkipProfile" class="btn btn-outline-primary">
                        Passer (je remplirai plus tard)
                    </button>
                    <button type="button" id="btnNextToRecap" class="btn btn-primary">
                        Continuer
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================================
             STEP 3 : Récapitulatif
        ============================================================= -->
        <div id="step-recap" style="display:none;">
            <h3 class="mb-3 fw-semibold">Récapitulatif</h3>
            <p class="text-muted small mb-4">Vérifiez avant de créer votre compte.</p>

            <div id="recap-content" class="border rounded p-3 bg-light"></div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="btnBackToProfile" class="btn btn-outline-secondary">
                    Retour
                </button>
                <button type="submit" class="btn btn-success">
                    Confirmer et créer le compte
                </button>
            </div>
        </div>

    </form>
</div>

<!-- JS -->
<script src="/assets/js/registerCandidat.js" defer></script>
