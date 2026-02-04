<?php

namespace App\Modules\Entreprise;

use App\Core\Database;
use App\Core\Auth;
use App\Modules\Auth\AuthRepository;
use App\Modules\Auth\AuthService;
use App\Modules\Auth\AuthRegistrationService;
use App\Core\Security;
use App\modules\Offres\OffresService;

/**
 * Contrôleur Entreprise (partie administrateur)
 * Gère l'affichage des vues et la transmission des actions
 * au service métier (EntrepriseService).
 */
class EntrepriseController
{
    /**
     * Fabrique les instances des services métier Entreprise, AuthRegistrationService et Auth.
     * Permet d'éviter la création manuelle des dépendances partout dans chacune des fonctions.
     */

    private function makeAuthService(): AuthService
    {
        $pdo  = Database::getConnection();
        $repo = new AuthRepository($pdo);

        return new AuthService($repo, $pdo);
    }

    private function makeEntrepriseService(): EntrepriseService
    {
        $pdo  = Database::getConnection();
        $auth = $this->makeAuthService();
        $repo = new EntrepriseRepository($pdo);

        return new EntrepriseService($repo, $auth, $pdo);
    }

    private function makeAuthRegistrationService(): AuthRegistrationService
    {
        $authService = $this->makeAuthService();
        $entrepriseService = $this->makeEntrepriseService();
        $authRepository = new AuthRepository(Database::getConnection());

        return new AuthRegistrationService($authService, $entrepriseService, $authRepository);
    }

    private function makeOffresService(): OffresService
    {
        $pdo  = Database::getConnection();
        $repo = new \App\Modules\Offres\OffresRepository($pdo);
        $validator = new \App\Modules\Offres\OffresValidator();

        return new OffresService($repo, $validator);
    }

    /**
     * MES RENDERERS PERSONNALISÉS
     */
    
    // Charge une vue publique (layout main)
    private function renderPublic(string $view, array $params = []): void
    {
        extract($params);
        
        ob_start();
        require __DIR__ . "/../../../views/entreprise/{$view}.php";
        $content = ob_get_clean();
        
        require __DIR__ . "/../../../views/layouts/main.php";
    }       
    
    // Charge une vue du tableau de bord (layout dashboard) 
    private function renderDashboard(string $view, array $params = []): void
    {
        extract($params);
        
        ob_start();
        require __DIR__ . "/../../../views/entreprise/{$view}.php";
        $content = ob_get_clean();
        
        require __DIR__ . "/../../../views/layouts/dashboard.php";
    }

    /**
     * MES FONCTIONS CONTROLEUR
     */
    public function Index(): void
        { 

        $filters = [ // Récupération des filtres de recherche
        'nom'          => $_GET['nom'] ?? null,
        'secteur'      => $_GET['secteur'] ?? null,
        'ville'        => $_GET['ville'] ?? null,
        'gestionnaire' => $_GET['gestionnaire'] ?? null,
        'tri'          => $_GET['tri'] ?? null
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = (int)($_GET['perPage'] ?? 10);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }
        $limit = $perPage;
        $offset = ($page - 1) * $limit;

        $service     = $this->makeEntrepriseService();
        $entreprises = $service->searchEntreprises($filters, $limit, $offset);
        
        if (!$entreprises['success']) {
            self::VerifyFailSystem($entreprises);
        }

        $total= $entreprises['total'];
        
        
        $pages = (int)ceil($total / $limit);

        //Données pour les filtres
        $secteurs = $service->listSecteurs();
        if (!$secteurs['success']) {
            self::VerifyFailSystem($secteurs);
        }
        
         // vue dashboard avec layout dashboard

        $this->renderPublic("public_list", [
            "titre"       => "Nos entreprises partenaires",
            "entreprises" => $entreprises['data'],
            "secteurs"    => $secteurs['data'],
            "page"        => $page,
            "pages"       => $pages,
            "perPage"     => $perPage
        ]);
    }
    
    
    public function adminIndex(): void

    {
        Auth::requireLogin();
        Auth::requireRole(['admin']); // Seul admin peut accéder à cette page

        $filters = [ // Récupération des filtres de recherche
        'nom'          => $_GET['nom'] ?? null,
        'secteur'      => $_GET['secteur'] ?? null,
        'ville'        => $_GET['ville'] ?? null,
        'gestionnaire' => $_GET['gestionnaire'] ?? null,
        'tri'          => $_GET['tri'] ?? null
        ];

        // Pagination
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = (int)($_GET['perPage'] ?? 10);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }
        $limit = $perPage;
        $offset = ($page - 1) * $limit;

