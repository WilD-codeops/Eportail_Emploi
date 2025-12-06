<?php

?>
<div class="mt-4">
    <h1 class="auth-page-title">Bienvenue</h1>
    <p class="auth-page-subtitle">
        Connectez-vous pour accéder à votre espace professionnel EPortailEmploi.
    </p>
<?php var_dump($_SESSION);  ?>
    <!-- Toggle Candidat / Recruteur (visuel pour l'instant) -->
    <div class="auth-role-toggle" aria-label="Choix du type de compte">
        <button type="button" class="active">Candidat</button>
        <button type="button">Recruteur</button>
    </div>

    <!-- Formulaire de connexion -->
    <form method="post" action="/Eportail_Emploi/public/login" class="auth-form mt-4">
        <div class="mb-3">
            <label for="email" class="form-label">Email professionnel</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="votre@email.com"
                    required
                >
            </div>
        </div>

        <div class="mb-2">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Votre mot de passe"
                    required
                >
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    value="1"
                    id="remember"
                    name="remember"
                >
                <label class="form-check-label" for="remember">
                    Se souvenir de moi
                </label>
            </div>
            <a href="/mot-de-passe-oublie" class="auth-link">
                Mot de passe oublié ?
            </a>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-auth-primary btn-lg">
                Se connecter
            </button>
        </div>

        <p class="small text-muted mb-0">
            Nouveau sur EPortailEmploi ?
            <a href="/Eportail_Emploi/public/register_candidat" class="auth-link">Créer un compte gratuitement</a>
        </p>
    </form>
</div>