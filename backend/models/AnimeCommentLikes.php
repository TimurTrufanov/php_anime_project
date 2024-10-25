<?php

namespace Backend\Models;

use PDO;

class AnimeCommentLikes
{
    protected PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function addLike(int $commentId, int $userId): bool
    {
        $query = "INSERT INTO anime_comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function removeLike(int $commentId, int $userId): bool
    {
        $query = "DELETE FROM anime_comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function userLikedComment(int $commentId, int $userId): bool
    {
        $query = "SELECT COUNT(*) FROM anime_comment_likes WHERE comment_id = :comment_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getLikesCount(int $commentId): int
    {
        $query = "SELECT COUNT(*) FROM anime_comment_likes WHERE comment_id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
