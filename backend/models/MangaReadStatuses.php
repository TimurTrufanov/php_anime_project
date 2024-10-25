<?php

namespace Backend\Models;

use PDO;

class MangaReadStatuses extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'manga_read_statuses');
    }
}
