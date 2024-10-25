<?php

namespace Backend\Models;

use PDO;

class AnimeUserStatuses
{
    protected PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function setStatus(int $userId, int $animeId, int $statusId): bool
    {
        $query = "INSERT INTO anime_user_statuses (user_id, anime_id, status_id) 
                  VALUES (:user_id, :anime_id, :status_id)
                  ON DUPLICATE KEY UPDATE status_id = :status_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getStatusesByUserId(int $userId): array
    {
        $query = "
        SELECT avs.anime_id, a.name AS anime_name, vs.name AS status_name, avs.status_id, 
               COALESCE(ar.score, 'not rated') AS rating
        FROM anime_user_statuses avs
        JOIN anime_view_statuses vs ON avs.status_id = vs.id
        LEFT JOIN anime a ON avs.anime_id = a.id
        LEFT JOIN anime_ratings ar ON avs.anime_id = ar.anime_id AND avs.user_id = ar.user_id
        WHERE avs.user_id = :user_id
        ORDER BY 
        CASE WHEN ar.score IS NULL THEN 1 ELSE 0 END,
        ar.score DESC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatusByUserIdAndAnimeId(int $userId, int $animeId): ?int
    {
        $query = "
        SELECT status_id 
        FROM anime_user_statuses 
        WHERE user_id = :user_id AND anime_id = :anime_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':anime_id', $animeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
