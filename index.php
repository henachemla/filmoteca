<?php
class Router {
    public function route ($url){
        if($url == '/films'){
            echo 'Affichage de la page des films';
        }elseif ($url == '/contact'){
            echo 'affichage de la page de contact';
        }else {
            echo 'page non trouvée'
        }
    }
}

$url = $_SERVER['REQUEST_URI'];
$router = new Router ();
$router -> route ($url);