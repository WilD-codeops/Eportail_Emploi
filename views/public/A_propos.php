    <?php
 // Fonction utilitaire pour échapper les valeurs affichées
$e = static fn($val) => htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');

/** @var string $title */
/** @var string $titre */;
?>

<!-- Bannière -->
<?php require __DIR__ . '/../partials/banniere.php'; ?>


<main class="container py-5" role="main" aria-labelledby="apropos-title">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 id="apropos-title" class="h4 fw-semibold mb-4">EPortail Emploi, votre passerelle vers l’avenir professionnel</h2>

            <p class="text-muted mb-4">
                EPortail Emploi est une plateforme moderne et intuitive dédiée à la mise en relation entre 
                <strong>candidats</strong> et <strong>recruteurs</strong>. Notre mission est simple : faciliter la recherche d’emploi, 
                fluidifier le recrutement et offrir une expérience transparente, efficace et humaine à chaque utilisateur.
            </p>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Une plateforme pensée pour tous</h3>
                <p class="text-muted">
                    Que vous soyez en recherche d’un premier emploi, en reconversion ou déjà expérimenté, 
                    EPortail Emploi vous accompagne dans chaque étape.  
                    Les recruteurs disposent quant à eux d’outils performants pour publier leurs offres, 
                    gérer leurs candidatures et identifier rapidement les profils les plus pertinents.
                </p>
            </section>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Une expérience simple et intuitive</h3>
                <p class="text-muted">
                    Nous mettons l’accent sur la clarté, l’ergonomie et l’accessibilité.  
                    Notre interface a été conçue pour permettre à chacun de naviguer facilement, 
                    de consulter les offres, de découvrir les entreprises partenaires et de postuler en quelques clics.
                </p>
            </section>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Des entreprises partenaires engagées</h3>
                <p class="text-muted">
                    EPortail Emploi collabore avec un large réseau d’entreprises de tous secteurs.  
                    Chaque partenaire s’engage à proposer des opportunités fiables, actualisées et adaptées aux besoins du marché.
                </p>
            </section>

            <section class="mb-4">
                <h3 class="h5 fw-semibold">Notre vision</h3>
                <p class="text-muted">
                    Nous croyons en un marché de l’emploi plus accessible, plus transparent et plus humain.  
                    Notre objectif est de devenir un acteur de référence en offrant une plateforme qui valorise autant 
                    les talents que les entreprises, et qui contribue à créer des rencontres professionnelles durables.
                </p>
            </section>

            <a href="/" class="btn btn-primary px-4 py-2" style="border-radius: var(--radius-md);">
                <i class="bi bi-arrow-left-circle me-2"></i>
                Retour à l’accueil
            </a>

        </div>
    </div>

</main>