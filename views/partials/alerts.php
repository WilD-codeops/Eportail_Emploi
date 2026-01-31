<?php
    //GESTION DES ERREURS PASSÉES PAR LE CONTRÔLEUR EN VARIABLE $error ou EN SESSION['error']

    $errorMessgae = $error ?? $_SESSION['error'] ?? null; // si error n'existe pas, on regarde en session si une erreur y est stockée

    if (!empty($errorMessgae)): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: '<?= htmlspecialchars($errorMessgae) ?>',
        confirmButtonColor: '#d33'
    });
    </script>

    <?php unset($_SESSION['error']); // on supprime l'erreur de la session après l'avoir affichée eviter boucle d'erreur ?> 

<?php endif; ?>

<?php 
    //GESTION DES SUCCÈS PASSÉS PAR LE CONTRÔLEUR EN VARIABLE $success 
    if (!empty($_SESSION['success'])): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: '<?= htmlspecialchars($_SESSION['success']) ?>',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
    });
    </script>
    <?php unset($_SESSION['success']); // on supprime le succès de la session après l'avoir affichée eviter boucle de succès  ?>
<?php endif; ?>



<?php 
    // AFFICHEGE DES ALERTES BASEES SUR LE PARAMETRE "reason" DANS L'URL
    if (isset($_GET['reason'])): ?>
    <script>
    const messages = {
        // CONNEXION / DÉCONNEXION
        'logout': {icon: 'success', title: 'Déconnecté !', text: 'À bientôt sur EPortailEmploi !', timer: 2500},
        'loggedin': {icon: 'success', title: 'Connexion réussie !', text: 'Bienvenue <?= htmlspecialchars($_SESSION['user_prenom'] ?? '') ?> connexion réussie.', timer: 2000},
        

        // AVERTISSEMENTS
        'unauthenticated': {icon: 'warning', title: 'Connexion requise', text: 'Veuillez vous connecter.', timer: 4000},
        'expired': {icon: 'warning', title: 'Session expirée', text: 'Reconnexion nécessaire.', timer: 4000},

        // REFUS / ERREURS
        'forbidden': {icon: 'error', title: 'Accès interdit', text: 'Droits insuffisants pour cette page.', timer: 3500},
        'csrf': {icon: 'error', title: 'Sécurité', text: 'Token CSRF invalide. Rechargez la page.', timer: 4500},

        // ℹ️ INFO
        'info': {icon: 'info', title: 'Information', text: 'Action effectuée.', timer: 3000}
    };

    const reason = '<?= htmlspecialchars($_GET['reason']) ?>';
    const msg = messages[reason] || messages.forbidden;

    Swal.fire({
        icon: msg.icon,
        title: msg.title,
        text: msg.text,
        timer: msg.timer,
        timerProgressBar: true,
        showConfirmButton: false
    });
    </script>
<?php endif; ?>
