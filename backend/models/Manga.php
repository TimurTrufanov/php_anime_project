<?php

namespace Backend\Models;

use Exception;
use PDO;

class Manga
{
    protected PDO $conn;
    protected string $table = 'manga';

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getAllWithRatings(): false|array
    {
        $query = "
            SELECT manga.*, AVG(manga_ratings.score) AS average_rating
            FROM manga
            LEFT JOIN manga_ratings ON manga.id = manga_ratings.manga_id
            GROUP BY manga.id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createManga(array $data): bool
    {
        $query = "
            INSERT INTO manga (name, description, release_date, image_url, chapter_count, status_id)
            VALUES (:name, :description, :release_date, :image_url, :chapter_count, :status_id)
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':release_date', $data['release_date']);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':chapter_count', $data['chapter_count'], PDO::PARAM_INT);
        $stmt->bindParam(':status_id', $data['status_id'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getMangaById($id)
    {
        $query = "SELECT * FROM manga WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateManga($data, $id): bool
    {
        $query = "
        UPDATE manga 
        SET name = :name, description = :description, release_date = :release_date, image_url = :image_url, chapter_count = :chapter_count, 
            status_id = :status_id
        WHERE id = :id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindParam(':release_date', $data['release_date']);
        $stmt->bindParam(':image_url', $data['image_url'], PDO::PARAM_STR);
        $stmt->bindParam(':chapter_count', $data['chapter_count'], PDO::PARAM_INT);
        $stmt->bindParam(':status_id', $data['status_id'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteManga($id): bool
    {
        $query = "DELETE FROM manga WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @throws Exception
     */
    public function attachRelations($mangaId, $authors, $artists, $genres): void
    {
        try {
            $this->conn->beginTransaction();
            $this->deleteRelations($mangaId);

            foreach ($authors as $authorId) {
                $query = "INSERT INTO manga_authors (manga_id, author_id) VALUES (:manga_id, :author_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':manga_id' => $mangaId, ':author_id' => $authorId]);
            }

            foreach ($artists as $artistId) {
                $query = "INSERT INTO manga_artists (manga_id, artist_id) VALUES (:manga_id, :artist_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':manga_id' => $mangaId, ':artist_id' => $artistId]);
            }

            foreach ($genres as $genreId) {
                $query = "INSERT INTO manga_genres (manga_id, genre_id) VALUES (:manga_id, :genre_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':manga_id' => $mangaId, ':genre_id' => $genreId]);
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function deleteRelations($mangaId): void
    {
        $query = "DELETE FROM manga_authors WHERE manga_id = :manga_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':manga_id' => $mangaId]);

        $query = "DELETE FROM manga_artists WHERE manga_id = :manga_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':manga_id' => $mangaId]);

        $query = "DELETE FROM manga_genres WHERE manga_id = :manga_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':manga_id' => $mangaId]);
    }

    public function getAuthorsByMangaId($mangaId): false|array
    {
        $query = "SELECT author_id FROM manga_authors WHERE manga_id = :manga_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getArtistsByMangaId($mangaId): false|array
    {
        $query = "SELECT artist_id FROM manga_artists WHERE manga_id = :manga_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getGenresByMangaId($mangaId): false|array
    {
        $query = "SELECT genre_id FROM manga_genres WHERE manga_id = :manga_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAuthorNamesByMangaId($mangaId): array
    {
        $query = "
        SELECT d.name 
        FROM manga_authors ad
        JOIN authors d ON ad.author_id = d.id
        WHERE ad.manga_id = :manga_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getArtistNamesByMangaId($mangaId): array
    {
        $query = "
        SELECT w.name 
        FROM manga_artists aw
        JOIN artists w ON aw.artist_id = w.id
        WHERE aw.manga_id = :manga_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getGenreNamesByMangaId($mangaId): array
    {
        $query = "
        SELECT g.name 
        FROM manga_genres ag
        JOIN genres g ON ag.genre_id = g.id
        WHERE ag.manga_id = :manga_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getMangaWithPagination(int $limit, int $offset, string $search = '', string $filter = '', string $sort = 'name'): array
    {
        $query = "SELECT * FROM manga WHERE 1=1";

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

    public function getTotalMangaCount(string $search = '', string $filter = ''): int
    {
        $query = "SELECT COUNT(*) FROM manga WHERE 1=1";
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

    public function getRatings($mangaId): array
    {
        $query = "
        SELECT AVG(score) as average, COUNT(*) as count
        FROM manga_ratings
        WHERE manga_id = :manga_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}