<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-0">Entreprises</h2>
                <p class="small text-muted mb-0">
                    Vue d’ensemble des entreprises partenaires et de leurs gestionnaires.
                </p>
            </div>
            <a href="/Eportail_Emploi/public/admin/entreprises/create" class="btn btn-primary btn-sm">
                + Créer une entreprise
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Secteur</th>
                        <th>Localisation</th>
                        <th>SIRET</th>
                        <th>Taille</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entreprises as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['nom']) ?></td>
                            <td><?= htmlspecialchars($e['secteur'] ?? '') ?></td>
                            <td>
                                <?= htmlspecialchars($e['ville'] ?? '') ?>
                                <?php if (!empty($e['pays'])): ?>
                                    (<?= htmlspecialchars($e['pays']) ?>)
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($e['siret'] ?? '') ?></td>
                            <td><?= htmlspecialchars($e['taille'] ?? '') ?></td>
                            <td class="text-end">
                                <a href="/Eportail_Emploi/public/admin/entreprises/edit?id=<?= (int)$e['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    Modifier
                                </a>
                                <form method="post"
                                      action="/Eportail_Emploi/public/admin/entreprises/delete"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer cette entreprise ?');">
                                    <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($entreprises)): ?>
                        <tr>
                            <td colspan="6" class="text-muted">
                                Aucune entreprise enregistrée.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>