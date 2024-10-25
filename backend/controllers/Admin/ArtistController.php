<?php

namespace Backend\Controllers\Admin;

use Backend\Controllers\Admin\BaseEntityController;
use Backend\Models\Database\Database;
use Backend\Models\BaseModel;

class ArtistController extends BaseEntityController
{
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new BaseModel($this->db, 'artists');
        $this->entityName = 'Artist';
        $this->entityUrl = 'artists';
    }
}