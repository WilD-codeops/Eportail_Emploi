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

        // On récupère le path sans query string
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Normaliser le path en supprimant le "base path" (ex: /Eportail_Emploi/public)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        // dirname peut contenir backslashes sur Windows — on normalise
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($basePath !== '' && $basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
            if ($path === '') {
                $path = '/';
            }
        }

        // Si pas de route enregistrée pour cette méthode + path
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

        // On instancie simplement le contrôleur (pas de container App ici)
        // ATTENTION : si votre contrôleur a des dépendances dans son constructeur,
        // l'instanciation sans arguments provoquera une erreur. Dans ce cas,
        // soit le contrôleur doit avoir un constructeur sans paramètres,
        // soit il faut fournir un simple factory / container.
        $controller = new $class();

        if (!method_exists($controller, $methodName)) {
            throw new \RuntimeException("Méthode {$methodName} introuvable dans {$class}");
        }

        $controller->$methodName();
    }
}