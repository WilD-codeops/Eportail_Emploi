<?php use App\Core\Auth; ?>
<!-- ===================== HERO SECTION ===================== -->
<section class="hero-section py-5 py-lg-4 text-white">
    <div class="container">
        <div class="row align-items-center">

            <?php $role = Auth::role(); ?>

            <!-- ===================== COLONNE TEXTE ===================== -->
            <div class="col-lg-6 mb-4">

                <!-- ===================== TITRE DYNAMIQUE ===================== -->
                <h1 class="fw-bold display-5 mb-3">
                    <?php
                    switch ($role) {

                        // VISITEUR
                        case null:
                            echo "Trouvez l’emploi<br>qui vous correspond.";
                            break;

                        // CANDIDAT
                        case 'candidat':
                            echo "Avancez dans votre<br>parcours professionnel.";
                            break;

                        // RECRUTEUR
                        case 'recruteur':
                            echo "Attirez les talents<br>dont vous avez besoin.";
                            break;

                        // GESTIONNAIRE
                        case 'gestionnaire':
                            echo "Pilotez vos équipes<br>et vos recrutements.";
                            break;

                        // ADMIN
                        case 'admin':
                            echo "Supervisez la<br>plateforme EPortailEmploi.";
                            break;
                    }
                    ?>
                </h1>

                <!-- ===================== SOUS-TITRE DYNAMIQUE ===================== -->
                <p class="lead mb-4">
                    <?php
                    switch ($role) {

                        case null:
                            echo "Une plateforme pensée pour mettre en relation les candidats et les entreprises,
                                  avec une expérience simple, moderne et efficace.";
                            break;

                        case 'candidat':
                            echo "Consultez les offres, suivez vos candidatures et valorisez votre profil en quelques clics.";
                            break;

                        case 'recruteur':
                            echo "Publiez vos offres, gérez vos candidatures et gagnez du temps sur vos recrutements.";
                            break;

                        case 'gestionnaire':
                            echo "Centralisez les offres, les candidatures et la gestion de votre équipe au même endroit.";
                            break;

                        case 'admin':
                            echo "Accédez aux outils de gestion, de suivi et d’administration de la plateforme.";
                            break;
                    }
                    ?>
                </p>

                <!-- ===================== BOUTONS D’ACTION ===================== -->
                <div class="d-flex flex-wrap gap-2">
                    <?php
                    switch ($role) {

                        // VISITEUR
                        case null:
                            ?>
                            <a href="/offres" class="btn btn-primary btn-lg">
                                Je cherche un emploi
                            </a>
                            <a href="/register/entreprise" class="btn btn-outline-light btn-lg">
                                Je recrute
                            </a>
                            <?php
                            break;

                        // CANDIDAT
                        case 'candidat':
                            ?>
                            <a href="/offres" class="btn btn-primary btn-lg">
                                Rechercher une offre
                            </a>
                            <a href="/maintenance" class="btn btn-outline-light btn-lg">
                                Mes candidatures
                            </a>
                            <?php
                            break;

                        // RECRUTEUR
                        case 'recruteur':
                            ?>
                            <a href="/dashboard/offres" class="btn btn-primary btn-lg">
                                Gérer mes offres
                            </a>
                            <a href="/dashboard/offres/create" class="btn btn-outline-light btn-lg">
                                Publier une offre
                            </a>
                            <?php
                            break;

                        // GESTIONNAIRE
                        case 'gestionnaire':
                            ?>
                            <a href="/dashboard/offres" class="btn btn-primary btn-lg">
                                Offres de l’équipe
                            </a>
                            <a href="/maintenance" class="btn btn-outline-light btn-lg">
                                Gérer mon équipe
                            </a>
                            <?php
                            break;

                        // ADMIN
                        case 'admin':
                            ?>
                            <a href="/admin/utilisateurs" class="btn btn-primary btn-lg">
                                Gérer les utilisateurs
                            </a>
                            <a href="/admin/offres" class="btn btn-outline-light btn-lg">
                                Gérer les offres
                            </a>
                            <?php
                            break;
                    }
                    ?>
                </div>
            </div>

            <!-- ===================== COLONNE ILLUSTRATION ===================== -->
            <div class="col-lg-6 text-center">
                <div class="hero-illustration rounded-4 shadow-lg mx-auto p-4 p-lg-3">
                    <span class="fw-semibold d-block mb-2">EPortailEmploi</span>
                    <p class="mb-0 small opacity-75">
                        Interface inspirée de la maquette Figma : tableau de bord, cartes d’offres, statistiques…
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>