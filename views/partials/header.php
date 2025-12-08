<?php
use App\Core\Auth;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            <span class="text-primary">E</span>PortailEmploi
        </a>

        <!-- Toggler mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Basculer la navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item mx-lg-2">
                    <a class="nav-link" href="/offres">Offres d'emploi</a>
                </li>
                <li class="nav-item mx-lg-2">
                    <a class="nav-link" href="/entreprises">Entreprises</a>
                </li>
            </ul>

            <!-- DYNAMIQUE AUTH -->
            <div class="d-flex flex-wrap gap-2 ms-lg-3 mt-3 mt-lg-0">
                <?php if (!Auth::isLogged()): ?>
                    
                    <!-- NON CONNECTÉ -->
                    <a class="btn btn-outline-primary btn-sm" href="/login">
                        Connexion
                    </a>
                    <a class="btn btn-primary btn-sm" href="/register/candidat">
                        S'inscrire
                    </a>
                <?php else: ?>

                    <!-- CONNECTÉ -->
                    <div class="d-flex align-items-center me-3">
                        <span class="role-indicator me-2" style="background: var(--color-secondary-green-dark); width: 12px; height: 12px; border-radius: 50%; display: inline-block;"></span>
                        <span class="fw-bold text-capitalize"><?= htmlspecialchars(Auth::role()) ?></span>
                    </div>

                    <a class="btn btn-outline-danger btn-sm me-2" href="/logout" title="Déconnexion">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>

                    <a class="btn btn-primary btn-sm" href="/<?= htmlspecialchars(Auth::role()) ?>/dashboard">
                        Mon espace
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
