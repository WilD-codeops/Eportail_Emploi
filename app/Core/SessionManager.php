<?php       
    namespace App\Core ;

    use App\Core\Auth;

    class SessionManager
    {
        public static function startSession(): void
        {
            if (session_status() === PHP_SESSION_NONE) {
                session_name("EPORTAILSESSID");  // Nom personnalisé pour la session
                session_start();
            }
        }
        
        public static function regenerateSessionId(): void
        {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
        }

        public static function checkSessionExpiration(int $timeoutSeconds = 1800,int $absoluteTimeoutSeconds = 7200):void { // 30 min et 2 heures
            if (!Auth::isLogged()) {
                return; // Aucun contrôle pour les visiteurs
            }
        
            $currentTime = time();
        
            // Initialisation si nécessaire
            if (!isset($_SESSION['created_at'])) {
                $_SESSION['created_at'] = $currentTime;
            }
            if (!isset($_SESSION['last_activity'])) {
                $_SESSION['last_activity'] = $currentTime;
            }
        
            // Timeout d'inactivité
            if (($currentTime - $_SESSION['last_activity']) > $timeoutSeconds) {
                Auth::logout();
                exit;
            }
        
            // Timeout absolu
            if (($currentTime - $_SESSION['created_at']) > $absoluteTimeoutSeconds) {
                 Auth::logout();
                exit;
            }
        
            // Mise à jour activité
            $_SESSION['last_activity'] = $currentTime;
        }
        

        public static function sessionDestroy(): void
        {
            self::startSession();

            session_unset();    // Supprime toutes les variables de session

                if(ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(                          // Supprime cookie PHPSESSID navigateur
                        session_name(),
                        '',
                        time() - 42000,
                        $params["path"],
                        $params["domain"],
                        $params["secure"],
                        $params["httponly"]
                    );
                }   

                session_destroy();
                
        }
    }