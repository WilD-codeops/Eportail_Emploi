<!-- ===================== CTA SECTION ===================== -->
<section class="cta-section text-bs-light py-5" aria-labelledby="cta-title">
    <div class="container text-center">

        <?php $role = $_SESSION['user_role'] ?? null; ?>

        <!-- ===================== VISITEUR NON CONNECTÉ ===================== -->
        <?php if (!$role): ?>

            <h3 id="cta-title" class="fw-bold mb-4">
                Prêt à décrocher votre prochain emploi ?
            </h3>

            <p class="mb-3 opacity-75">
                Rejoignez des milliers de candidats qui ont trouvé leur job idéal.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/register/candidat" class="btn btn-light btn-lg">Créer mon profil</a>
                <a href="/offres" class="btn btn-outline-dark text-light btn-lg">Explorer les offres</a>
            </div>

        <!-- ===================== UTILISATEURS CONNECTÉS ===================== -->
        <?php else: ?>

            <!-- Titre adapté au rôle -->
            <h3 id="cta-title" class="fw-bold mb-3">
                <?php if ($role === 'candidat'): ?>
                    Continuez à avancer dans votre parcours professionnel.
                <?php elseif ($role === 'recruteur'): ?>
                    Recrutez plus efficacement, en toute simplicité.
                <?php elseif ($role === 'gestionnaire'): ?>
                    Pilotez vos recrutements avec clarté et maîtrise.
                <?php elseif ($role === 'admin'): ?>
                    Supervisez les activités du portail.
                <?php endif; ?>
            </h3>

            <!-- Sous-texte léger -->
            <p class="opacity-75 mb-0">
                <?php if ($role === 'candidat'): ?>
                    Les offres évoluent chaque jour, restez attentif aux nouvelles opportunités.
                <?php elseif ($role === 'recruteur'): ?>
                    Des profils qualifiés vous attendent, prêts à rejoindre votre entreprise.
                <?php elseif ($role === 'gestionnaire'): ?>
                    Centralisez les candidatures et optimisez vos processus RH.
                <?php elseif ($role === 'admin'): ?>
                    Tous les outils sont à votre disposition pour assurer la fluidité du service.
                <?php endif; ?>
            </p>

        <?php endif; ?>

        <!-- ===================== FIN CTA SECTION ===================== -->

    </div>
</section>