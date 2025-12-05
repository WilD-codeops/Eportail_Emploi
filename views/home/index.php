
<!-- ===================== HERO SECTION ===================== -->
<section class="hero-section py-5 text-white">
    <div class="container">
        <div class="row align-items-center">

            <!-- Colonne texte -->
            <div class="col-lg-6 mb-4">
                <h1 class="fw-bold display-5 mb-3">
                    Trouvez l’emploi<br>qui vous correspond.
                </h1>
                <p class="lead mb-4">
                    Une plateforme pensée pour mettre en relation les candidats et les entreprises,
                    avec une expérience simple, moderne et efficace.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="/login" class="btn btn-primary btn-lg">
                        Je cherche un emploi
                    </a>
                    <a href="/entreprise/register" class="btn btn-outline-light btn-lg">
                        Je recrute
                    </a>
                </div>
            </div>

            <!-- Colonne image -->
            <div class="col-lg-6 text-center">
                <div class="hero-illustration rounded-4 shadow-lg mx-auto p-4">
                    <span class="fw-semibold d-block mb-2">EPortailEmploi</span>
                    <p class="mb-0 small opacity-75">
                        Interface inspirée de la maquette Figma : tableau de bord, cartes d’offres, statistiques…
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Bloc recherche rapide -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="h4 text-center mb-4 fw-semibold">
            Rechercher une offre
        </h2>

        <form class="row g-3 justify-content-center">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Poste, mot-clé…">
            </div>

            <div class="col-md-3">
                <select class="form-select">
                    <option selected>Localisation</option>
                    <option>Île-de-France</option>
                    <option>Auvergne-Rhône-Alpes</option>
                    <option>Occitanie</option>
                </select>
            </div>

            <div class="col-md-3">
                <select class="form-select">
                    <option selected>Type de contrat</option>
                    <option>CDI</option>
                    <option>CDD</option>
                    <option>Alternance</option>
                    <option>Stage</option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">
                    Rechercher
                </button>
            </div>
        </form>
    </div>
</section>

<!-- ===================== LAST OFFERS SECTION ===================== -->
<section class="last-offers-section py-5">
    <div class="container">
        <h3 class="text-center mb-4 fw-semibold">Dernières offres publiées</h3>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm last-offer-card">
                    <div class="card-body">

                        <ul class="list-unstyled small mb-3">
                            <li class="mb-2">
                                <strong>Développeur PHP / MySQL</strong><br>
                                <span class="text-muted">CDI – Paris</span>
                            </li>
                            <li class="mb-2">
                                <strong>Chargé de Recrutement</strong><br>
                                <span class="text-muted">CDI – Lyon</span>
                            </li>
                            <li>
                                <strong>Chef de Projet Digital</strong><br>
                                <span class="text-muted">CDD – Lille</span>
                            </li>
                        </ul>

                        <a href="/offres" class="btn btn-primary w-100">
                            Voir toutes les offres
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>





