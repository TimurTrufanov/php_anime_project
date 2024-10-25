<?php

namespace Backend\Models;

use PDO;

class Director extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'directors');
    }
}
