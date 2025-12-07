<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-3">Modifier l’entreprise</h2>

        <form method="post" action="/admin/entreprises/edit">
            <input type="hidden" name="id" value="<?= (int)$entreprise['id'] ?>">

            <h3 class="h6 text-uppercase text-muted mt-3 mb-2">Informations entreprise</h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom de l’entreprise</label>
                    <input type="text" name="nom_entreprise" class="form-control"
                           value="<?= htmlspecialchars($entreprise['nom']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Secteur</label>
                    <select name="secteur_id" class="form-select" required>
                        <option value="">Sélectionner un secteur</option>
                        <?php foreach ($secteurs as $s): ?>
                            <option value="<?= (int)$s['id'] ?>"
                                <?= (int)$s['id'] === (int)$entreprise['secteur_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control"
                           value="<?= htmlspecialchars($entreprise['adresse'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Code postal</label>
                    <input type="text" name="code_postal" class="form-control"
                           value="<?= htmlspecialchars($entreprise['code_postal'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Ville</label>
                    <input type="text" name="ville" class="form-control"
                           value="<?= htmlspecialchars($entreprise['ville'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Pays</label>
                    <input type="text" name="pays" class="form-control"
                           value="<?= htmlspecialchars($entreprise['pays'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control"
                           value="<?= htmlspecialchars($entreprise['telephone'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email entreprise</label>
                    <input type="email" name="email_entreprise" class="form-control"
                           value="<?= htmlspecialchars($entreprise['email'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">SIRET</label>
                    <input type="text" name="siret" class="form-control"
                           value="<?= htmlspecialchars($entreprise['siret'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Site web</label>
                    <input type="url" name="site_web" class="form-control"
                           value="<?= htmlspecialchars($entreprise['site_web'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Taille</label>
                    <input type="text" name="taille" class="form-control"
                           value="<?= htmlspecialchars($entreprise['taille'] ?? '') ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($entreprise['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="/admin/entreprises" class="btn btn-outline-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>