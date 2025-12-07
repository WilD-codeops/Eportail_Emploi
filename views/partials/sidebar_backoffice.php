<?php
use App\Core\Auth;

// Rôle courant
$role = Auth::role() ?? 'candidat';

// Charge les menus
$menus = require __DIR__ . '/../../config/menus.php';
$items = $menus[$role] ?? [];

// URL courante pour mettre la classe "active"
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>

<nav class="sidebar bg-dark text-white d-flex flex-column">
    <div class="sidebar-brand px-3 py-3 border-bottom">
        <span class="fw-bold">Eportail<span class="text-primary">Emploi</span></span>
        <div class="small opacity-75"><?= htmlspecialchars(ucfirst($role)) ?> panel</div>
    </div>

    <ul class="nav nav-pills flex-column mb-auto mt-2">
        <?php foreach ($items as $item): 
            $isActive = $currentPath === $item['url'];
        ?>
            <li class="nav-item">
                <a 
                    href="<?= htmlspecialchars($item['url']) ?>" 
                    class="nav-link d-flex align-items-center px-3 py-2 <?= $isActive ? 'active text-white' : 'text-white-50' ?>"
                >
                    <i class="bi <?= htmlspecialchars($item['icon']) ?> me-2"></i>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="mt-auto px-3 py-3 border-top small">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-secondary me-2" style="width:32px;height:32px;"></div>
            <div>
                <div class="fw-semibold">Mon compte</div>
                <div class="text-white-50"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <a href="/logout" class="d-block mt-2 text-decoration-none text-white-50">
            <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
        </a>
    </div>
</nav>