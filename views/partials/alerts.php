<?php if (isset($_GET['reason']) || isset($_SESSION['error'])): ?>
<div id="error-alert" class="alert alert-warning alert-dismissible fade show" role="alert">
    <?php 
    $reason = $_GET['reason'] ?? $_SESSION['error'] ?? '';
    
    $messages = [
        'unauthenticated' => "Veuillez vous connecter pour accéder à cette page",
        'forbidden' => "Accès restreint. Droits insuffisants",
        'expired' => "Session expirée. Reconnexion requise"
    ];
    
    echo $messages[$reason] ?? "Une erreur s'est produite";
    unset($_SESSION['error']); // Flash message
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<script>
Swal.fire({
    icon: 'warning',
    title: 'Accès restreint',
    text: '<?= htmlspecialchars($messages[$reason] ?? "Erreur") ?>',
    timer: 4000
});
</script>
<?php endif; ?>
