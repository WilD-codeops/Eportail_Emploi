<?php
 // Fonction utilitaire pour échapper les valeurs affichées
$e = static fn($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

/** @var string $title */
/** @var string $titre */;
?>
<!-- Bannière -->
<?php require __DIR__ . '/../partials/banniere.php'; ?>

<main class="container py-5" role="main" aria-labelledby="mentions-title">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 id="mentions-title" class="h4 fw-semibold mb-4">Informations légales</h2>

            <p class="text-muted">
                Conformément aux dispositions légales en vigueur, vous trouverez ci‑dessous les informations relatives à l’éditeur du site, à l’hébergement et aux conditions d’utilisation.
            </p>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Éditeur du site</h3>
                <p class="text-muted mb-0">
                    Nom de l’entreprise<br>
                    Adresse complète<br>
                    SIRET : 000 000 000 00000<br>
                    Email : contact@entreprise.fr
                </p>
            </section>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Hébergement</h3>
                <p class="text-muted mb-0">
                    Hébergeur : Nom de l’hébergeur<br>
                    Adresse complète<br>
                    Téléphone : 00 00 00 00 00
                </p>
            </section>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Responsabilité</h3>
                <p class="text-muted">
                    Les informations présentes sur ce site sont fournies à titre indicatif. L’éditeur ne saurait être tenu responsable en cas d’erreur, d’omission ou d’indisponibilité du service.
                </p>
            </section>

            <a href="/" class="btn btn-primary px-4 py-2" style="border-radius: var(--radius-md);">
                <i class="bi bi-arrow-left-circle me-2"></i>
                Retour à l’accueil
            </a>

        </div>
    </div>

</main>