        $service     = $this->makeEntrepriseService();
        $entreprises = $service->searchEntreprises($filters, $limit, $offset);

        
        if (!$entreprises['success']) {
            self::VerifyFailSystem($entreprises);
        }

        $total= $entreprises['total'];
        
        
        $pages = (int)ceil($total / $limit);

        //Données pour les filtres
        $secteurs = $service->listSecteurs();
        if (!$secteurs['success']) {
            self::VerifyFailSystem($secteurs);
        }
        
         // vue dashboard avec layout dashboard

        $this->renderDashboard("list", [
            "rubrique"    => "Gestion entreprise",
            "title"       => "Liste des entreprises",
            "entreprises" => $entreprises['data'],
            "secteurs"    => $secteurs['data'],
            "page"        => $page,
            "pages"       => $pages,
            "perPage"     => $perPage,
            "kpi"         => [
                "total" => $total,
                "sectorsCount" => count($secteurs['data'] ?? [])
            ]
        ]);
    }
    
    public function show(): void
    {
        
        $id = (int)($_GET['id'] ?? 0);
        
        $service= $this->makeEntrepriseService();

        $entreprise = $service->findEntreprise($id);
        if (!$entreprise['success']) {
            self::VerifyFailSystem($entreprise);

        }
        if (empty($entreprise['data'])) {
        $this->flashError("Cette entreprise est inexistante.");
        header("Location: /entreprises");
        exit;
        }

    
        
        $offres = $service->listOffresByEntreprise($id);
        if (!$offres['success']) {
            self::VerifyFailSystem($offres);
        }
        
        $this->renderPublic("show", [
            "title"       => "Détails de l'entreprise",
            "entreprise"  => $entreprise['data'],
            "offres"      => $offres['data']
        ]);
    }

    /*Formulaire de création d'une nouvelle entreprise*/
    public function createForm(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin']); // Seul admin peut accéder à cette page
        
        $service  = $this->makeEntrepriseService();
        $secteurs = $service->listSecteurs();
        if (!$secteurs['success']) {
            self::VerifyFailSystem($secteurs);
        }

        $this->renderDashboard("create", [
            "rubrique" => "Gestion entreprise",
            "title"    => "Créer une entreprise",
            "secteurs" => $secteurs['data']
        ]);
    }

    /*Traitement de création d'entreprise + gestionnaire post createForm*/
    public function create(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin']); // Seul admin peut accéder à cette action
        // Vérification token CSRF
        Security::requireCsrfToken('entreprise_create', $_POST['csrf_token'] ?? null);
        
        $service = $this->makeEntrepriseService();
        $registration = $this->makeAuthRegistrationService();
        $result  = $registration->registerEntreprise($_POST);

        // Si erreur metier => on ré-affiche le formulaire avec les données saisies sinon systemError -> page 500

        if (!$result['success']) {
            self::VerifyFailSystem($result);
            $secteurs = $service->listSecteurs();
            if (!$secteurs['success']) {
                self::VerifyFailSystem($secteurs);
            }
            
            // IMPORTANT : on renvoie au form les valeurs saisies (POST) pour éviter de tout retaper
            $entrepriseToCreate = $_POST;
            $this->renderDashboard("create", [
                "rubrique"   => "Gestion entreprise",
                "title"       => "Créer une entreprise",
                "entreprise" => $entrepriseToCreate,
                "error"      => $result['error'],
                "secteurs"   => $secteurs['data'],
            ]);
            
            return;
        }

        self::flashSuccess($result['message']);
        header("Location: /admin/entreprises");
        exit;
    }

    /**
     * Formulaire de modification d'une entreprise.
     */
    public function editForm(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin','gestionnaire']); // Accès restreint aux rôles spécifiés
        $id = (int)($_GET['id'] ?? Auth::entrepriseId());

        //La meme methode est partage pour admin et gestionnaire
        // SÉCURITÉ : Vérifier que le rôle correspond à la route
        //exclu gestionnaire sur routes /admin et admin sur routes /dashboard
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (Auth::role() === 'gestionnaire' && str_starts_with($currentPath, '/admin/')) {
            self::flashError("Accès refusé : vous n'avez pas les permissions pour cette section.");
            header("Location: /dashboard/equipe");
            exit;
        }

        if (Auth::role() === 'admin' && str_starts_with($currentPath, '/dashboard/')) {
            self::flashError("Veuillez utiliser la section admin.");
            header("Location: /admin/entreprises");
            exit;
        }

        // SÉCURITÉ : un gestionnaire ne peut éditer que son entreprise
        if (Auth::role() === 'gestionnaire' && $id !== Auth::entrepriseId()) {
            self::flashError("Accès refusé : vous ne pouvez modifier que votre entreprise.");
            header("Location: /dashboard/equipe");
            exit;
        }

        $service    = $this->makeEntrepriseService();
        $entreprise = $service->findEntreprise($id);

        if (!$entreprise['success']) {
            self::VerifyFailSystem($entreprise);
        }

        $secteurs= $service->listSecteurs();
        if (!$secteurs['success']) {
            self::VerifyFailSystem($secteurs);
        }
        

        $this->renderDashboard("edit", [
            "rubrique"    => "Gestion entreprise",
            "title"      => Auth::role()=='admin' ? "Modifier une entreprise" : "Modifier mon entreprise",
            "entreprise" => $entreprise['data'],
            "secteurs"   => $secteurs['data'],
        ]);
    }

    /**
     * Traitement des modifications d'entreprise.
     */
    public function update(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin','gestionnaire']); // Acces restreint aux rôles spécifiés
        Security::requireCsrfToken('entreprise_edit', $_POST['csrf_token'] ?? null);

        //partage la meme route pour admin et gestionnaire
        //  on verifie que le role correspond a la route
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (Auth::role() === 'gestionnaire' && str_starts_with($currentPath, '/admin/')) {
            self::flashError("Accès refusé : vous n'avez pas les permissions pour cette section.");
            header("Location: /dashboard/equipe");
            exit;
        }

        if (Auth::role() === 'admin' && str_starts_with($currentPath, '/dashboard/')) {
            self::flashError("Veuillez utiliser la section admin.");
            header("Location: /admin/entreprises");
            exit;
        }

        
        
        $service = $this->makeEntrepriseService();
        $id      = (int)($_POST['id'] ?? 0);

        // SÉCURITÉ : un gestionnaire ne peut modifier que son entreprise
        if (Auth::role() === 'gestionnaire' && $id !== Auth::entrepriseId()) {
            self::flashError("Accès refusé : vous ne pouvez modifier que votre entreprise.");
            header("Location: /dashboard/equipe");
            exit;
        }

        // Appel service
        $result = $service->updateEntreprise($id, $_POST);

        // Si erreur metier => on ré-affiche le formulaire avec les données saisies sinon systemError -> page 500
        if (!$result['success']) {

            self::VerifyFailSystem($result);
            // IMPORTANT : on renvoie au form les valeurs saisies (POST) pour éviter de tout retaper
            // On garde aussi l'id car edit.php construit l'action avec l'id
            $entreprise = $_POST;
            $entreprise['id'] = $id;

            $secteurs= $service->listSecteurs();
            if (!$secteurs['success']) {
                self::VerifyFailSystem($secteurs);
            }

            $this->renderDashboard("edit", [
                "rubrique"   => "Gestion entreprise",
                "title"      => "Modifier une entreprise",
                "entreprise" => $entreprise,
                "secteurs"   => $secteurs['data'],
                "error"      => $result['error'],    // message d’erreur
            ]);
            return;
        }
        self::flashSuccess($result['message']);

        switch (Auth::role()) {
            case 'admin':
                $redirectUrl = "/admin/entreprises";
                break;
            case 'gestionnaire':
                $redirectUrl = "/dashboard/equipe";
                break;
        }
        header("Location: " . $redirectUrl);
    }   


    /**
     * Suppression d'une entreprise.
     */
    public function delete(): void
    {
        Auth::requireLogin();
        Auth::requireRole(['admin']); // Accès restreint aux admins
       
        $csrfkey = $_POST['csrf_key'] ?? '';
        Security::requireCsrfToken($csrfkey, $_POST['csrf_token'] ?? null);

        $id      = (int)($_POST['id'] ?? 0);
        $service = $this->makeEntrepriseService();
        $result=$service->deleteEntreprise($id);

        if (!$result['success']) {
            self::VerifyFailSystem($result);

            self::flashError($result['error']);
            header("Location: /admin/entreprises");
            exit;
        } 

        self::flashSuccess($result['message']);
        header("Location: /admin/entreprises");
        exit;
    }

    public static function flashSuccess(string $message): void
    {
        $_SESSION['success'] = $message;   
    }

    public static function flashError(string $message): void
    {
        $_SESSION['error'] = $message;   
    }

    public static function flashSystemError(string $message): void
    {
        $_SESSION['systemError'] = $message;   
    }

    public static function VerifyFailSystem($result): void
    {
        if (isset($result['systemError']) && $result['code']>=2000) {
                self::flashSystemError($result['error']);
                header("Location: /500");
                exit;
            }
        
    }
}