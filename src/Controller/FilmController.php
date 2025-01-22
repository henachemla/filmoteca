<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\TemplateRenderer;
use App\Entity\Film;
use App\Repository\FilmRepository;

class FilmController
{
    private TemplateRenderer $renderer;

    public function __construct()
    {
        $this->renderer = new TemplateRenderer();
    }

    public function list(array $queryParams)
    {
        $filmRepository = new FilmRepository();
        $films = $filmRepository->findAll();

        /* $filmEntities = [];
        foreach ($films as $film) {
            $filmEntity = new Film();
            $filmEntity->setId($film['id']);
            $filmEntity->setTitle($film['title']);
            $filmEntity->setYear($film['year']);
            $filmEntity->setType($film['type']);
            $filmEntity->setSynopsis($film['synopsis']);
            $filmEntity->setDirector($film['director']);
            $filmEntity->setCreatedAt(new \DateTime($film['created_at']));
            $filmEntity->setUpdatedAt(new \DateTime($film['updated_at']));

            $filmEntities[] = $filmEntity;
        } */

        //dd($films);

        echo $this->renderer->render('film/list.html.twig', [
            'films' => $films,
        ]);

        // header('Content-Type: application/json');
        // echo json_encode($films);
    }

    public function create() : void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $filmRepository = new FilmRepository();
            $film = new Film();
            $film->setTitle($_POST['title']);
            $film->setYear((int)$_POST['year']);
            $film->setDirector($_POST['director']);
            $film->setSynopsis($_POST['synopsis']);
            $film->setCreatedAt(new \DateTime());
            $film->setUpdatedAt(new \DateTime());

            $filmRepository->save($film);
            header('Location: /films');
            exit;
        }

        echo $this->renderer->render('film/list.html.twig');
    }
    

    public function read(array $queryParams)
{
    // Vérifier que l'ID est présent dans les paramètres
    if (!isset($queryParams['id']) || !is_numeric($queryParams['id'])) {
        echo "ID invalide ou manquant.";
        return;
    }

    // Convertir l'ID en entier
    $filmId = (int) $queryParams['id'];

    // Charger le film depuis la base de données
    $filmRepository = new FilmRepository();
    $film = $filmRepository->find($filmId);

    // Vérifier si le film existe
    if (!$film) {
        echo "Film non trouvé.";
        return;
    }

    // Afficher les détails du film
    include_once __DIR__ . "/../views/film/read.html.twig";
}

    

    public function update(array $queryParams, array $postData)
    {
        if (!isset($queryParams['id']) || !is_numeric($queryParams['id'])) {
            echo "ID invalide ou manquant.";
            return;
        }

        if (empty($postData['title']) || empty($postData['release_date']) || empty($postData['director']) || empty($postData['genre']) || empty($postData['synopsis'])) {
            echo "Tous les champs doivent être remplis.";
            return;
        }

        $filmRepository = new FilmRepository();
        $updated = $filmRepository->update((int) $queryParams['id'], [
            'title' => htmlspecialchars($postData['title']),
            'release_date' => htmlspecialchars($postData['release_date']),
            'director' => htmlspecialchars($postData['director']),
            'genre' => htmlspecialchars($postData['genre']),
            'synopsis' => htmlspecialchars($postData['synopsis']),
        ]);

        if ($updated) {
            echo "Le film a été mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour du film.";
        }
    }


    public function delete(array $queryParams)
    {
        if (!isset($queryParams['id']) || !is_numeric($queryParams['id'])) {
            echo "ID invalide ou manquant.";
            return;
        }

        $filmRepository = new FilmRepository();
        $deleted = $filmRepository->delete((int) $queryParams['id']);

        if ($deleted) {
            echo "Le film a été supprimé avec succès.";
        } else {
            echo "Erreur lors de la suppression du film.";
        }
    }

}