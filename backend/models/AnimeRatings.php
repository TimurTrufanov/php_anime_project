<?php

namespace Backend\Models;

use PDO;

class AnimeRatings
{
    protected PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function rateAnime(int $userId, int $animeId, int $score): bool
    {
        $query = "SELECT * FROM anime_ratings WHERE user_id = :user_id AND anime_id = :anime_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $query = "UPDATE anime_ratings SET score = :score WHERE user_id = :user_id AND anime_id = :anime_id";
        } else {
            $query = "INSERT INTO anime_ratings (user_id, anime_id, score) VALUES (:user_id, :anime_id, :score)";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);

        $result = $stmt->execute();

        $this->updateAverageRating($animeId);

        return $result;
    }

    private function updateAverageRating(int $animeId): void
    {
        $query = "
    UPDATE anime 
    SET average_rating = ROUND((SELECT AVG(score) FROM anime_ratings WHERE anime_id = :anime_id), 2)
    WHERE id = :anime_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
