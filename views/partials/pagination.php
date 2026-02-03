<?php
/**
 * Partial global : Pagination Bootstrap
 *
 * Variables attendues :
 * - $page  (int)  page courante
 * - $pages (int)  nombre total de pages
 * - $perPage (int) optionnel - éléments par page
 * - $_GET  pour conserver les filtres
 */

if (!isset($page) || !isset($pages) || $pages <= 1) {
    return;
}

$perPage = $perPage ?? ($_GET['perPage'] ?? 10);
?>

<nav aria-label="Pagination">
    <ul class="pagination justify-content-center">

        <!-- Précédent -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link bg-secondary text-white"
               href="<?= $page > 1 ? '?' . http_build_query(array_merge($_GET, ['page' => $page - 1, 'perPage' => $perPage])) : '#' ?>">
                &laquo;
            </a>
        </li>

        <!-- Pages numérotées -->
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link <?= $i != $page ? 'bg-secondary text-white' : '' ?>"
                   href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'perPage' => $perPage])) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <!-- Suivant -->
        <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
            <a class="page-link bg-secondary text-white"
               href="<?= $page < $pages ? '?' . http_build_query(array_merge($_GET, ['page' => $page + 1, 'perPage' => $perPage])) : '#' ?>">
                &raquo;
            </a>
        </li>

    </ul>
</nav>