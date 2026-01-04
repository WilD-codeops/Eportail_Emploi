<?php
declare(strict_types=1);

namespace App\Modules\Offres;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Security;

class OffresController
{
    /** Fabrique le service Offres */
    private function makeService(): OffresService
    {
        $pdo       = Database::getConnection();
        $repo      = new OffresRepository($pdo);
        $validator = new OffresValidator();

        return new OffresService($repo, $validator);
    }

    /** Fabrique le repository (pour ownership) */
    private function makeRepository(): OffresRepository
    {
        $pdo = Database::getConnection();
        return new OffresRepository($pdo);
    }

    /** Vue publique */
    private function renderPublic(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require __DIR__ . "/../../../views/offres/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }

    /** Vue dashboard */
    private function renderDashboard(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require __DIR__ . "/../../../views/offres/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    /**
 * Rend un partial (HTML) sans layout.
 * Utilisé par AJAX : on renvoie uniquement un fragment HTML.
 */
    private function renderPartial(string $view, array $params = []): void
    {
        extract($params);
        require __DIR__ . "/../../../views/offres/{$view}.php";
    }


    /** Liste publique avec filtres */
    public function index(): void
    {
        $filters = [
            'keyword'         => isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : null,
            'localisation_id' => isset($_GET['localisation_id']) ? (int)$_GET['localisation_id'] : null,
            'type_offre_id'   => isset($_GET['type_offre_id']) ? (int)$_GET['type_offre_id'] : null,
        ];

        $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        $service = $this->makeService();
        $result  = $service->listPublic($filters, $page, $perPage);
        $refs    = $service->getReferenceData(false)['data'] ?? [];

        $this->renderPublic("public_list", [
            "title" => "Offres d'emploi",
            "data"  => $result['data'] ?? [],
            "refs"  => $refs,
        ]);
    }

    /** Détail public */
    public function show(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(404);
            die("404");
        }

        $service = $this->makeService();
        $result  = $service->showPublic($id);

        if (!$result['success']) {
            http_response_code(404);
            die("404");
        }

        $this->renderPublic("show", [
            "title" => $result['data']['offre']['titre'] ?? "Offre",
            "offre" => $result['data']['offre'],
        ]);
    }

    /** Liste admin */
    public function adminIndex(): void
    {
        Auth::requireRole(['admin']);

        $filters = [
            'keyword' => isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : null,
            'statut'  => isset($_GET['statut']) ? trim((string)$_GET['statut']) : null,
            'type_offre_id' => isset($_GET['type_offre_id']) ? (int)$_GET['type_offre_id'] : null,
        ];

        // Sécurise la pagination : pas de page 0 / pas de perPage énorme
        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? min(50, max(1, (int)$_GET['perPage'])) : 10;


        $service = $this->makeService();
        $result  = $service->listAdminPaginated($filters, $page, $perPage);
        $refs    = $service->getReferenceData(true)['data'] ?? [];

        $this->renderDashboard("list", [
            "title" => "Gestion des offres",
            "mode"  => "admin",
            "items" => $result['data']['items'] ?? [],
            "pagination" => $result['data']['pagination'] ?? [],
            "filters" => $filters,
            "refs" => $refs,
        ]);
    }


    /** Liste gestionnaire/recruteur */
    public function manageIndex(): void
    {
        Auth::requireRole(['gestionnaire', 'recruteur']);

        $entrepriseId = Auth::entrepriseId();
        if (!$entrepriseId) {
            Security::forbidden();
        }

        $filters = [
            'keyword' => isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : null,
            'statut'  => isset($_GET['statut']) ? trim((string)$_GET['statut']) : null,
            'type_offre_id' => isset($_GET['type_offre_id']) ? (int)$_GET['type_offre_id'] : null,
        ];

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? min(50, max(1, (int)$_GET['perPage'])) : 10;

        $service = $this->makeService();
        $result  = $service->listEntreprisePaginated($entrepriseId, $filters, $page, $perPage);
        $refs    = $service->getReferenceData(false)['data'] ?? [];

        $this->renderDashboard("list", [
            "title" => "Mes offres",
            "mode"  => "entreprise",
            "items" => $result['data']['items'] ?? [],
            "pagination" => $result['data']['pagination'] ?? [],
            "filters" => $filters,
            "refs" => $refs,
        ]);
    }

       /**
    * AJAX (ADMIN) : renvoie uniquement table + pagination (HTML)
    * URL: GET /admin/offres/partial?... (keyword/statut/type/page/perPage)
    */
    public function adminPartial(): void
    {
        Auth::requireRole(['admin']);

        $filters = [
            'keyword'       => isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : null,
            'statut'        => isset($_GET['statut']) ? trim((string)$_GET['statut']) : null,
            'type_offre_id' => isset($_GET['type_offre_id']) ? (int)$_GET['type_offre_id'] : null,
        ];

        $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        $service = $this->makeService();
        $result  = $service->listAdminPaginated($filters, $page, $perPage);

        $this->renderPartial("_results", [
            "mode"       => "admin",
            "items"      => $result['data']['items'] ?? [],
            "pagination" => $result['data']['pagination'] ?? [],
            "filters"    => $filters,
            "refs"       => $service->getReferenceData(true)['data'] ?? [],
        ]);
    }

    /**
     * AJAX (ENTREPRISE) : renvoie uniquement table + pagination (HTML)
     * URL: GET /dashboard/offres/partial?...
     */
    public function managePartial(): void
    {
        Auth::requireRole(['gestionnaire', 'recruteur']);

        $entrepriseId = Auth::entrepriseId();
        if (!$entrepriseId) {
            Security::forbidden();
        }

        $filters = [
            'keyword'       => isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : null,
            'statut'        => isset($_GET['statut']) ? trim((string)$_GET['statut']) : null,
            'type_offre_id' => isset($_GET['type_offre_id']) ? (int)$_GET['type_offre_id'] : null,
        ];

        $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 10;

        $service = $this->makeService();
        $result  = $service->listEntreprisePaginated($entrepriseId, $filters, $page, $perPage);

        $this->renderPartial("_results", [
            "mode"       => "entreprise",
            "items"      => $result['data']['items'] ?? [],
            "pagination" => $result['data']['pagination'] ?? [],
            "filters"    => $filters,
            "refs"       => $service->getReferenceData(false)['data'] ?? [],
        ]);
    }


    /** Formulaire création */
    public function createForm(): void
    {
        Auth::requireLogin();
        $isAdmin = Auth::role() === 'admin';
        if (!$isAdmin) {
            Auth::requireRole(['gestionnaire', 'recruteur']);
        }

        $service = $this->makeService();
        $refs    = $service->getReferenceData($isAdmin)['data'] ?? [];

        $csrf = Security::generateCsrfToken('offres_create');

        $this->renderDashboard("create", [
            "title" => "Créer une offre",
            "refs"  => $refs,
            "csrf"  => $csrf,
            "isAdmin" => $isAdmin
        ]);
    }

    /** Création (POST) */
    public function create(): void
    {
        Auth::requireLogin();
        $isAdmin = Auth::role() === 'admin';
        if (!$isAdmin) {
            Auth::requireRole(['gestionnaire', 'recruteur']);
        }

        Security::requireCsrfToken('offres_create', $_POST['csrf_token'] ?? null);

        $service            = $this->makeService();
        $auteurId           = Auth::userId() ?? 0;
        $entrepriseIdCtx    = $isAdmin ? 0 : (Auth::entrepriseId() ?? 0);
        $result             = $service->createOffre($_POST, $auteurId, $isAdmin, $entrepriseIdCtx);

        if (!$result['success']) {
            $refs = $service->getReferenceData($isAdmin)['data'] ?? [];
            $csrf = Security::generateCsrfToken('offres_create');

            $this->renderDashboard("create", [
                "title"  => "Créer une offre",
                "refs"   => $refs,
                "errors" => $result['errors'] ?? [],
                "input"  => $result['data']['input'] ?? [],
                "csrf"   => $csrf,
                "isAdmin" => $isAdmin
            ]);
            return;
        }

        $redirect = $isAdmin ? "/admin/offres?success=1" : "/dashboard/offres?success=1";
        header("Location: {$redirect}");
        exit;
    }

    /** Formulaire édition */
    public function editForm(): void
    {
        Auth::requireLogin();
        $isAdmin = Auth::role() === 'admin';
        if (!$isAdmin) {
            Auth::requireRole(['gestionnaire', 'recruteur']);
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $repo  = $this->makeRepository();
        $offre = $repo->find($id);
        if (!$offre) {
            http_response_code(404);
            die("404");
        }

        if (!$isAdmin) {
            $entrepriseId = Auth::entrepriseId();
            if (!$entrepriseId || $offre['entreprise_id'] !== $entrepriseId) {
                Security::forbidden();
            }
        }

        $service = $this->makeService();
        $refs    = $service->getReferenceData($isAdmin)['data'] ?? [];
        $csrf    = Security::generateCsrfToken('offres_update');

        $this->renderDashboard("edit", [
            "title" => "Modifier une offre",
            "refs"  => $refs,
            "offre" => $offre,
            "csrf"  => $csrf,
            "isAdmin" => $isAdmin
        ]);
    }

    /** Mise à jour (POST) */
    public function update(): void
    {
        Auth::requireLogin();
        $isAdmin = Auth::role() === 'admin';
        if (!$isAdmin) {
            Auth::requireRole(['gestionnaire', 'recruteur']);
        }

        Security::requireCsrfToken('offres_update', $_POST['csrf_token'] ?? null);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $repo  = $this->makeRepository();
        $offre = $repo->find($id);
        if (!$offre) {
            http_response_code(404);
            die("404");
        }

        if (!$isAdmin) {
            $entrepriseId = Auth::entrepriseId();
            if (!$entrepriseId || $offre['entreprise_id'] !== $entrepriseId) {
                Security::forbidden();
            }
        }

        $service         = $this->makeService();
        $auteurId        = Auth::userId() ?? 0;
        $entrepriseIdCtx = $isAdmin ? 0 : (Auth::entrepriseId() ?? 0);
        $result          = $service->updateOffre($id, $_POST, $auteurId, $isAdmin, $entrepriseIdCtx);

        if (!$result['success']) {
            $refs = $service->getReferenceData($isAdmin)['data'] ?? [];
            $csrf = Security::generateCsrfToken('offres_update');

            $this->renderDashboard("edit", [
                "title"  => "Modifier une offre",
                "refs"   => $refs,
                "offre"  => $offre,
                "errors" => $result['errors'] ?? [],
                "input"  => $result['data']['input'] ?? [],
                "csrf"   => $csrf,
                "isAdmin" => $isAdmin
            ]);
            return;
        }

        $redirect = $isAdmin ? "/admin/offres?success=1" : "/dashboard/offres?success=1";
        header("Location: {$redirect}");
        exit;
    }

    /** Suppression */
    public function delete(): void
    {
        Auth::requireLogin();
        $isAdmin = Auth::role() === 'admin';
        if (!$isAdmin) {
            Auth::requireRole(['gestionnaire', 'recruteur']);
        }

        $csrfKey = $_POST['csrf_key'] ?? '';
        Security::requireCsrfToken($csrfKey, $_POST['csrf_token'] ?? null);


        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $repo  = $this->makeRepository();
        $offre = $repo->find($id);
        if (!$offre) {
            http_response_code(404);
            die("404");
        }

        if (!$isAdmin) {
            $entrepriseId = Auth::entrepriseId();
            if (!$entrepriseId || $offre['entreprise_id'] !== $entrepriseId) {
                Security::forbidden();
            }
        }

        $entrepriseIdCtx = $isAdmin ? 0 : (Auth::entrepriseId() ?? 0);
        $service         = $this->makeService();
        $result          = $service->deleteOffre($id, $isAdmin, $entrepriseIdCtx);
        if (!$result['success']) {
            Security::forbidden();
        }

        $redirect = $isAdmin ? "/admin/offres?success=1" : "/dashboard/offres?success=1";
        header("Location: {$redirect}");
        exit;
    }
}

// Routes :
// GET  /offres                 -> index
// GET  /offres/show?id=ID      -> show
// GET  /admin/offres           -> adminIndex
// GET  /admin/offres/create    -> createForm
// POST /admin/offres/create    -> create
// GET  /admin/offres/edit?id=ID-> editForm
// POST /admin/offres/edit?id=ID-> update
// POST /admin/offres/delete?id=ID -> delete
// GET  /dashboard/offres          -> manageIndex
// GET  /dashboard/offres/create   -> createForm
// POST /dashboard/offres/create   -> create
// GET  /dashboard/offres/edit?id=ID -> editForm
// POST /dashboard/offres/edit?id=ID -> update
// POST /dashboard/offres/delete?id=ID -> delete
