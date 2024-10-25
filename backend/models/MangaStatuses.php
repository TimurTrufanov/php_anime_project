<?php

namespace Backend\Models;

use PDO;

class MangaStatuses extends BaseModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        parent::__construct($db, 'manga_statuses');
        $this->db = $db;
    }

    public function getStatusById($statusId)
    {
        $query = "SELECT * FROM manga_statuses WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $statusId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
