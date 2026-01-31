<?php
use App\Core\Security;
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$csrfToken = Security::generateCsrfToken('reset_password');
$token = $token ?? '';
?>
<div class="auth-form-wrapper mt-4">
  <h1 class="auth-page-title">Réinitialiser le mot de passe</h1>
  <p class="auth-page-subtitle">Choisissez un nouveau mot de passe.</p>

  <div id="form-errors" class="alert alert-danger <?= !empty($error) ? '' : 'd-none' ?>" aria-live="assertive">
    <?= !empty($error) ? $e($error) : '' ?>
  </div>

  <form method="post" action="/password/reset" class="auth-form mt-4" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $e($csrfToken) ?>">
    <input type="hidden" name="token" value="<?= $e($token) ?>">

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Nouveau mot de passe *</label>
        <div class="input-group">
          <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
          <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password" aria-label="Afficher le mot de passe">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <div class="col-md-6 mb-3">
        <label for="password_confirm" class="form-label">Confirmation *</label>
        <div class="input-group">
          <input id="password_confirm" type="password" name="password_confirm" class="form-control" required autocomplete="new-password">
          <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirm" aria-label="Afficher la confirmation">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="form-check mb-3">
      <input type="checkbox" class="form-check-input" id="cgu" name="cgu" required>
      <label for="cgu" class="form-check-label">Je confirme vouloir modifier mon mot de passe</label>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a class="btn btn-outline-secondary" href="/login">Retour</a>
      <button class="btn btn-success" type="submit">Mettre à jour</button>
    </div>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".toggle-password").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.target;
      const input = document.getElementById(id);
      if (!input) return;
      const hidden = input.type === "password";
      input.type = hidden ? "text" : "password";
      const icon = btn.querySelector("i");
      if (icon) icon.className = hidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
  });
});
</script>
