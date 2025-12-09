<?php if (isset($_GET['reason']) || isset($_SESSION['error'])): ?>
<script>

const reason = '<?= htmlspecialchars($_GET['reason'] ?? $_SESSION['error'] ?? '') ?>';
const messages = {
    'logout': {icon: 'success', title: 'Déconnecté !', text: 'À bientôt !', timer: 2500},
    'unauthenticated': {icon: 'warning', title: 'Connexion requise', text: 'Veuillez vous connecter.', timer: 4000},
    'forbidden': {icon: 'error', title: 'Accès interdit', text: 'Droits insuffisants.', timer: 3500},
    'expired': {icon: 'warning', title: 'Session expirée', text: 'Reconnexion nécessaire.', timer: 4000}
};

if (messages[reason]) {
    Swal.fire({
        icon: messages[reason].icon,
        title: messages[reason].title,
        text: messages[reason].text,
        timer: messages[reason].timer,
        timerProgressBar: true,
        showConfirmButton: false
    });
}
</script>
<?php unset($_SESSION['error']); endif; ?>
