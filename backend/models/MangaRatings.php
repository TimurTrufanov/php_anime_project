<?php

namespace Backend\Models;

use PDO;

class MangaRatings
{
    protected PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function rateManga(int $userId, int $mangaId, int $score): bool
    {
        $query = "SELECT * FROM manga_ratings WHERE user_id = :user_id AND manga_id = :manga_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $query = "UPDATE manga_ratings SET score = :score WHERE user_id = :user_id AND manga_id = :manga_id";
        } else {
            $query = "INSERT INTO manga_ratings (user_id, manga_id, score) VALUES (:user_id, :manga_id, :score)";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);

        $result = $stmt->execute();

        $this->updateAverageRating($mangaId);

        return $result;
    }

    private function updateAverageRating(int $mangaId): void
    {
        $query = "
    UPDATE manga 
    SET average_rating = ROUND((SELECT AVG(score) FROM manga_ratings WHERE manga_id = :manga_id), 2)
    WHERE id = :manga_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
