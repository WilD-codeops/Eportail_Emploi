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
$router->get('/', 'App\\Modules\\Home\\HomeController@index');

// Maintenance page
$router->get('/maintenance', 'App\\Modules\\Home\\HomeController@maintenance');

// Auth routes page d'authentification
$router->get('/login', 'App\\Modules\\Auth\\AuthController@showLogin');
$router->post('/login', 'App\\Modules\\Auth\\AuthController@login');
$router->get('/logout', 'App\\Modules\\Auth\\AuthController@logout');

$router->get('/register/candidat', 'App\\Modules\\Auth\\AuthController@showRegisterCandidat');
$router->post('/register/candidat', 'App\\Modules\\Auth\\AuthController@registerCandidat');

$router->get('/register/entreprise', 'App\\Modules\\Auth\\AuthController@showRegisterEntreprise');
$router->post('/register/entreprise', 'App\\Modules\\Auth\\AuthController@registerEntreprise');


// Entreprises Liste publique
$router->get('/entreprises', 'App\\Modules\\Entreprise\\EntrepriseController@index');

// Entreprises — CRUD Admin
$router->get('/admin/entreprises', 'App\\Modules\\Entreprise\\EntrepriseController@adminIndex');
$router->get('/admin/entreprises/create', 'App\\Modules\\Entreprise\\EntrepriseController@createForm');
$router->post('/admin/entreprises/create', 'App\\Modules\\Entreprise\\EntrepriseController@create');

$router->get('/admin/entreprises/edit', 'App\\Modules\\Entreprise\\EntrepriseController@editForm');
$router->post('/admin/entreprises/edit', 'App\\Modules\\Entreprise\\EntrepriseController@update');
$router->post('/admin/entreprises/delete', 'App\\Modules\\Entreprise\\EntrepriseController@delete');


// Offres route publique
$router->get('/offres', 'App\\Modules\\Offres\\OffresController@index'); 
$router->get('/offres/show', 'App\\Modules\\Offres\\OffresController@show');

// Offres — CRUD Admin
$router->get('/admin/offres', 'App\\Modules\\Offres\\OffresController@adminIndex');
$router->get('/admin/offres/create', 'App\\Modules\\Offres\\OffresController@createForm');
$router->post('/admin/offres/create', 'App\\Modules\\Offres\\OffresController@create');
$router->get('/admin/offres/edit', 'App\\Modules\\Offres\\OffresController@editForm');
$router->post('/admin/offres/edit', 'App\\Modules\\Offres\\OffresController@update');
$router->post('/admin/offres/delete', 'App\\Modules\\Offres\\OffresController@delete');

// Offres — CRUD Dashboard Recruteur/Gestionnaire
$router->get('/dashboard/offres', 'App\\Modules\\Offres\\OffresController@manageIndex');
$router->get('/dashboard/offres/create', 'App\\Modules\\Offres\\OffresController@createForm');
$router->post('/dashboard/offres/create', 'App\\Modules\\Offres\\OffresController@create');
$router->get('/dashboard/offres/edit', 'App\\Modules\\Offres\\OffresController@editForm');
$router->post('/dashboard/offres/edit', 'App\\Modules\\Offres\\OffresController@update');
$router->post('/dashboard/offres/delete', 'App\\Modules\\Offres\\OffresController@delete');

// Offres — PARTIALS (AJAX HTML)
$router->get('/admin/offres/partial', 'App\\Modules\\Offres\\OffresController@adminPartial');
$router->get('/dashboard/offres/partial', 'App\\Modules\\Offres\\OffresController@managePartial');

$router->run();
