<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

$router = new Router();

// Routes de test
$router->get('/', 'App\\Modules\\Home\\HomeController@index');

$router->get('/login', 'App\\Modules\\Auth\\AuthController@showLogin');
$router->post('/login', 'App\\Modules\\Auth\\AuthController@login');

$router->get('/register/candidat', 'App\\Modules\\Auth\\AuthController@showRegisterCandidat');
$router->post('/register/candidat', 'App\\Modules\\Auth\\AuthController@registerCandidat');

$router->get('/register/entreprise', 'App\\Modules\\Auth\\AuthController@showRegisterEntreprise');
$router->post('/register/entreprise', 'App\\Modules\\Auth\\AuthController@registerEntreprise');

$router->run();