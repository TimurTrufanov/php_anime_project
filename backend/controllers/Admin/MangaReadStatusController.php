<?php

namespace Backend\Controllers\Admin;

use Backend\Controllers\Admin\BaseEntityController;
use Backend\Models\Database\Database;
use Backend\Models\BaseModel;

class MangaReadStatusController extends BaseEntityController
{
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new BaseModel($this->db, 'manga_read_statuses');
        $this->entityName = 'Manga Read Status';
        $this->entityUrl = 'manga-read-statuses';
    }
}