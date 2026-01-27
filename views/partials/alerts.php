<?php
    //GESTION DES ERREURS PASSÉES PAR LE CONTRÔLEUR EN VARIABLE $error 
    if (!empty($error)): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: '<?= htmlspecialchars($error) ?>',
        confirmButtonColor: '#d33'
    });
    </script>
<?php endif; ?>


<?php 
    // AFFICHEGE DES ALERTES BASEES SUR LE PARAMETRE "reason" DANS L'URL
    if (isset($_GET['reason'])): ?>
    <script>
    const messages = {
        // SUCCÈS
        'logout': {icon: 'success', title: 'Déconnecté !', text: 'À bientôt sur EPortailEmploi !', timer: 2500},
        'loggedin': {icon: 'success', title: 'Connexion réussie !', text: 'Bienvenue dans votre espace.', timer: 2000},
        'created': {icon: 'success', title: 'Créé avec succès !', text: 'Votre compte est actif.', timer: 3000},
        'updated': {icon: 'success', title: 'Mis à jour !', text: 'Modifications enregistrées.', timer: 2500},

        // AVERTISSEMENTS
        'unauthenticated': {icon: 'warning', title: 'Connexion requise', text: 'Veuillez vous connecter pour continuer.', timer: 4000},
        'expired': {icon: 'warning', title: 'Session expirée', text: 'Reconnexion nécessaire.', timer: 4000},

        // REFUS / ERREURS
        'forbidden': {icon: 'error', title: 'Accès interdit', text: 'Droits insuffisants pour cette page.', timer: 3500},
        'invalid_email': {icon: 'error', title: 'Email invalide', text: 'Format email incorrect.', timer: 4000},
        'duplicate': {icon: 'error', title: 'Déjà existant', text: 'Cet email est déjà utilisé.', timer: 4000},
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
