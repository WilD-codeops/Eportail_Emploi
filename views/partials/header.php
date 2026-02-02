<?php
use App\Core\Auth;

$role = Auth::role();
?>

<header class="shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="/" aria-label="Accueil EPortailEmploi">
                <span translate="no" class="text-primary">E</span>PortailEmploi
            </a>

            <!-- Toggler mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                    aria-expanded="false" aria-label="Ouvrir le menu de navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">

                <!-- Liens principaux -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item mx-lg-2">
                        <a class="nav-link nav-link-strong" href="/offres" aria-label="Voir les offres d'emploi">
                            Offres d'emploi
                        </a>
                    </li>
                    <li class="nav-item mx-lg-2">
                        <a class="nav-link nav-link-strong" href="/entreprises" aria-label="Voir les entreprises">
                            Entreprises
                        </a>
                    </li>
                </ul>

                <!-- ===================== DYNAMIQUE AUTH ===================== -->
                <div class="d-flex flex-wrap gap-2 ms-lg-3 mt-3 mt-lg-0">

                    <?php if (!Auth::isLogged()): ?>

                        <!-- ===================== NON CONNECTÉ ===================== -->
                        <a translate="no"
                           class="btn btn-green btn-sm"
                           href="/login"
                           aria-label="Se connecter">
                            Connexion
                        </a>

                        <a class="btn btn-primary btn-sm"
                           href="/register/candidat"
                           aria-label="Créer un compte candidat">
                            S'inscrire
                        </a>
                    <?php else: ?>

                        <!-- ===================== CONNECTÉ ===================== -->
                        <div class="d-flex align-items-center me-3" aria-label="Rôle utilisateur connecté">
                            <span class="role-indicator me-2"
                                  style="background: var(--color-secondary-green-dark);
                                         width: 12px; height: 12px; border-radius: 50%;
                                         display: inline-block;"></span>

                            <span class="fw-bold text-capitalize">
                                <?= htmlspecialchars($role) ?>
                            </span>
                        </div>

                        <!-- Déconnexion -->
                        <a class="btn btn-outline-danger btn-sm me-2"
                           href="/logout"
                           aria-label="Se déconnecter">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>

                        <!-- ===================== SWITCH DASHBOARD ===================== -->
                        <?php
                        switch ($role) {

                            case 'candidat':
                                echo '<a class="btn btn-primary btn-sm" href="/candidat/dashboard" aria-label="Accéder à l’espace candidat">Mon espace</a>';
                                break;

                            case 'recruteur':
                                echo '<a class="btn btn-primary btn-sm" href="/dashboard/offres" aria-label="Accéder à l’espace recruteur">Mon espace</a>';
                                break;

                            case 'gestionnaire':
                                echo '<a class="btn btn-primary btn-sm" href="/dashboard/equipe" aria-label="Accéder à l’espace gestionnaire">Mon espace</a>';
                                break;

                            case 'admin':
                                echo '<a class="btn btn-primary btn-sm" href="/admin/users" aria-label="Accéder à l’espace administrateur">Mon espace</a>';
                                break;

                            default:
                                echo '<a class="btn btn-primary btn-sm" href="/" aria-label="Accéder à l’espace utilisateur">Mon espace</a>';
                                break;
                        }
                        ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </nav>
</header>