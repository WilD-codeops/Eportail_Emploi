<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri    = $_SERVER['REQUEST_URI'] ?? '/';

    // Chemin brut
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';

    // On récupère le "base path" (ex: /Eportail_Emploi/public)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath   = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    // Si le path commence par le basePath, on le retire
    var_dump($method, $path);
    if ($basePath !== '' && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath)) ?: '/';
    }
   
    // À ce stade, si tu es sur http://localhost/Eportail_Emploi/public/
    // $path vaudra simplement "/"

    if (!isset($this->routes[$method][$path])) {
        http_response_code(404);
        echo "404 - Page non trouvée";
        return;
    }

    $action = $this->routes[$method][$path];

    [$class, $methodName] = explode('@', $action);
    if (!class_exists($class)) {
        throw new \RuntimeException("Classe contrôleur introuvable : {$class}");
    }

    $app = \App\Core\App::getInstance();
    $controller = $app->resolve($class); 

    if (!method_exists($controller, $methodName)) {
        throw new \RuntimeException("Méthode {$methodName} introuvable dans {$class}");
    }

    $controller->$methodName();
    }
}