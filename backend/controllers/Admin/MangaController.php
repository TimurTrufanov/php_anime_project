<?php

namespace Backend\Controllers\Admin;

use AllowDynamicProperties;
use Backend\Controllers\BaseController;
use Backend\Models\Manga;
use Backend\Models\Genre;
use Backend\Models\Author;
use Backend\Models\Artist;
use Backend\Models\MangaStatuses;
use Backend\Models\Database\Database;
use Backend\Validation\MangaImageUploader;
use Backend\Validation\MaxLengthValidator;
use Backend\Validation\NameValidator;
use Backend\Validation\NumberValidator;
use Backend\Validation\ReleaseDateValidator;
use Backend\Validation\SelectValidator;
use Exception;
use JetBrains\PhpStorm\NoReturn;

#[AllowDynamicProperties]
class MangaController extends BaseController
{
    protected Manga $mangaModel;
    protected Genre $genreModel;
    protected Author $authorModel;
    protected Artist $artistModel;
    protected MangaStatuses $statusModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->mangaModel = new Manga($this->db);
        $this->genreModel = new Genre($this->db);
        $this->authorModel = new Author($this->db);
        $this->artistModel = new Artist($this->db);
        $this->statusModel = new MangaStatuses($this->db);
    }

    private function getCommonData(): array
    {
        return [
            'genres' => $this->genreModel->getAll(),
            'authors' => $this->authorModel->getAll(),
            'artists' => $this->artistModel->getAll(),
            'statuses' => $this->statusModel->getAll()
        ];
    }

    private function getMangaDataFromRequest(string $imagePath): array
    {
        $releaseDate = !empty($_POST['release_date']) ? $_POST['release_date'] : null;

        return [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'release_date' => $releaseDate,
            'image_url' => $imagePath,
            'chapter_count' => $_POST['chapters'] ?? '',
            'status_id' => $_POST['status_id'] ?? null,
        ];
    }

    /**
     * @throws Exception
     */
    #[NoReturn] private function saveAndRedirect(int $mangaId, array $authors, array $artists, array $genres): void
    {
        $this->mangaModel->attachRelations($mangaId, $authors, $artists, $genres);
        header('Location: /admin/manga');
        exit;
    }

    public function index(): void
    {
        $mangaList = $this->mangaModel->getAllWithRatings();
        $this->render('admin/manga/index', ['mangaList' => $mangaList], 'admin');
    }

    /**
     * @throws Exception
     */
    public function create(): void
    {
        $errors = [];
        $mangaData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateAndUpdateManga();

            if (empty($errors)) {
                $mangaData = $this->getMangaDataFromRequest($_POST['image_url']);
                $this->mangaModel->createManga($mangaData);
                $mangaId = $this->db->lastInsertId();
                $this->saveAndRedirect($mangaId, $_POST['authors'] ?? [], $_POST['artists'] ?? [], $_POST['genres'] ?? []);
            } else {
                $mangaData = $_POST;
            }
        }

        $this->render('admin/manga/create', array_merge($this->getCommonData(), ['errors' => $errors, 'manga' => $mangaData]), 'admin');
    }


    public function show($id): void
    {
        $manga = $this->mangaModel->getMangaById($id);
        if (!$manga) {
            http_response_code(404);
            echo "Manga not found";
            return;
        }

        $this->render('admin/manga/show', ['manga' => $manga], 'admin');
    }

    /**
     * @throws Exception
     */
    private function prepareMangaDataForRendering($id): array
    {
        $manga = $this->mangaModel->getMangaById($id);
        if (!$manga) {
            http_response_code(404);
            echo "Manga not found";
            return [];
        }

        $selectedAuthors = $this->mangaModel->getAuthorsByMangaId($id);
        $selectedArtists = $this->mangaModel->getArtistsByMangaId($id);
        $selectedGenres = $this->mangaModel->getGenresByMangaId($id);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateAndUpdateManga($id);
            $selectedAuthors = $_POST['authors'] ?? [];
            $selectedArtists = $_POST['artists'] ?? [];
            $selectedGenres = $_POST['genres'] ?? [];
        }

        return [
            'manga' => $manga,
            'errors' => $errors,
            'selected_authors' => $selectedAuthors,
            'selected_artists' => $selectedArtists,
            'selected_genres' => $selectedGenres,
        ];
    }


    /**
     * @throws Exception
     */
    public function edit($id): void
    {
        $data = $this->prepareMangaDataForRendering($id);
        if ($data) {
            $this->render('admin/manga/edit', array_merge($data, $this->getCommonData()), 'admin');
        }
    }

    /**
     * @throws Exception
     */
    public function update($id): void
    {
        $data = $this->prepareMangaDataForRendering($id);
        if ($data) {
            $this->render('admin/manga/edit', array_merge($data, $this->getCommonData()), 'admin');
        }
    }

    public function delete($id): void
    {
        $manga = $this->mangaModel->getMangaById($id);
        if (!$manga) {
            http_response_code(404);
            echo "Manga not found";
            return;
        }

        if ($manga['image_url'] !== '/uploads/media/image_not_found.jpg') {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . $manga['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->mangaModel->deleteManga($id);
        header('Location: /admin/manga');
        exit;
    }

    /**
     * @throws Exception
     */
    private function validateAndUpdateManga(?int $id = null): array
    {
        $errors = [];

        $nameValidator = new NameValidator($this->mangaModel, $id);
        $nameErrors = $nameValidator->validateName($_POST['name'] ?? '');
        if (!empty($nameErrors)) {
            $errors['name'] = $nameErrors;
        }

        $descriptionValidator = new MaxLengthValidator(65535);
        if (!$descriptionValidator->validate($_POST['description'] ?? '')) {
            $errors['description'] = $descriptionValidator->getErrorMessage();
        }

        $chapterCountValidator = new NumberValidator();
        if (!$chapterCountValidator->validate($_POST['chapters'] ?? '')) {
            $errors['chapters'] = $chapterCountValidator->getErrorMessage();
        }

        $releaseDateValidator = new ReleaseDateValidator();
        if (!$releaseDateValidator->validate($_POST['release_date'] ?? '')) {
            $errors['release_date'] = $releaseDateValidator->getErrorMessage();
        }

        $statusValidator = new SelectValidator($this->statusModel, 'status_id');
        if (!$statusValidator->validate($_POST['status_id'] ?? '')) {
            $errors['status_id'] = $statusValidator->getErrorMessage();
        }

        $authorValidator = new SelectValidator($this->authorModel, 'authors');
        if (isset($_POST['authors'])) {
            foreach ($_POST['authors'] as $authorId) {
                if (!$authorValidator->validate($authorId)) {
                    $errors['authors'][] = $authorValidator->getErrorMessage();
                }
            }
        }

        $artistValidator = new SelectValidator($this->artistModel, 'artists');
        if (isset($_POST['artists'])) {
            foreach ($_POST['artists'] as $artistId) {
                if (!$artistValidator->validate($artistId)) {
                    $errors['artists'][] = $artistValidator->getErrorMessage();
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

        $imageValidator = new MangaImageUploader();
        $newImageUploaded = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($newImageUploaded) {
            if (!$imageValidator->validateFile($_FILES['image'])) {
                $errors['image'] = $imageValidator->getErrorMessage();
            }
        }

        if (empty($errors)) {
            $existingManga = $id ? $this->mangaModel->getMangaById($id) : null;
            $oldImagePath = $existingManga['image_url'] ?? '/uploads/media/image_not_found.jpg';

            $imagePath = $newImageUploaded
                ? $imageValidator->upload($_FILES['image'], $_SERVER['DOCUMENT_ROOT'] . '/uploads/media/manga/')
                : $oldImagePath;

            if ($oldImagePath !== '/uploads/media/image_not_found.jpg' && $oldImagePath !== $imagePath) {
                $oldImageFullPath = $_SERVER['DOCUMENT_ROOT'] . $oldImagePath;
                if (file_exists($oldImageFullPath)) {
                    unlink($oldImageFullPath);
                }
            }

            $mangaData = $this->getMangaDataFromRequest($imagePath);

            if ($id) {
                $this->mangaModel->updateManga($mangaData, $id);
            } else {
                $this->mangaModel->createManga($mangaData);
                $id = $this->db->lastInsertId();
            }

            $this->saveAndRedirect($id, $_POST['authors'] ?? [], $_POST['artists'] ?? [], $_POST['genres'] ?? []);
        }

        return $errors;
    }
}
