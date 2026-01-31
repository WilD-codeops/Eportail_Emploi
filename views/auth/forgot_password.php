<?php
use App\Core\Security;
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$csrfToken = Security::generateCsrfToken('forgot_password');
?>
<div class="auth-form-wrapper mt-4">
  <h1 class="auth-page-title">Mot de passe oublié</h1>
  <p class="auth-page-subtitle">Entrez votre email pour recevoir un lien de réinitialisation.</p>

  <div id="form-errors" class="alert alert-danger <?= !empty($error) ? '' : 'd-none' ?>" aria-live="assertive">
    <?= !empty($error) ? $e($error) : '' ?>
  </div>

  <div class="alert alert-success <?= !empty($success) ? '' : 'd-none' ?>" aria-live="polite">
    <?= !empty($success) ? $e($success) : '' ?>
    <?php if (!empty($debug_link)): ?>
      <div class="mt-2 small">
        <strong>DEV :</strong> <a href="<?= $e($debug_link) ?>">Ouvrir le lien</a>
      </div>
    <?php endif; ?>
  </div>

  <form method="post" action="/password/forgot" class="auth-form mt-4" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $e($csrfToken) ?>">

    <div class="mb-3">
      <label for="email" class="form-label">Email *</label>
      <input id="email" type="email" name="email" class="form-control" required autocomplete="email">
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a class="btn btn-outline-secondary" href="/login">Retour</a>
      <button class="btn btn-primary" type="submit">Envoyer le lien</button>
    </div>
  </form>
</div>
