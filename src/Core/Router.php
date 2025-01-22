<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\HomeController;

class Router
{
    public function route(): void
    {
        // Récupère l'URL demandée et la nettoie
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Définit les routes et leurs contrôleurs associés
        $routes = [
            'film' => 'FilmController',
            'contact' => 'ContactController',
        ];

        // Analyse l'URI pour extraire la route et les paramètres
        $parts = explode('/', $uri); // Exemple : ['film', 'read', '1']

        $route = $parts[0] ?? null;   // Exemple : 'film'
        $action = $parts[1] ?? null; // Exemple : 'read'
        $param = $parts[2] ?? null;  // Exemple : '1'

        if (array_key_exists($route, $routes)) {
            // Crée dynamiquement le contrôleur
            $controllerName = 'App\\Controller\\' . $routes[$route];

            if (!class_exists($controllerName)) {
                echo "Controller '$controllerName' not found";
                return;
            }

            $controller = new $controllerName();

            // Vérifie si l'action existe dans le contrôleur
            if (method_exists($controller, $action)) {
                $queryParams = $_GET;

                // Ajoute le paramètre extrait de l'URL à $_GET
                if ($param) {
                    $queryParams['id'] = $param;
                }

                // Appelle la méthode du contrôleur avec les paramètres
                $controller->$action($queryParams);
            } else {
                echo "Action '$action' not found in $controllerName";
            }
        } else {
            // Si la route n'existe pas, affiche la page d'accueil
            $controller = new HomeController();
            $controller->index();
        }
    }
}
