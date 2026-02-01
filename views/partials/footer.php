            <footer class="site-footer" role="contentinfo">
                <div class="container py-5">
                    <div class="row gy-4">

                        <!-- Colonne logo + texte -->
                        <div class="col-md-4 text-center text-md-start">
                <div class="footer-brand mb-3">
                    <span class="footer-logo">EPortail<span>Emploi</span></span>
                </div>

                <p class="">
                    La plateforme de référence pour votre carrière professionnelle.</p>

                <div class="footer-social d-flex gap-2 justify-content-center justify-content-md-start">
                    <a href="#" aria-label="LinkedIn" class="footer-social-icon">in</a>
                    <a href="#" aria-label="Twitter / X" class="footer-social-icon">X</a>
                    <a href="#" aria-label="Paramètres" class="footer-social-icon">⚙</a>
                </div>
            </div>

            <?php $role = $_SESSION['user_role'] ?? null; ?>

            <!-- Colonne Candidats (visible pour tous) -->
            <div class="col-6 col-md-2">
                <h6 id="footer-candidats" class="footer-title">Candidats</h6>
                <ul class="footer-links list-unstyled" aria-labelledby="footer-candidats">
                    <li><a href="/offres/public_list">Rechercher un emploi</a></li>

                    <?php if (!$role): ?>
                        <li><a href="/candidat/register_candidat">Créer un profil</a></li>
                    <?php endif; ?>

                    <?php if ($role === 'candidat'): ?>
                        <li><a href="/maintenance">Mon espace candidat</a></li>
                        <li><a href="/maintenance">Mes candidatures</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Colonne Recruteurs -->
            <?php if ($role === 'recruteur' || $role==='gestionnaire'): ?>
            <div class="col-6 col-md-3">
                <h6 id="footer-recruteurs" class="footer-title">Recruteurs</h6>
                <ul class="footer-links list-unstyled" aria-labelledby="footer-recruteurs">
                    <li><a href="dashboard/offres">Gérer mes offres</a></li>

                    <?php if ($role === 'gestionnaire'): ?>
                        <li><a href="/maintenance">Gérer mon equipe</a></li>
                        <li><a href="/maintenance">Voir les candidatures</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Colonne Admin -->
            <?php if ($role === 'admin'): ?>
            <div class="col-6 col-md-3">
                <h6 id="footer-admin" class="footer-title">Administration</h6>
                <ul class="footer-links list-unstyled" aria-labelledby="footer-admin">
                    <li><a href="/admin/utilisateurs">Gérer les utilisateurs</a></li>
                    <li><a href="/admin/offres">Gérer les offres</a></li>
                    <li><a href="/admin/entreprises">Gérer les entreprises</a></li>
                    <li><a href="/maintenance">Logs & sécurité</a></li>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Colonne Support -->
            <div class="col-6 col-md-3">
                <h6 id="footer-support" class="footer-title">Support</h6>
                <ul class="footer-links list-unstyled" aria-labelledby="footer-support">
                    <li><a href="/centre-aide">Centre d’aide</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/a-propos">À propos</a></li>
                    <li><a href="/mentions-legales">Mentions légales</a></li>
                </ul>
            </div>

        </div>
    </div>

    <div class="footer-bottom py-3">
        <div class="container text-center small">
            © 2026 EPortailEmploi. Tous droits réservés.
        </div>
    </div>
</footer>