<?php

use App\Core\Security;
use App\Core\Auth;

/** @var array $user */

?>

<div class="card shadow-sm" style="border-radius: var(--radius-md);">
    <div class="card-body">

        <h1 class="h5 mb-3">Modifier un utilisateur</h1>
        <p class="text-muted small mb-4">
            Mettez à jour les informations de l’utilisateur. Le mot de passe est optionnel.
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php
            $csrfKey   = "user_edit";
            $csrfToken = Security::generateCsrfToken($csrfKey);
        ?>

        <form method="POST" action="/admin/users/update" class="row g-3" id="user-edit-form">

            <input type="hidden" name="csrf_key" value="<?= $csrfKey ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="id" value="<?= (int)($user['id'] ?? 0) ?>">

            <!-- Prénom -->
            <div class="col-md-6">
                <label for="prenom" class="form-label">Prénom</label>
                <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    class="form-control"
                    value="<?= htmlspecialchars($user['prenom'] ?? '') ?>"
                    required
                >
            </div>

            <!-- Nom -->
            <div class="col-md-6">
                <label for="nom" class="form-label">Nom</label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    class="form-control"
                    value="<?= htmlspecialchars($user['nom'] ?? '') ?>"
                    required
                >
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                    required
                >
            </div>

            <!-- Téléphone -->
            <div class="col-md-6">
                <label for="telephone" class="form-label">Téléphone</label>
                <input
                    type="text"
                    id="telephone"
                    name="telephone"
                    class="form-control"
                    value="<?= htmlspecialchars($user['telephone'] ?? '') ?>"
                    required
                >
            </div>

            <!-- Mot de passe (optionnel) -->
            <div class="col-md-6">
                <label for="mot_de_passe" class="form-label">
                    Nouveau mot de passe <span class="text-muted small">(laisser vide pour ne pas changer)</span>
                </label>

                <div class="input-group">
                    <input
                        type="mot_de_passe"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        class="form-control"
                        autocomplete="new-password"
                    >
                    <button
                        type="button"
                        class="btn btn-outline-secondary toggle-password"
                        data-target="password"
                        aria-label="Afficher ou masquer le mot de passe"
                    >
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <!-- Barre de force -->
                <div class="password-strength mt-2">
                    <div class="strength-fill strength-0"></div>
                </div>

                <!-- Checklist -->
                <ul class="list-unstyled small mt-2">
                    <li class="check-item"><i class="bi bi-dot"></i> 8 caractères minimum</li>
                    <li class="check-item"><i class="bi bi-dot"></i> 1 majuscule</li>
                    <li class="check-item"><i class="bi bi-dot"></i> 1 chiffre</li>
                    <li class="check-item"><i class="bi bi-dot"></i> 1 caractère spécial (@$!%*?&)</li>
                </ul>
            </div>

            <!-- Rôle -->
            <div class="col-md-6">
                <label for="role" class="form-label">Rôle</label>
                <select id="role" name="role" class="form-select" required>
                    <?php
                        $currentRole = $user['role'] ?? '';
                        $connectedRole = Auth::role();
                    ?>

                    <?php if ($connectedRole === 'admin'): ?>
                        <option value="admin"        <?= $currentRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="gestionnaire" <?= $currentRole === 'gestionnaire' ? 'selected' : '' ?>>Gestionnaire</option>
                        <option value="recruteur"    <?= $currentRole === 'recruteur' ? 'selected' : '' ?>>Recruteur</option>
                        <option value="candidat"     <?= $currentRole === 'candidat' ? 'selected' : '' ?>>Candidat</option>

                    <?php elseif ($connectedRole === 'gestionnaire'): ?>
                        <!-- Gestionnaire ne peut attribuer que recruteur/gestionnaire (le service revalide de toute façon) -->
                        <option value="gestionnaire" <?= $currentRole === 'gestionnaire' ? 'selected' : '' ?>>Gestionnaire</option>
                        <option value="recruteur"    <?= $currentRole === 'recruteur' ? 'selected' : '' ?>>Recruteur</option>
                    <?php endif; ?>
                </select>
            </div>

                        <!-- Entreprise -->
            <?php if (Auth::role() === 'admin'): ?>
            <div class="col-md-6">
                <label for="entreprise_id" class="form-label">Entreprise</label>
                <select id="entreprise_id" name="entreprise_id" class="form-select" >
                    <option value="">Sélectionner</option>
                    <?php foreach ($entreprises as $e): ?>
                        <option 
                            value="<?= (int)$e['id'] ?>"
                            <?= (isset($user['entreprise_id']) && (string)$user['entreprise_id'] === (string)$e['id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
                    
                        <!-- Boutons -->
                        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                            <a href="/admin/users" class="btn btn-outline-secondary">Annuler</a>
                            <button class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    
                    </form>
                </div>
            </div>
                    
            <script>
document.addEventListener("DOMContentLoaded", () => {
    const passwordField = document.getElementById("mot_de_passe");
    const form = document.getElementById("user-edit-form");

    function updatePasswordStrength() {
        if (!passwordField) return;
        
        const pass = passwordField.value;
        const checks = document.querySelectorAll(".check-item");
        const fill = document.querySelector(".strength-fill");

        if (!checks.length || !fill) return;

        let score = 0;
        // 8 caractères
        if (pass.length >= 8) { checks[0].classList.add("valid"); score++; }
        else checks[0].classList.remove("valid");
        // Majuscule
        if (/[A-Z]/.test(pass)) { checks[1].classList.add("valid"); score++; }
        else checks[1].classList.remove("valid");
        // Chiffre
        if (/\d/.test(pass)) { checks[2].classList.add("valid"); score++; }
        else checks[2].classList.remove("valid");
        // Spécial
        if (/[@$!%*?&]/.test(pass)) { checks[3].classList.add("valid"); score++; }
        else checks[3].classList.remove("valid");

        fill.className = `strength-fill strength-${score}`;
    }

    // Toggle password visibility
    document.querySelectorAll(".toggle-password").forEach(btn => {
        btn.addEventListener("click", () => {
            const targetId = btn.dataset.target;  // "mot_de_passe"
            const input = document.getElementById(targetId);
            if (!input) return;

            const isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";

            const icon = btn.querySelector("i");
            icon.className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
        });
    });

    // Strength meter
    if (passwordField) {
        passwordField.addEventListener("input", updatePasswordStrength);
    }
});
</script>
