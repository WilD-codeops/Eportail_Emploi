<?php 
use App\Core\Security;
use App\Core\Auth;

/** @var array $entreprises */
/** @var array $old */
/** @var string $error */
?>

<div class="card shadow-sm" style="border-radius: var(--radius-md);">
    <div class="card-body">

        <h1 class="h5 mb-3">Créer un utilisateur</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php 
            $csrfKey = "user_create";
            $csrfToken = Security::generateCsrfToken($csrfKey);
        ?>

        <form method="POST" action="/admin/users/create" class="row g-3">

            <input type="hidden" name="csrf_key" value="<?= $csrfKey ?>">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Prénom -->
            <div class="col-md-6">
                <label for="prenom" class="form-label">Prénom</label>
                <input 
                    type="text"
                    id="prenom"
                    name="prenom"
                    class="form-control"
                    value="<?= htmlspecialchars($old['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                    value="<?= htmlspecialchars($old['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                >
            </div>

            <!-- Mot de passe -->
            <div class="col-md-6">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>

                <div class="input-group">
                    <input 
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        class="form-control"
                        required
                    >
                    <button 
                        type="button"
                        class="btn btn-outline-secondary toggle-password"
                        data-target="mot_de_passe"
                        aria-label="Afficher ou masquer le mot de passe"
                    >
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <div class="small text-muted mt-1">
                    • 8 caractères minimum<br>
                    • 1 majuscule<br>
                    • 1 minuscule<br>
                    • 1 chiffre<br>
                    • 1 caractère spécial (@$!%*?&)
                </div>
            </div>

            <!-- Rôle -->
            <div class="col-md-6">
                <label for="role" class="form-label">Rôle</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Sélectionner</option>

                    <?php if (Auth::role() === 'admin'): ?>
                        <option value="admin">Admin</option>
                        <option value="gestionnaire">Gestionnaire</option>
                        <option value="recruteur">Recruteur</option>
                        <option value="candidat">Candidat</option>

                    <?php elseif (Auth::role() === 'gestionnaire'): ?>
                        <option value="recruteur">Recruteur</option>
                    <?php endif; ?>
                </select>
            </div>

            <<div class="col-md-6">
    <label for="entreprise_id" class="form-label">Entreprise</label>

    <?php if (Auth::role() === 'admin'): ?>
        <!-- Admin : entreprise seulement si rôle = gestionnaire ou recruteur -->
        <select id="entreprise_id" name="entreprise_id" class="form-select">
            <option value="">Aucune</option>
            <?php foreach ($entreprises as $e): ?>
                <option 
                    value="<?= (int)$e['id'] ?>"
                    <?= (isset($old['entreprise_id']) && (string)$old['entreprise_id'] === (string)$e['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

    <?php else: ?>
        <!-- Gestionnaire : entreprise imposée -->
        <input 
            type="text"
            class="form-control"
            value="<?= htmlspecialchars(Auth::entrepriseId(), ENT_QUOTES, 'UTF-8') ?>"
            disabled
        >
        <input type="hidden" name="entreprise_id" value="<?= Auth::entrepriseId() ?>">
    <?php endif; ?>
</div>

            <!-- Boutons -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <a href="/admin/users" class="btn btn-outline-secondary">Annuler</a>
                <button class="btn btn-primary">Créer</button>
            </div>

        </form>
    </div>
</div>

<script>
document.querySelectorAll(".toggle-password").forEach(btn => {
    btn.addEventListener("click", () => {
        const target = document.getElementById(btn.dataset.target);
        const isHidden = target.type === "password";
        target.type = isHidden ? "text" : "password";
        btn.querySelector("i").className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
});

const roleSelect = document.getElementById("role");
const entrepriseSelect = document.getElementById("entreprise_id");

function updateEntrepriseForRole() {
    if (!roleSelect || !entrepriseSelect) return;

    const role = roleSelect.value;

    // Pour admin :
    // - gestionnaire / recruteur => entreprise requise
    // - admin / candidat => entreprise désactivée + vidée
    if (role === "gestionnaire" || role === "recruteur") {
        entrepriseSelect.disabled = false;
    } else {
        entrepriseSelect.disabled = true;
        entrepriseSelect.value = "";
    }
}

if (roleSelect && entrepriseSelect) {
    roleSelect.addEventListener("change", updateEntrepriseForRole);
    updateEntrepriseForRole(); // init
}
</script>
