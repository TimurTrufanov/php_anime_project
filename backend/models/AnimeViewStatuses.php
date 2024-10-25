<?php

namespace Backend\Models;

use PDO;

class AnimeViewStatuses extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'anime_view_statuses');
    }
}
