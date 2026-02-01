<?php
 // Fonction utilitaire pour échapper les valeurs affichées
$e = static fn($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');//ent_QUOTES pour éviter les problèmes avec les apostrophes et utf-8 pour gérer les caractères spéciaux correctement

/** @var string $title */
/** @var string $titre */;
?>

<!-- Bannière -->
<?php require __DIR__ . '/../partials/banniere.php'; ?>

<main class="container py-5" role="main" aria-labelledby="contact-title">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 id="contact-title" class="h4 fw-semibold mb-4">Nous contacter</h2>

            <p class="text-muted mb-4">
                Une question, un problème ou un besoin d’assistance ?  
                Remplissez le formulaire ci‑dessous, nous reviendrons vers vous rapidement.
            </p>

            <form method="POST" action="/contact/send" class="mb-4">

                <div class="mb-3">
                    <label for="contact-nom" class="form-label fw-semibold">Nom *</label>
                    <input id="contact-nom" name="nom" type="text" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="contact-email" class="form-label fw-semibold">Email *</label>
                    <input id="contact-email" name="email" type="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="contact-message" class="form-label fw-semibold">Message *</label>
                    <textarea id="contact-message" name="message" rows="4" class="form-control" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: var(--radius-md);">
                    <i class="bi bi-send me-2"></i>
                    Envoyer
                </button>

            </form>

            <a href="/" class="btn btn-secondary px-4 py-2" style="border-radius: var(--radius-md);">
                <i class="bi bi-arrow-left-circle me-2"></i>
                Retour à l’accueil
            </a>

        </div>
    </div>

</main>