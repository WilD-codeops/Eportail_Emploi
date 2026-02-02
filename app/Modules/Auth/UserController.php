<?php

namespace App\Modules\Auth;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Security;
use App\Modules\Auth\UserRepository;
use App\Modules\Auth\UserService;

class UserController
{
    /* ============================================================
       FACTORY : création du UserService avec injection du repo
       ============================================================ */
    private function makeUserService(): UserService
    {
        $pdo  = Database::getConnection();
        $repo = new UserRepository($pdo);

        return new UserService($repo);
    }

    /* ============================================================
       RENDERERS : layout dashboard ou main selon contexte
       ============================================================ */
    private function renderDashboard(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require __DIR__ . "/../../../views/users/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    private function renderMain(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require __DIR__ . "/../../../views/users/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . "/../../../views/layouts/main.php";
    }

    /* ============================================================
       ADMIN : LISTE DES UTILISATEURS
       Route : /admin/users
       ============================================================ */
    public function adminIndex(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin']);

        $filters = [
            'nom'           => $_GET['nom'] ?? null,
            'email'         => $_GET['email'] ?? null,
            'role'          => $_GET['role'] ?? null,
            'entreprise_id' => $_GET['entreprise_id'] ?? null,
            'dernier_acces' => $_GET['dernier_acces'] ?? null,
        ];

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;

        $service = $this->makeUserService();
        $result  = $service->search($filters, $limit, $offset);

        if (!$result['success']) {
            AuthController::VerifyFailSystem($result);
        }

        $total = $result['total'];
        $pages = (int)ceil($total / $limit);

        /* KPIs dynamiques */
        $kpiTotalUsers = $service->countUsers();
        $kpiAdmins     = $service->countFiltered(['role' => 'admin']);
        $kpiGest       = $service->countFiltered(['role' => 'gestionnaire']);
        $kpiRecr       = $service->countFiltered(['role' => 'recruteur']);
        $kpiCand       = $service->countFiltered(['role' => 'candidat']);

        $this->renderDashboard("admin_index", [
            "title" => "Gestion des utilisateurs",
            "users" => $result['data'],
            "filters" => $filters,
            "page" => $page,
            "pages" => $pages,
            "kpi" => [
                "total" => $kpiTotalUsers['total'] ?? 0,
                "admins" => $kpiAdmins['total'] ?? 0,
                "gestionnaires" => $kpiGest['total'] ?? 0,
                "recruteurs" => $kpiRecr['total'] ?? 0,
                "candidats" => $kpiCand['total'] ?? 0,
            ]
        ]);
    }

    /* ============================================================
       GESTIONNAIRE : LISTE DES MEMBRES DE SON ENTREPRISE
       Route : /dashboard/equipe
       ============================================================ */
    public function gestionnaireIndex(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['gestionnaire']);

        $filters = [
            'nom'           => $_GET['nom'] ?? null,
            'email'         => $_GET['email'] ?? null,
            'role'          => $_GET['role'] ?? null,
            'entreprise_id' => Auth::entrepriseId(), // verrouillage automatique
        ];

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;

        $service = $this->makeUserService();
        $result  = $service->search($filters, $limit, $offset);

        if (!$result['success']) {
            AuthController::VerifyFailSystem($result);
        }

        $total = $result['total'];
        $pages = (int)ceil($total / $limit);

        /* KPIs dynamiques entreprise */
        $kpiTotal = $service->countFiltered(['entreprise_id' => Auth::entrepriseId()]);
        $kpiGest  = $service->countFiltered(['entreprise_id' => Auth::entrepriseId(), 'role' => 'gestionnaire']);
        $kpiRecr  = $service->countFiltered(['entreprise_id' => Auth::entrepriseId(), 'role' => 'recruteur']);

        $this->renderDashboard("gestionnaire_index", [
            "title" => "Mon équipe",
            "users" => $result['data'],
            "filters" => $filters,
            "page" => $page,
            "pages" => $pages,
            "kpi" => [
                "total" => $kpiTotal['total'] ?? 0,
                "gestionnaires" => $kpiGest['total'] ?? 0,
                "recruteurs" => $kpiRecr['total'] ?? 0,
            ]
        ]);
    }

    /* ============================================================
       FORMULAIRE DE CREATION
       ============================================================ */
    public function createForm(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin', 'gestionnaire']);

        $this->renderDashboard("create", [
            "title" => "Créer un utilisateur"
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin', 'gestionnaire']);
        Security::requireCsrfToken('user_create', $_POST['csrf_token'] ?? null);

        $service = $this->makeUserService();
        $result  = $service->createUser($_POST);

        if (!$result['success']) {
            AuthController::VerifyFailSystem($result);

            $this->renderDashboard("create", [
                "title" => "Créer un utilisateur",
                "error" => $result['error'],
                "old"   => $_POST
            ]);
            return;
        }

        AuthController::flashSuccess("Utilisateur créé avec succès.");
        header("Location: /admin/users");
        exit;
    }

    /* ============================================================
       EDITION
       ============================================================ */
    public function editForm(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin', 'gestionnaire']);

        $id = (int)($_GET['id'] ?? 0);

        $service = $this->makeUserService();
        $user    = $service->getUser($id);

        if (!$user['success']) {
            AuthController::VerifyFailSystem($user);
        }

        $this->renderDashboard("edit", [
            "title" => "Modifier un utilisateur",
            "user"  => $user['data']
        ]);
    }

    public function update(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin', 'gestionnaire']);
        Security::requireCsrfToken('user_edit', $_POST['csrf_token'] ?? null);

        $id      = (int)($_POST['id'] ?? 0);
        $service = $this->makeUserService();
        $result  = $service->updateUser($id, $_POST);

        if (!$result['success']) {
            AuthController::VerifyFailSystem($result);

            $this->renderDashboard("edit", [
                "title" => "Modifier un utilisateur",
                "user"  => $_POST,
                "error" => $result['error']
            ]);
            return;
        }

        AuthController::flashSuccess("Utilisateur mis à jour avec succès.");
        header("Location: /admin/users");
        exit;
    }

    /* ============================================================
       SUPPRESSION
       ============================================================ */
    public function delete(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin', 'gestionnaire']);
        Security::requireCsrfToken('user_delete', $_POST['csrf_token'] ?? null);

        $id      = (int)($_POST['id'] ?? 0);
        $service = $this->makeUserService();
        $result  = $service->deleteUser($id);

        if (!$result['success']) {
            AuthController::VerifyFailSystem($result);
            AuthController::flashError($result['error']);
            header("Location: /admin/users");
            exit;
        }

        AuthController::flashSuccess("Utilisateur supprimé avec succès.");
        header("Location: /admin/users");
        exit;
    }

    /* ============================================================
       PROFIL (recruteur / candidat)
       ============================================================ */
    public function profil(): void
    {
        Auth::requireLogin();

        $id = Auth::userId();

        $service = $this->makeUserService();
        $user    = $service->getUser($id);

        if (!$user['success']) {
            AuthController::VerifyFailSystem($user);
        }

        $this->renderMain("profil", [
            "title" => "Mon profil",
            "user"  => $user['data']
        ]);
    }
}