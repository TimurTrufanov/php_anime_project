<?php

namespace Backend\Models;

use PDO;

class Genre extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'genres');
    }
}
