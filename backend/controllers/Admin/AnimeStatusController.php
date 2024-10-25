<?php

namespace Backend\Controllers\Admin;

use Backend\Controllers\Admin\BaseEntityController;
use Backend\Models\Database\Database;
use Backend\Models\BaseModel;

class AnimeStatusController extends BaseEntityController
{
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new BaseModel($this->db, 'anime_statuses');
        $this->entityName = 'Anime Status';
        $this->entityUrl = 'anime-statuses';
    }
}