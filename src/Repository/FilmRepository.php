<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\DatabaseConnection;
use App\Service\EntityMapper;
use App\Entity\Film;

class FilmRepository
{
    private \PDO $db; // Instance de connexion à la base de données
    private EntityMapper $entityMapperService; // Service pour mapper les entités

    public function __construct()
    {
        // Initialise la connexion à la base de données en utilisant DatabaseConnection
        $this->db = DatabaseConnection::getConnection();
        // Initialise le service de mappage des entités
        $this->entityMapperService = new EntityMapper();
    }

    // Méthode pour récupérer tous les films de la base de données
    public function findAll(): array
    {
        $query = 'SELECT * FROM film';
        $stmt = $this->db->query($query);

        $films = $stmt->fetchAll();

        return $this->entityMapperService->mapToEntities($films, Film::class);
    }

    // Méthode pour récupérer un film par son identifiant
    public function find(int $id): ?Film
    {
        $query = 'SELECT * FROM film WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);

        $film = $stmt->fetch();

        if (!$film) {
            return null; // Retourne null si le film n'est pas trouvé
        }

        return $this->entityMapperService->mapToEntity($film, Film::class);
    }

    // Méthode pour ajouter un nouveau film
    public function create(Film $film): int
    {
        $query = 'INSERT INTO film (title, release_date, director, synopsis, genre)
                  VALUES (:title, :release_date, :director, :synopsis, :genre)';
        $stmt = $this->db->prepare($query);

        $stmt->execute([
            ':title' => $film->getTitle(),
            ':release_date' => $film->getReleaseDate(),
            ':director' => $film->getDirector(),
            ':synopsis' => $film->getSynopsis(),
            ':genre' => $film->getGenre(),
        ]);

        return (int) $this->db->lastInsertId(); // Retourne l'identifiant du film créé
    }

    // Méthode pour mettre à jour un film existant
    public function update(int $id, Film $film): bool
    {
        $query = 'UPDATE film
                  SET title = :title, release_date = :release_date, director = :director, synopsis = :synopsis, genre = :genre
                  WHERE id = :id';
        $stmt = $this->db->prepare($query);

        $updated = $stmt->execute([
            ':id' => $id,
            ':title' => $film->getTitle(),
            ':release_date' => $film->getReleaseDate(),
            ':director' => $film->getDirector(),
            ':synopsis' => $film->getSynopsis(),
            ':genre' => $film->getGenre(),
        ]);

        return $updated;
    }

    // Méthode pour supprimer un film par son identifiant
    public function delete(int $id): bool
    {
        $query = 'DELETE FROM film WHERE id = :id';
        $stmt = $this->db->prepare($query);

        return $stmt->execute([':id' => $id]);
    }
}
