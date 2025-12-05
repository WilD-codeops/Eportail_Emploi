<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

$router = new Router();

// Routes de test
$router->get('/', 'App\\Modules\\Home\\HomeController@index');

$router->run();