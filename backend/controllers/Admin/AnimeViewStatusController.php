<?php

namespace Backend\Controllers\Admin;

use Backend\Controllers\Admin\BaseEntityController;
use Backend\Models\Database\Database;
use Backend\Models\BaseModel;

class AnimeViewStatusController extends BaseEntityController
{
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new BaseModel($this->db, 'anime_view_statuses');
        $this->entityName = 'Anime View Status';
        $this->entityUrl = 'anime-view-statuses';
    }
}