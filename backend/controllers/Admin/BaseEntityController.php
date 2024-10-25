<?php

namespace Backend\Controllers\Admin;

use Backend\Controllers\BaseController;
use Backend\Models\BaseModel;
use Backend\Validation\NameValidator;

class BaseEntityController extends BaseController
{
    protected BaseModel $model;
    protected string $entityName;
    protected string $entityUrl;

    public function index(): void
    {
        $items = $this->model->getAll();
        $data = [
            'items' => $items,
            'title' => $this->entityName . ' List',
            'entityName' => $this->entityName,
            'entityUrl' => $this->entityUrl
        ];
        $this->render('admin/entity/index', $data, 'admin');
    }

    public function show(int $id): void
    {
        $item = $this->model->getById($id);
        if (!$item) {
            http_response_code(404);
            echo $this->entityName . " not found";
            return;
        }
        $data = [
            'item' => $item,
            'title' => 'View ' . $this->entityName,
            'entityUrl' => $this->entityUrl,
            'entityName' => $this->entityName
        ];
        $this->render('admin/entity/show', $data, 'admin');
    }

    public function edit(int $id): void
    {
        $item = $this->model->getById($id);
        if (!$item) {
            http_response_code(404);
            echo $this->entityName . " not found";
            return;
        }
        $data = [
            'item' => $item,
            'title' => 'Edit ' . $this->entityName,
            'entityUrl' => $this->entityUrl,
            'entityName' => $this->entityName
        ];
        $this->render('admin/entity/edit', $data, 'admin');
    }

    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $validator = new NameValidator($this->model, $id);
            $errors = $validator->validateName($name);

            if (empty($errors)) {
                if ($this->model->update($id, $name)) {
                    header('Location: /admin/' . $this->entityUrl);
                    exit;
                } else {
                    echo "Failed to update " . $this->entityName;
                }
            } else {
                $item = $this->model->getById($id);
                $data = [
                    $this->entityName => $item,
                    'title' => 'Edit ' . $this->entityName,
                    'errors' => $errors,
                    'entityUrl' => $this->entityUrl
                ];
                $this->render('admin/entity/edit', $data, 'admin');
            }
        }
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $validator = new NameValidator($this->model);
            $errors = $validator->validateName($name);

            if (empty($errors)) {
                if ($this->model->create($name)) {
                    header('Location: /admin/' . $this->entityUrl);
                    exit;
                } else {
                    echo "Failed to create " . $this->entityName;
                }
            } else {
                $this->render('admin/entity/create', [
                    'title' => 'Add ' . $this->entityName,
                    'errors' => $errors,
                    'entityUrl' => $this->entityUrl,
                    'entityName' => $this->entityName
                ], 'admin');
            }
        } else {
            $this->render('admin/entity/create', [
                'title' => 'Add ' . $this->entityName,
                'entityUrl' => $this->entityUrl,
                'entityName' => $this->entityName
            ], 'admin');
        }
    }


    public function delete(int $id): void
    {
        if ($this->model->delete($id)) {
            header('Location: /admin/' . $this->entityUrl);
            exit;
        } else {
            echo "Failed to delete " . $this->entityName;
        }
    }
}
