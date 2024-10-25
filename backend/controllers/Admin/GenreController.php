<?php
namespace Backend\Controllers\Admin;

use Backend\Controllers\Admin\BaseEntityController;
use Backend\Models\Database\Database;
use Backend\Models\BaseModel;

class GenreController extends BaseEntityController
{
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new BaseModel($this->db, 'genres');
        $this->entityName = 'Genre';
        $this->entityUrl = 'genres';
    }
}