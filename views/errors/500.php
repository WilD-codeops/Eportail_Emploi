<?php 
if(isset($_SESSION['systemError'])){
    $errorMessage = $_SESSION['systemError'];
    unset($_SESSION['systemError']);
} else {
    $errorMessage = null;
}
?>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg p-4" style="max-width: 600px; width: 100%;">
        
        <div class="text-center mb-3">
            <h1 class="text-danger fw-bold">Erreur 500</h1>
            <p class="text-muted">
                Une erreur interne est survenue. Le serveur n'a pas pu traiter la demande.
            </p>
        </div>

        <?php if (!empty($errorMessage) ): ?>
            <div class="alert alert-warning">
                <h5 class="fw-bold">Message technique (mode développement)</h5>
                <pre class="bg-light p-3 border rounded small"><?= htmlspecialchars($errorMessage) ?></pre>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="/" class="btn btn-primary">
                Retour à l'accueil
            </a>
        </div>

    </div>
</div>