<?php
 // Fonction utilitaire pour échapper les valeurs affichées
$e = static fn($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

/** @var string $title */
/** @var string $titre */;
?>

<!-- Bannière -->
<?php require __DIR__ . '/../partials/banniere.php'; ?> 


<main class="container py-5" role="main" aria-labelledby="aide-title">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 id="aide-title" class="h4 fw-semibold mb-4">Comment pouvons‑nous vous aider ?</h2>

            <p class="text-muted mb-4">
                Retrouvez ici les réponses aux questions les plus fréquentes.  
                Si vous ne trouvez pas votre réponse, vous pouvez également nous contacter.
            </p>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Créer un compte</h3>
                <p class="text-muted">
                    Pour créer un compte, cliquez sur “Inscription” dans le menu principal et suivez les étapes.
                </p>
            </section>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Mot de passe oublié</h3>
                <p class="text-muted">
                    Rendez‑vous sur la page “Mot de passe oublié” et entrez votre adresse email pour recevoir un lien de réinitialisation.
                </p>
            </section>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Candidatures</h3>
                <p class="text-muted">
                    Vous pouvez postuler directement depuis la page d’une offre. Un tableau de bord vous permet ensuite de suivre vos candidatures.
                </p>
            </section>

            <a href="/" class="btn btn-primary px-4 py-2" style="border-radius: var(--radius-md);">
                <i class="bi bi-arrow-left-circle me-2"></i>
                Retour à l’accueil
            </a>

        </div>
    </div>

</main>