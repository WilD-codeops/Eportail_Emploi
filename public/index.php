<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

$router = new Router();

// Routes home
$router->get('/', 'App\\Modules\\Home\\HomeController@index');

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

$router->get('/admin/entreprises/edit', 'App\\Modules\\Entreprise\\EntrepriseController@edit');
$router->post('/admin/entreprises/edit', 'App\\Modules\\Entreprise\\EntrepriseController@update');
$router->post('/admin/entreprises/delete', 'App\\Modules\\Entreprise\\EntrepriseController@delete');



$router->run();