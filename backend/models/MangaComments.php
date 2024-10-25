<?php

namespace Backend\Models;

use PDO;

class MangaComments
{
    protected PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function addComment(int $userId, int $mangaId, string $text): bool
    {
        $query = "INSERT INTO manga_comments (user_id, manga_id, text, created_at) 
                  VALUES (:user_id, :manga_id, :text, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->bindParam(':text', $text, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getCommentsByMangaId(int $mangaId): array
    {
        $query = "
            SELECT c.*, u.username, u.avatar_url, u.id AS user_id
            FROM manga_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.manga_id = :manga_id
            ORDER BY created_at DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':manga_id', $mangaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommentById(int $commentId): ?array
    {
        $query = "SELECT * FROM manga_comments WHERE id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function deleteComment(int $commentId): bool
    {
        $query = "DELETE FROM manga_comments WHERE id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
