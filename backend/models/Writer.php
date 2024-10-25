<?php

namespace Backend\Models;

use PDO;

class Writer extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'writers');
    }
}
