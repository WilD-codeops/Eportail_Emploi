<?php use App\Core\Security; use App\Core\Auth; ?>

<div class="card shadow-sm" style="border-radius: var(--radius-md);">
    <div class="card-body">

        <h1 class="h5 mb-3">Créer un utilisateur</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php
            $csrfKey = "user_create";
            $csrfToken = Security::generateCsrfToken($csrfKey);
        ?>

        <form method="POST" action="<?= Auth::role() === 'admin' ? '/admin/users/create' : '/dashboard/equipe/create' ?>" class="row g-3">

            <input type="hidden" name="csrf_key" value="<?= $csrfKey ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Prénom -->
            <div class="col-md-6">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text"
                       id="prenom"
                       name="prenom"
                       class="form-control"
                       value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                       required>
            </div>

            <!-- Nom -->
            <div class="col-md-6">
                <label for="nom" class="form-label">Nom</label>
                <input type="text"
                       id="nom"
                       name="nom"
                       class="form-control"
                       value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                       required>
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                       class="form-control"
                       required>
            </div>

            <!-- Téléphone -->
            <div class="col-md-6">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="text"
                       id="telephone"
                       name="telephone"
                       value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                       class="form-control"
                       required>
            </div>

            <!-- Mot de passe -->
            <div class="col-md-6">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input type="password"
                       id="mot_de_passe"
                       name="mot_de_passe"
                       class="form-control"
                       required>
            </div>

            <!-- Rôle -->
            <div class="col-md-6">
                <label for="role" class="form-label">Rôle</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Sélectionner</option>
                    <?php if (Auth::role() === 'admin'): ?>
                        <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="gestionnaire" <?= ($old['role'] ?? '') === 'gestionnaire' ? 'selected' : '' ?>>Gestionnaire</option>
                        <option value="recruteur" <?= ($old['role'] ?? '') === 'recruteur' ? 'selected' : '' ?>>Recruteur</option>
                    <?php elseif (Auth::role() === 'gestionnaire'): ?>
                        <option value="recruteur" <?= ($old['role'] ?? '') === 'recruteur' ? 'selected' : '' ?>>Recruteur</option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Entreprise -->
            <div class="col-md-6">
                <label for="entreprise_id" class="form-label">Entreprise</label>

                <?php if (Auth::role() === 'admin'): ?>
                <select id="entreprise_id" name="entreprise_id" class="form-select" required>
                    <option value="">Sélectionner</option>
                    <option value="null" <?= ($old['entreprise_id'] ?? '') === null ? 'selected' : '' ?>>Aucune</option>
                    <?php foreach ($entreprises as $e): ?>
                        <option value="<?= (int)$e['id'] ?>" <?= (isset($old['entreprise_id']) && $old['entreprise_id']==(int)$e['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php else: ?>
                    <input type="text"
                           class="form-control"
                           value="<?= htmlspecialchars(Auth::entrepriseId()) ?>"
                           disabled>
                    <input type="hidden" name="entreprise_id" value="<?= Auth::entrepriseId() ?>">
                <?php endif; ?>
            </div>

            <!-- Boutons -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <a href="/admin/users" class="btn btn-outline-secondary">Annuler</a>
                <button class="btn btn-primary">Créer l’utilisateur</button>
            </div>

        </form>
    </div>
</div>