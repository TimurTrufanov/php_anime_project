<?php

namespace Backend\Models;

use PDO;

class Author extends BaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'authors');
    }
}
