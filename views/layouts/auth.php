<?php
/**
 * Layout pour toutes les pages d'authentification
 *
 * Variables attendues :
 * - string $title        : titre de la page (onglet navigateur)
 * - string $content      : contenu de la colonne gauche (formulaire)
 * - string $authVariant  : type de page ("login", "register", "forgot", "reset")
 */
?>
<?php
var_dump(password_hash('lucasmorel!2026', PASSWORD_BCRYPT));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'EPortailEmploi - Authentification', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    >   
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS custom -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth-body">

    <!-- GESTION DES ALERTES SWEETALERT -->
    <?php include __DIR__ . '/../partials/alerts.php'; ?> 




<div class="auth-layout">
    <!-- Colonne gauche : logo + formulaire -->
    <div class="auth-panel auth-panel-left">
        <div class="auth-left-inner">

            <!-- Logo + retour home -->
            <div class="auth-header mb-4">
                <a href="/" class="auth-home-icon">
                    <i class="bi bi-house-fill"></i>
                </a>
                <div class="auth-logo-block">
                    <div class="auth-logo-icon">
                        <!-- Tu pourras remplacer par ton vrai logo -->
                        <span>E</span>
                    </div>
                    <div class="auth-logo-text">
                        <span class="auth-logo-title">EPortailEmploi</span>
                        <span class="auth-logo-subtitle">votre passerelle de carrière</span>
                    </div>
                </div>
            </div>

            <!-- Contenu spécifique à la page (formulaire, titres, etc.) -->
            <?= $content ?? '' ?>

        </div>
    </div>

    <!-- Colonne droite : visuel selon la page -->
    <div class="auth-panel auth-panel-right">
        <div class="auth-right-inner">
            <?php
            // On change le contenu en fonction du type de page
            $variant = $authVariant ?? 'login';

            if ($variant === 'login'): ?>
                <h2 class="auth-right-title">Trouvez votre emploi idéal</h2>
                <p class="auth-right-text">
                    Connectez-vous pour accéder aux meilleures opportunités de carrière
                    et échanger avec des recruteurs de premier plan.
                </p>
                <div class="auth-right-illustration auth-right-illustration--login"></div>

            <?php elseif ($variant === 'register_candidat'): ?>
                <h2 class="auth-right-title">Créez votre profil</h2>
                <p class="auth-right-text">
                    Rejoignez EPortailEmploi et mettez en valeur votre parcours,
                    vos compétences et vos aspirations professionnelles.
                </p>
                <div class="auth-right-illustration auth-right-illustration--register"></div>

            <?php elseif ($variant === 'register_entreprise'): ?>
                <h2 class="auth-right-title">Recrutez les meilleurs talents</h2>    
                <p class="auth-right-text">
                    Rejoignez EPortailEmploi et présentez votre entreprise,
                    vos opportunités de carrière et votre culture professionnelle.
                </p>
                <div class="auth-right-illustration auth-right-illustration--register"></div>

            <?php elseif ($variant === 'forgot'): ?>
                <h2 class="auth-right-title">Récupération sécurisée</h2>
                <p class="auth-right-text">
                    Nous vous envoyons un lien sécurisé pour réinitialiser votre mot de passe
                    en toute confidentialité.
                </p>
                <div class="auth-right-illustration auth-right-illustration--forgot"></div>

            <?php elseif ($variant === 'reset'): ?>
                <h2 class="auth-right-title">Protection renforcée</h2>
                <p class="auth-right-text">
                    Créez un mot de passe fort pour sécuriser votre compte
                    et protéger vos données personnelles.
                </p>
                <div class="auth-right-illustration auth-right-illustration--reset"></div>

            <?php else: ?>
                <!-- Variante par défaut -->
                <h2 class="auth-right-title">Espace sécurisé</h2>
                <p class="auth-right-text">
                    Accédez à votre espace professionnel en toute sécurité.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<!-- JS custom -->
<script src="/assets/js/app.js"></script>
<script src="/assets/js/registerEntreprise.js"></script>
</body>
</html>