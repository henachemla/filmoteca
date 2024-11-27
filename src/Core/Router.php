<?php

declare(strict_types=1);

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Router
{
    private $twig;

    public function __construct()
    {
        // Initialiser Twig
        $loader = new FilesystemLoader(__DIR__.'/../views');
        $this->twig = new Environment($loader);
    }

    public function route(): void
    {
        // Récupère l'URL demandée
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $parts = explode('/', $uri);

        $route = $parts[0] ?? null;   // 'films'
        $action = $parts[1] ?? null; // 'create'

        // Définit les routes et leurs contrôleurs associés
        $routes = [
            'films' => 'FilmController',
            'contact' => 'ContactController',
        ];

        if (array_key_exists($route, $routes)) {
            $controllerName = 'App\\Controller\\' . $routes[$route];

            if (!class_exists($controllerName)) {
                echo "Controller '$controllerName' not found";
                return;
            }

            // Instancier le contrôleur avec Twig
            $controller = new $controllerName($this->twig);

            if (method_exists($controller, $action)) {
                $queryParams = $_GET;
                $controller->$action($queryParams);
            } else {
                echo "Action '$action' not found in $controllerName";
            }
        } else {
            echo "404 Not Found";
        }
    }
}
