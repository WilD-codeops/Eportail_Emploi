<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php'; 

use App\Core\Router;
use App\Core\SessionManager;


// CONFIGURATION SECURITE SESSION
ini_set('session.use_strict_mode', 1);           // N'accepte que les ID générés par PHP (anti fixation)
ini_set('session.cookie_httponly', 1);           // Empêche JS de lire les cookies (anti XSS)
ini_set('session.cookie_secure', 0);             // Secure = 1 en production HTTPS  => mettre 1 en prod !
ini_set('session.cookie_samesite', 'Strict');    // Empêche d’envoyer le cookie depuis un autre domaine (anti CSRF)
ini_set('session.use_only_cookies', 1);          // Pas d’ID de session dans l’URL
ini_set('session.gc_maxlifetime', 7200);         // Durée max côté serveur

SessionManager::startSession();
SessionManager::checkSessionExpiration();

$router = new Router();

// Routes home
$router->get('/', 'App\\Modules\\Home\\HomeController@index'); // Accueil
$router->get('/mentions-legales', 'App\\Modules\\Home\\HomeController@mentionsLegales'); // Mentions légales
$router->get('/contact', 'App\\Modules\\Home\\HomeController@contact'); // Contact
$router->get('/centre-aide', 'App\\Modules\\Home\\HomeController@centreAide'); // Centre d'aide
$router->get('/a-propos', 'App\\Modules\\Home\\HomeController@aPropos'); // À propos


// Maintenance page
$router->get('/maintenance', 'App\\Modules\\Home\\HomeController@maintenance');// Maintenance
$router->get('/500', 'App\\Modules\\Home\\HomeController@error500');// Erreur 500

// Auth routes page d'authentification
$router->get('/login', 'App\\Modules\\Auth\\AuthController@showLogin');// Formulaire login
$router->post('/login', 'App\\Modules\\Auth\\AuthController@login');// Traitement login
$router->get('/logout', 'App\\Modules\\Auth\\AuthController@logout');// Logout

$router->get('/register/candidat', 'App\\Modules\\Auth\\AuthController@showRegisterCandidat');// Formulaire inscription candidat
$router->post('/register/candidat', 'App\\Modules\\Auth\\AuthController@registerCandidat');// Traitement inscription candidat

$router->get('/register/entreprise', 'App\\Modules\\Auth\\AuthController@showRegisterEntreprise');// Formulaire inscription entreprise
$router->post('/register/entreprise', 'App\\Modules\\Auth\\AuthController@registerEntreprise');// Traitement inscription entreprise
 
$router->get('/password/forgot',       'App\\Modules\\Auth\\AuthController@showForgotPassword' );// Formulaire mot de passe oublié  
$router->post('/password/forgot',       'App\\Modules\\Auth\\AuthController@ForgotPassword' );// Traitement mot de passe oublié
$router->get('/password/reset',        'App\\Modules\\Auth\\AuthController@showResetPassword'  );// Formulaire réinitialisation mot de passe   
$router->post('/password/reset',       'App\\Modules\\Auth\\AuthController@resetPassword'      );// Traitement réinitialisation mot de passe



// Entreprises Liste publique
$router->get('/entreprises', 'App\\Modules\\Entreprise\\EntrepriseController@index');// Liste entreprises
$router->get('/entreprises/show', 'App\\Modules\\Entreprise\\EntrepriseController@show');// Détail entreprise

// Entreprises — CRUD Admin
$router->get('/admin/entreprises', 'App\\Modules\\Entreprise\\EntrepriseController@adminIndex');// Liste admin entreprises
$router->get('/admin/entreprises/create', 'App\\Modules\\Entreprise\\EntrepriseController@createForm');// Formulaire création entreprise
$router->post('/admin/entreprises/create', 'App\\Modules\\Entreprise\\EntrepriseController@create');// Traitement création entreprise

// Alias (singulier) pour compatibilité d'URL
$router->get('/admin/entreprise/create', 'App\\Modules\\Entreprise\\EntrepriseController@createForm');
$router->post('/admin/entreprise/create', 'App\\Modules\\Entreprise\\EntrepriseController@create');

$router->get('/admin/entreprises/edit', 'App\\Modules\\Entreprise\\EntrepriseController@editForm');// Formulaire édition entreprise
$router->post('/admin/entreprises/edit', 'App\\Modules\\Entreprise\\EntrepriseController@update');// Traitement édition entreprise
$router->post('/admin/entreprises/delete', 'App\\Modules\\Entreprise\\EntrepriseController@delete');// Suppression entreprise
$router->get('/dashboard/entreprise/edit', 'App\\Modules\\Entreprise\\EntrepriseController@editForm');// Formulaire édition entreprise gestionnaire

