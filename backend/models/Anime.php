<?php

namespace Backend\Models;

use Exception;
use PDO;

class Anime
{
    protected PDO $conn;
    protected string $table = 'anime';

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getAllWithRatings(): false|array
    {
        $query = "
            SELECT anime.*, AVG(anime_ratings.score) AS average_rating
            FROM anime
            LEFT JOIN anime_ratings ON anime.id = anime_ratings.anime_id
            GROUP BY anime.id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createAnime(array $data): bool
    {
        $query = "
            INSERT INTO anime (name, description, release_date, image_url, trailer_url, status_id, episode_duration)
            VALUES (:name, :description, :release_date, :image_url, :trailer_url, :status_id, :episode_duration)
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':release_date', $data['release_date']);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':trailer_url', $data['trailer_url'], PDO::PARAM_STR);
        $stmt->bindParam(':status_id', $data['status_id'], PDO::PARAM_INT);
        $stmt->bindParam(':episode_duration', $data['episode_duration'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getAnimeById($id)
    {
        $query = "SELECT * FROM anime WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAnime($data, $id): bool
    {
        $query = "
        UPDATE anime 
        SET name = :name, description = :description, release_date = :release_date, image_url = :image_url, trailer_url = :trailer_url, 
            status_id = :status_id, episode_duration = :episode_duration
        WHERE id = :id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':release_date', $data['release_date']);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':trailer_url', $data['trailer_url'], PDO::PARAM_STR);
        $stmt->bindParam(':status_id', $data['status_id'], PDO::PARAM_INT);
        $stmt->bindParam(':episode_duration', $data['episode_duration'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteAnime($id): bool
    {
        $query = "DELETE FROM anime WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @throws Exception
     */
    public function attachRelations($animeId, $directors, $writers, $genres): void
    {
        try {
            $this->conn->beginTransaction();
            $this->deleteRelations($animeId);

            foreach ($directors as $directorId) {
                $query = "INSERT INTO anime_directors (anime_id, director_id) VALUES (:anime_id, :director_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':anime_id' => $animeId, ':director_id' => $directorId]);
            }

            foreach ($writers as $writerId) {
                $query = "INSERT INTO anime_writers (anime_id, writer_id) VALUES (:anime_id, :writer_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':anime_id' => $animeId, ':writer_id' => $writerId]);
            }

            foreach ($genres as $genreId) {
                $query = "INSERT INTO anime_genres (anime_id, genre_id) VALUES (:anime_id, :genre_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':anime_id' => $animeId, ':genre_id' => $genreId]);
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function deleteRelations($animeId): void
    {
        $query = "DELETE FROM anime_directors WHERE anime_id = :anime_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':anime_id' => $animeId]);

        $query = "DELETE FROM anime_writers WHERE anime_id = :anime_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':anime_id' => $animeId]);

        $query = "DELETE FROM anime_genres WHERE anime_id = :anime_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':anime_id' => $animeId]);
    }

    public function getDirectorsByAnimeId($animeId): false|array
    {
        $query = "SELECT director_id FROM anime_directors WHERE anime_id = :anime_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getWritersByAnimeId($animeId): false|array
    {
        $query = "SELECT writer_id FROM anime_writers WHERE anime_id = :anime_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getGenresByAnimeId($animeId): false|array
    {
        $query = "SELECT genre_id FROM anime_genres WHERE anime_id = :anime_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDirectorNamesByAnimeId($animeId): array
    {
        $query = "
        SELECT d.name 
        FROM anime_directors ad
        JOIN directors d ON ad.director_id = d.id
        WHERE ad.anime_id = :anime_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getWriterNamesByAnimeId($animeId): array
    {
        $query = "
        SELECT w.name 
        FROM anime_writers aw
        JOIN writers w ON aw.writer_id = w.id
        WHERE aw.anime_id = :anime_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getGenreNamesByAnimeId($animeId): array
    {
        $query = "
        SELECT g.name 
        FROM anime_genres ag
        JOIN genres g ON ag.genre_id = g.id
        WHERE ag.anime_id = :anime_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAnimeWithPagination(int $limit, int $offset, string $search = '', string $filter = '', string $sort = 'name'): array
    {
        $query = "SELECT * FROM anime WHERE 1=1";

        if ($search) {
            $query .= " AND name LIKE :search";
        }
        if ($filter) {
            $query .= " AND status_id = :filter";
        }

        $allowedSorts = ['name', 'release_date', 'average_rating', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'release_date') {
                $query .= " ORDER BY release_date IS NULL, release_date";
            } elseif ($sort === 'average_rating') {
                $query .= " ORDER BY " . $sort . " DESC";
            } else {
                $query .= " ORDER BY " . $sort;
            }
        } else {
            $query .= " ORDER BY name";
        }

        $query .= " LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);

        if ($search) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        if ($filter) {
            $stmt->bindValue(':filter', $filter, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAnimeCount(string $search = '', string $filter = ''): int
    {
        $query = "SELECT COUNT(*) FROM anime WHERE 1=1";
        if ($search) {
            $query .= " AND name LIKE :search";
        }
        if ($filter) {
            $query .= " AND status_id = :filter";
        }

        $stmt = $this->conn->prepare($query);
        if ($search) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        if ($filter) {
            $stmt->bindValue(':filter', $filter, PDO::PARAM_INT);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getRatings($animeId): array
    {
        $query = "
        SELECT AVG(score) as average, COUNT(*) as count
        FROM anime_ratings
        WHERE anime_id = :anime_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}