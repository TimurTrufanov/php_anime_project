<?php

namespace Backend\Models;

use PDO;

class Artist extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'artists');
    }
}