//Utilisateurs — CRUD Admin
$router->get('/admin/users', 'App\\Modules\\Auth\\UserController@adminIndex');// Liste admin utilisateurs
$router->get('/admin/users/create', 'App\\Modules\\Auth\\UserController@createForm');// Formulaire création utilisateur
$router->post('/admin/users/create', 'App\\Modules\\Auth\\UserController@create');// Traitement création utilisateur
$router->get('/admin/users/edit', 'App\\Modules\\Auth\\UserController@editForm');// Formulaire édition utilisateur
$router->post('/admin/users/edit', 'App\\Modules\\Auth\\UserController@update');// Traitement édition utilisateur
$router->post('/admin/users/delete', 'App\\Modules\\Auth\\UserController@delete');// Suppression utilisateur
//utilisateurs —CRUD gestionnaire
$router->get('/dashboard/equipe', 'App\\Modules\\Auth\\UserController@gestionnaireIndex');// Liste gestion utilisateurs de son équipe
$router->get('/dashboard/equipe/create', 'App\\Modules\\Auth\\UserController@createForm');// Formulaire création utilisateur de son équipe
$router->post('/dashboard/equipe/create', 'App\\Modules\\Auth\\UserController@create');// Traitement création utilisateur de son équipe
$router->get('/dashboard/equipe/edit', 'App\\Modules\\Auth\\UserController@editForm');// Formulaire édition utilisateur de son équipe
$router->post('/dashboard/equipe/edit', 'App\\Modules\\Auth\\UserController@update');// Traitement édition utilisateur de son équipe
$router->post('/dashboard/equipe/delete', 'App\\Modules\\Auth\\UserController@delete');// Suppression utilisateur de son équipe

//utilisateurrs — recruteur
$router->get('/dashboard/profil', 'App\\Modules\\Auth\\UserController@recruteurProfile');// Voir profil recruteur
$router->post('/dashboard/profil', 'App\\Modules\\Auth\\UserController@updateProfile');// Mettre à jour ses infos recruteur


// Offres route publique
$router->get('/offres', 'App\\Modules\\Offres\\OffresController@index');// Liste offres
$router->get('/offres/show', 'App\\Modules\\Offres\\OffresController@show');// Détail offre

// Offres — CRUD Admin
$router->get('/admin/offres', 'App\\Modules\\Offres\\OffresController@adminIndex');// Liste admin offres
$router->get('/admin/offres/create', 'App\\Modules\\Offres\\OffresController@createForm');// Formulaire création offre admin
$router->post('/admin/offres/create', 'App\\Modules\\Offres\\OffresController@create');// Traitement création offre admin
$router->get('/admin/offres/edit', 'App\\Modules\\Offres\\OffresController@editForm');// Formulaire édition offre admin
$router->post('/admin/offres/edit', 'App\\Modules\\Offres\\OffresController@update');// Traitement édition offre admin
$router->post('/admin/offres/delete', 'App\\Modules\\Offres\\OffresController@delete');// Suppression offre admin

// Offres — CRUD Dashboard Recruteur/Gestionnaire
$router->get('/dashboard/offres', 'App\\Modules\\Offres\\OffresController@manageIndex');// Liste gestion offres gestionnaire/recruteur
$router->get('/dashboard/offres/create', 'App\\Modules\\Offres\\OffresController@createForm');// Formulaire création offre gestionnaire/recruteur
$router->post('/dashboard/offres/create', 'App\\Modules\\Offres\\OffresController@create');// Traitement création offre gestionnaire/recruteur
$router->get('/dashboard/offres/edit', 'App\\Modules\\Offres\\OffresController@editForm');// Formulaire édition offre gestionnaire/recruteur
$router->post('/dashboard/offres/edit', 'App\\Modules\\Offres\\OffresController@update');// Traitement édition offre gestionnaire/recruteur
$router->post('/dashboard/offres/delete', 'App\\Modules\\Offres\\OffresController@delete');// Suppression offre gestionnaire/recruteur


// Offres — PARTIALS (AJAX HTML)
$router->get('/admin/offres/partial', 'App\\Modules\\Offres\\OffresController@adminPartial');// Partial Ajax liste admin offres
$router->get('/dashboard/offres/partial', 'App\\Modules\\Offres\\OffresController@managePartial');// Partial Ajax liste gestion offres gestionnaire/recruteur

$router->run();
