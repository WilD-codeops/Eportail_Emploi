<h1>Offres d'emploi</h1>
<?php if (!isset(($offres))): ?>
    <h2>Impossible de récuperer les offres en base de données connexion pas faite la variable <span>$offres</span> not set !</h2> 
<?php else: ?>
    <h2>Bienvenue sur la page des offres d'emploi. Ici, vous pouvez consulter les dernières opportunités de carrière disponibles.</h2>        
    <?php foreach ($offres as $offre): ?>
    <div class="offer-card">
        <h2 class="offer-title"><?= htmlspecialchars($offre->titre) ?></h2>
        <p class="company">
            <?= htmlspecialchars($offre->entreprise_nom) ?> • <?= htmlspecialchars($offre->localisation) ?> • <span class="salary"><?= htmlspecialchars($offre->salaire) ?></span>
        </p>
        <p class="snippet"><?= htmlspecialchars($offre->description) ?></p> 
        <a href="/offres/<?= $offre->id ?>" class="btn-offer">Voir l’offre</a>
    </div>
    <?php endforeach; ?>
    <?php if (empty($offres)): ?>
    <p>Aucune offre d'emploi disponible pour le moment. Revenez bientôt pour découvrir de nouvelles opportunités !</p>

    <?php endif; ?>

<?php endif; ?>