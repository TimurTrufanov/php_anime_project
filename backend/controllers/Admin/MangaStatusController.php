<?php

namespace Backend\Controllers\Admin;

use Backend\Controllers\Admin\BaseEntityController;
use Backend\Models\Database\Database;
use Backend\Models\BaseModel;

class MangaStatusController extends BaseEntityController
{
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new BaseModel($this->db, 'manga_statuses');
        $this->entityName = 'Manga Status';
        $this->entityUrl = 'manga-statuses';
    }
}