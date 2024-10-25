<?php

namespace Backend\Controllers\Admin;

use AllowDynamicProperties;
use Backend\Controllers\BaseController;
use Backend\Models\Anime;
use Backend\Models\Genre;
use Backend\Models\Director;
use Backend\Models\Writer;
use Backend\Models\AnimeStatuses;
use Backend\Models\Database\Database;
use Backend\Validation\AnimeImageUploader;
use Backend\Validation\MaxLengthValidator;
use Backend\Validation\NameValidator;
use Backend\Validation\ReleaseDateValidator;
use Backend\Validation\SelectValidator;
use Exception;
use JetBrains\PhpStorm\NoReturn;

#[AllowDynamicProperties]
class AnimeController extends BaseController
{
    protected Anime $animeModel;
    protected Genre $genreModel;
    protected Director $directorModel;
    protected Writer $writerModel;
    protected AnimeStatuses $statusModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->animeModel = new Anime($this->db);
        $this->genreModel = new Genre($this->db);
        $this->directorModel = new Director($this->db);
        $this->writerModel = new Writer($this->db);
        $this->statusModel = new AnimeStatuses($this->db);
    }

    private function getCommonData(): array
    {
        return [
            'genres' => $this->genreModel->getAll(),
            'directors' => $this->directorModel->getAll(),
            'writers' => $this->writerModel->getAll(),
            'statuses' => $this->statusModel->getAll()
        ];
    }

    private function getAnimeDataFromRequest(string $imagePath): array
    {
        $releaseDate = !empty($_POST['release_date']) ? $_POST['release_date'] : null;

        return [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'release_date' => $releaseDate,
            'image_url' => $imagePath,
            'trailer_url' => $_POST['trailer'] ?? '',
            'status_id' => $_POST['status_id'] ?? null,
            'episode_duration' => $_POST['episode_duration'] ?? null
        ];
    }

    /**
     * @throws Exception
     */
    #[NoReturn] private function saveAndRedirect(int $animeId, array $directors, array $writers, array $genres): void
    {
        $this->animeModel->attachRelations($animeId, $directors, $writers, $genres);
        header('Location: /admin/anime');
        exit;
    }

    public function index(): void
    {
        $animeList = $this->animeModel->getAllWithRatings();
        $this->render('admin/anime/index', ['animeList' => $animeList], 'admin');
    }

    /**
     * @throws Exception
     */
    public function create(): void
    {
        $errors = [];
        $animeData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateAndUpdateAnime();

            if (empty($errors)) {
                $animeData = $this->getAnimeDataFromRequest($_POST['image_url']);
                $this->animeModel->createAnime($animeData);
                $animeId = $this->db->lastInsertId();
                $this->saveAndRedirect($animeId, $_POST['directors'] ?? [], $_POST['writers'] ?? [], $_POST['genres'] ?? []);
            } else {
                $animeData = $_POST;
            }
        }

        $this->render('admin/anime/create', array_merge($this->getCommonData(), ['errors' => $errors, 'anime' => $animeData]), 'admin');
    }


    public function show($id): void
    {
        $anime = $this->animeModel->getAnimeById($id);
        if (!$anime) {
            http_response_code(404);
            echo "Anime not found";
            return;
        }

        $this->render('admin/anime/show', ['anime' => $anime], 'admin');
    }

    /**
     * @throws Exception
     */
    private function prepareAnimeDataForRendering($id): array
    {
        $anime = $this->animeModel->getAnimeById($id);
        if (!$anime) {
            http_response_code(404);
            echo "Anime not found";
            return [];
        }

        $selectedDirectors = $this->animeModel->getDirectorsByAnimeId($id);
        $selectedWriters = $this->animeModel->getWritersByAnimeId($id);
        $selectedGenres = $this->animeModel->getGenresByAnimeId($id);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateAndUpdateAnime($id);
            $selectedDirectors = $_POST['directors'] ?? [];
            $selectedWriters = $_POST['writers'] ?? [];
            $selectedGenres = $_POST['genres'] ?? [];
        }

        return [
            'anime' => $anime,
            'errors' => $errors,
            'selected_directors' => $selectedDirectors,
            'selected_writers' => $selectedWriters,
            'selected_genres' => $selectedGenres,
        ];
    }


    /**
     * @throws Exception
     */
    public function edit($id): void
    {
        $data = $this->prepareAnimeDataForRendering($id);
        if ($data) {
            $this->render('admin/anime/edit', array_merge($data, $this->getCommonData()), 'admin');
        }
    }

    /**
     * @throws Exception
     */
    public function update($id): void
    {
        $data = $this->prepareAnimeDataForRendering($id);
        if ($data) {
            $this->render('admin/anime/edit', array_merge($data, $this->getCommonData()), 'admin');
        }
    }

    public function delete($id): void
    {
        $anime = $this->animeModel->getAnimeById($id);
        if (!$anime) {
            http_response_code(404);
            echo "Anime not found";
            return;
        }

        if ($anime['image_url'] !== '/uploads/media/image_not_found.jpg') {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . $anime['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->animeModel->deleteAnime($id);
        header('Location: /admin/anime');
        exit;
    }

    /**
     * @throws Exception
     */
    private function validateAndUpdateAnime(?int $id = null): array
    {
        $errors = [];

        $nameValidator = new NameValidator($this->animeModel, $id);
        $nameErrors = $nameValidator->validateName($_POST['name'] ?? '');
        if (!empty($nameErrors)) {
            $errors['name'] = $nameErrors;
        }

        $descriptionValidator = new MaxLengthValidator(65535);
        if (!$descriptionValidator->validate($_POST['description'] ?? '')) {
            $errors['description'] = $descriptionValidator->getErrorMessage();
        }

        $trailerValidator = new MaxLengthValidator(1024);
        if (!$trailerValidator->validate($_POST['trailer'] ?? '')) {
            $errors['trailer'] = $trailerValidator->getErrorMessage();
        }

        $episodeDurationValidator = new MaxLengthValidator(10);
        if (!$episodeDurationValidator->validate($_POST['episode_duration'] ?? '')) {
            $errors['episode_duration'] = $episodeDurationValidator->getErrorMessage();
        }

        $releaseDateValidator = new ReleaseDateValidator();
        if (!$releaseDateValidator->validate($_POST['release_date'] ?? '')) {
            $errors['release_date'] = $releaseDateValidator->getErrorMessage();
        }

        $statusValidator = new SelectValidator($this->statusModel, 'status_id');
        if (!$statusValidator->validate($_POST['status_id'] ?? '')) {
            $errors['status_id'] = $statusValidator->getErrorMessage();
        }

        $directorValidator = new SelectValidator($this->directorModel, 'directors');
        if (isset($_POST['directors'])) {
            foreach ($_POST['directors'] as $directorId) {
                if (!$directorValidator->validate($directorId)) {
                    $errors['directors'][] = $directorValidator->getErrorMessage();
                }
            }
        }

        $writerValidator = new SelectValidator($this->writerModel, 'writers');
        if (isset($_POST['writers'])) {
            foreach ($_POST['writers'] as $writerId) {
                if (!$writerValidator->validate($writerId)) {
                    $errors['writers'][] = $writerValidator->getErrorMessage();
                }
            }
        }

        $genreValidator = new SelectValidator($this->genreModel, 'genres');
        if (isset($_POST['genres'])) {
            foreach ($_POST['genres'] as $genreId) {
                if (!$genreValidator->validate($genreId)) {
                    $errors['genres'][] = $genreValidator->getErrorMessage();
                }
            }
        }

        $imageValidator = new AnimeImageUploader();
        $newImageUploaded = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($newImageUploaded) {
            if (!$imageValidator->validateFile($_FILES['image'])) {
                $errors['image'] = $imageValidator->getErrorMessage();
            }
        }

        if (empty($errors)) {
            $existingAnime = $id ? $this->animeModel->getAnimeById($id) : null;
            $oldImagePath = $existingAnime['image_url'] ?? '/uploads/media/image_not_found.jpg';

            $imagePath = $newImageUploaded
                ? $imageValidator->upload($_FILES['image'], $_SERVER['DOCUMENT_ROOT'] . '/uploads/media/anime/')
                : $oldImagePath;

            if ($oldImagePath !== '/uploads/media/image_not_found.jpg' && $oldImagePath !== $imagePath) {
                $oldImageFullPath = $_SERVER['DOCUMENT_ROOT'] . $oldImagePath;
                if (file_exists($oldImageFullPath)) {
                    unlink($oldImageFullPath);
                }
            }

            $animeData = $this->getAnimeDataFromRequest($imagePath);

            if ($id) {
                $this->animeModel->updateAnime($animeData, $id);
            } else {
                $this->animeModel->createAnime($animeData);
                $id = $this->db->lastInsertId();
            }

            $this->saveAndRedirect($id, $_POST['directors'] ?? [], $_POST['writers'] ?? [], $_POST['genres'] ?? []);
        }

        return $errors;
    }
}
