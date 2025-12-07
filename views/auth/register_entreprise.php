<div class="auth-form-wrapper mt-4">

    <h1 class="auth-page-title">Créer un espace entreprise</h1>
    <p class="auth-page-subtitle">
        Rejoignez EPortailEmploi et centralisez votre gestion de recrutement.
    </p>

    <!-- Toggle Candidat / Recruteur (visuel pour l'instant) -->
    <div class="auth-role-toggle" aria-label="Choix du type de compte">
        <button type="button" >Candidat</button>
        <button type="button" class="active">Recruteur</button>
    </div>

    <!-- Zone d’erreurs (accessibilité ARIA) -->
    <div id="form-errors" class="alert alert-danger d-none" aria-live="assertive"></div>

    <form id="entrepriseForm" method="post" action="/register/entreprise" onsubmit="return validateFinalStep()" class="auth-form mt-4">

        <!-- ============================================================
             ÉTAPE 1 : Informations entreprise
        ============================================================= -->
        <div id="step-company">

            <h3 class="mb-3 fw-semibold">Informations entreprise</h3>

            <div class="mb-3">
                <label class="form-label">Nom de l'entreprise *</label>
                <input type="text" name="nom_entreprise" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Secteur d'activité *</label>
                <select name="secteur_id" class="form-select" required>
                    <option value="">— Choisir un secteur —</option>
                    <option value="1">Informatique</option>
                    <option value="2">Marketing</option>
                    <option value="3">Ressources humaines</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Adresse *</label>
                <input type="text" name="adresse" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Code postal *</label>
                    <input type="text" name="code_postal" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Ville *</label>
                    <input type="text" name="ville" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Pays *</label>
                    <input type="text" name="pays" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone professionnel</label>
                <input type="text" name="telephone" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Email entreprise</label>
                <input type="email" name="email_entreprise" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">SIRET *</label>
                <input type="text" name="siret" class="form-control" required>
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
                <input type="text" name="prenom" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Nom *</label>
                <input type="text" name="nom" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Email professionnel *</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone professionnel</label>
                <input type="text" name="telephone_gestionnaire" class="form-control">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mot de passe *</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmation *</label>
                    <input type="password" name="password_confirm" class="form-control">
                </div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="cgu" required>
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