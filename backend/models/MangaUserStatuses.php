<?php

namespace Backend\Models;

use PDO;

class MangaUserStatuses
{
    protected PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function setStatus(int $userId, int $mangaId, int $statusId): bool
    {
        $query = "INSERT INTO manga_user_statuses (user_id, manga_id, status_id) 
                  VALUES (:user_id, :manga_id, :status_id)
                  ON DUPLICATE KEY UPDATE status_id = :status_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getStatusesByUserId(int $userId): array
    {
        $query = "
        SELECT avs.manga_id, a.name AS manga_name, vs.name AS status_name, avs.status_id, 
               COALESCE(ar.score, 'not rated') AS rating
        FROM manga_user_statuses avs
        JOIN manga_read_statuses vs ON avs.status_id = vs.id
        LEFT JOIN manga a ON avs.manga_id = a.id
        LEFT JOIN manga_ratings ar ON avs.manga_id = ar.manga_id AND avs.user_id = ar.user_id
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

    public function getStatusByUserIdAndMangaId(int $userId, int $mangaId): ?int
    {
        $query = "
        SELECT status_id 
        FROM manga_user_statuses 
        WHERE user_id = :user_id AND manga_id = :manga_id
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
