<?php
$entreprise = is_array($entreprise ?? null) ? $entreprise : [];
$secteurs   = is_array($secteurs ?? null)   ? $secteurs   : [];

// Fonction d’échappement HTML
$echappe = static fn($val) : string => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

//Récupérer la valeur d'un champ (valeurs existantes) depuis $entreprise
$value = static function (string $key, $default = '') use ($entreprise) {
    return htmlspecialchars((string)($entreprise[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};

?>
<div class="auth-form-wrapper mt-4">

    <h1 class="auth-page-title">Créer un espace entreprise</h1>
    <p class="auth-page-subtitle">
        Rejoignez EPortailEmploi et centralisez votre gestion de recrutement.
    </p>

    <!-- Toggle Candidat / Recruteur (visuel pour l'instant) -->
    <div class="auth-role-toggle" aria-label="Choix du type de compte">
        <a href="/register/candidat"><button type="button">Candidat</button></a>
        <a href="/register/entreprise"><button type="button" class="active">Recruteur</button></a>
    </div>

    <!-- Zone d’erreurs (accessibilité ARIA) -->
    <div id="form-errors" class="alert alert-danger d-none" aria-live="assertive"></div>

    <!--Csrf_Token-->
    <?php use App\Core\Security;
    $csrfToken = Security::generateCsrfToken('register_entreprise');
    ?>


    <!-- Formulaire d'inscription entreprise -->
    <form id="entrepriseForm" method="post" action="/register/entreprise" class="auth-form mt-4">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrfToken) //csrf ?>">

        <!-- ÉTAPE 1 FORMULAIRE : INFORMATIONS ENTREPRISE -->
        <div id="step-company">

            <h3 class="mb-3 fw-semibold">Informations entreprise</h3>

            <div class="mb-3">
                <label class="form-label">Nom de l'entreprise *</label>
                <input id="nom_entreprise" type="text" name="nom_entreprise" class="form-control" value="<?= $value('nom_entreprise') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Secteur d'activité *</label>
                <select id="secteur_id" name="secteur_id" class="form-select" required>
                    <?php foreach ($secteurs as $s): ?>
                        <?php $selected = ((int)$value('secteur_id') === (int)$s['id']) ? 'selected' : ''; ?>
                        <option value="<?= $echappe($s['id']) ?>" <?= $selected ?>><?= $echappe($s['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Adresse *</label>
                <input type="text" id="adresse" name="adresse" class="form-control" value="<?= $echappe($value('adresse')) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Code postal *</label>
                    <input id="code_postal" type="text" name="code_postal" class="form-control" value="<?= $echappe($value('code_postal')) ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Ville *</label>
                    <input type="text" id="ville" name="ville" class="form-control" value="<?= $echappe($value('ville')) ?>" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Pays *</label>
                    <input type="text" id="pays" name="pays" class="form-control" value="<?= $echappe($value('pays')) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone professionnel</label>
                <input id="telephone_entreprise" type="text" name="telephone" class="form-control" value="<?= $echappe($value('telephone')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Email entreprise</label>
                <input id="email_entreprise" type="email" name="email_entreprise" class="form-control" value="<?= $echappe($value('email_entreprise')) ?>">
            </div>

            
            <div class="mb-3">
                <label class="form-label">SIRET *</label>
                <input id="siret" type="text" name="siret" class="form-control siret-input" maxlength="14" value="<?= $echappe($value('siret')) ?>" required>
                <div class="validity-feedback mt-1 small d-none">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    SIRET valide (14 chiffres)
                </div>
                <div class="invalid-feedback mt-1 d-none">
                    <i class="bi bi-x-circle-fill text-danger me-1"></i>
                    SIRET : 14 chiffres requis
                </div>
            </div>


            <div class="mb-3">
                <label class="form-label">Site web</label>
                <input id="url" name="site_web" class="form-control" placeholder="https://monsite.fr" value="<?= $echappe($value('site_web')) ?>">
            </div>
                
            <div class="mb-3">
                <label class="form-label">Taille de l'entreprise</label>
                <select id="taille" name="taille" class="form-select">
                    <?php
                        $tailleOptions = ['1-10', '11-50', '51-250', '250+'];
                        foreach ($tailleOptions as $opt):
                            $selected = ($value('taille') === $opt) ? 'selected' : '';
                    ?>
                        <option value="<?= $e($opt) ?>" <?= $selected ?>><?= $e($opt) ?> salariés</option>
                <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" 
                          placeholder="Décrivez brièvement votre entreprise et ses activités..."><?= $echappe($value('description')) ?></textarea>
            </div>


            <div class="d-flex justify-content-between mt-4">
                <a href="/login" class="btn btn-outline-secondary">Annuler</a>
                <button type="button" id="btnNextToManager" class="btn btn-primary">
                    Continuer
                </button>
            </div>

        </div>

        <!-- ============================================================
             ÉTAPE 2 : Gestionnaire
        ============================================================= -->
        <div id="step-manager" style="display:none;">

            <h3 class="mb-3 fw-semibold">Gestionnaire du compte</h3>

            <div class="mb-3">
                <label class="form-label">Prénom *</label>
                <input id="prenom" type="text" name="prenom" class="form-control" value="<?= $echappe($value('prenom')) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nom *</label>
                <input id="nom" type="text" name="nom" class="form-control" value="<?= $echappe($value('nom')) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email professionnel *</label>
                <input id="email" type="email" name="email" class="form-control" value="<?= $echappe($value('email')) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone professionnel</label>
                <input id="telephone_gestionnaire" type="text" name="telephone_gestionnaire" class="form-control" value="<?= $echappe($value('telephone_gestionnaire')) ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mot de passe *</label>
                    <div class="input-group">
                      <input type="password" name="password" id="password" class="form-control" required>
                      <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                        <i class="bi bi-eye"></i>
                      </button>
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
                    <label class="form-label">Confirmation *</label>
                    <div class="input-group">
                      <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                      <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirm">
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
                <button type="button" id="btnBackToCompany" class="btn btn-outline-secondary">
                    Retour
                </button>
                <button type="button" id="btnNextToRecap" class="btn btn-primary">
                    Continuer
                </button>
            </div>

        </div>

        <!-- ============================================================
             ÉTAPE 3 : Récapitulatif
        ============================================================= -->
        <div id="step-recap" style="display:none;">

            <h3 class="mb-3 fw-semibold">Récapitulatif</h3>
            <p class="text-muted small mb-4">
                Vérifiez les informations avant validation.
            </p>

            <div id="recap-content" class="border rounded p-3 bg-light"></div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="btnBackToManager" class="btn btn-outline-secondary">
                    Retour
                </button>
                <button type="submit" class="btn btn-success">
                    Confirmer et créer le compte
                </button>
            </div>

        </div>

    </form>
</div>

