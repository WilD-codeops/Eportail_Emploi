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
     * Permet d'éviter la création manuelle des dépendances partout.
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

        return new AuthRegistrationService($authService, $entrepriseService);
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

        $page = max(1, (int)($_GET['page'] ?? 1));//page minimum = 1 
        $limit = 10; //elements par page
        $offset = ($page - 1) * $limit;//calcul offset qui correspond au nb d'elements a sauter avant de commencer a recuperer les elements

        $service     = $this->makeEntrepriseService();
        $entreprises = $service->searchEntreprises($filters, $limit, $offset);//appel service avec filtres et pagination
        
        if (!$entreprises['success']) {
            self::VerifyFailSystem($entreprises);
        }

        $total= $entreprises['total'];
        
        
        $pages = (int)ceil($total / $limit); //calcul nb total de pages a afficher selon le total d'elements et le nb d'elements par page

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
            "pages"       => $pages
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
        $page = max(1, (int)($_GET['page'] ?? 1));//page minimum = 1 
        $limit = 10; //elements par page
        $offset = ($page - 1) * $limit;//calcul offset qui correspond au nb d'elements a sauter avant de commencer a recuperer les elements

        $service     = $this->makeEntrepriseService();
        $entreprises = $service->searchEntreprises($filters, $limit, $offset);

        
        if (!$entreprises['success']) {
            self::VerifyFailSystem($entreprises);
        }

        $total= $entreprises['total'];
        
        
        $pages = (int)ceil($total / $limit); //calcul nb total de pages a afficher selon le total d'elements et le nb d'elements par page

        //Données pour les filtres
        $secteurs = $service->listSecteurs();
        if (!$secteurs['success']) {
            self::VerifyFailSystem($secteurs);
        }
        
         // vue dashboard avec layout dashboard

        $this->renderDashboard("list", [
            "title"       => "Gestion des entreprises",
            "entreprises" => $entreprises['data'],
            "secteurs"    => $secteurs['data'],
            "page"        => $page,
            "pages"       => $pages
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

        if (!$result['success']) {

            $secteurs = $service->listSecteurs();
            if (!$secteurs['success']) {
                self::VerifyFailSystem($secteurs);
            }

            $entrepriseToCreate = $_POST;
            $this->renderDashboard("create", [
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
        Auth::requireRole(['admin','recruteur','gestionnaire']); // Accès restreint aux rôles spécifiés
        $id = (int)($_GET['id'] ?? 0);

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
            "title"      => "Modifier une entreprise",
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
        Auth::requireRole(['admin','recruteur','gestionnaire']); // Acces restreint aux rôles spécifiés
        Security::requireCsrfToken('entreprise_edit', $_POST['csrf_token'] ?? null);

        $service = $this->makeEntrepriseService();
        $id      = (int)($_POST['id'] ?? 0);

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
                "title"      => "Modifier une entreprise",
                "entreprise" => $entreprise,
                "secteurs"   => $secteurs['data'],
                "error"      => $result['error'],    // message d’erreur
            ]);
            return;
        }
        self::flashSuccess($result['message']);
        header("Location: /admin/entreprises");
        exit;
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