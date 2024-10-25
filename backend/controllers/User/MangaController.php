<?php

namespace Backend\Controllers\User;

use Backend\Controllers\BaseController;
use Backend\Models\Manga;
use Backend\Models\MangaCommentLikes;
use Backend\Models\MangaRatings;
use Backend\Models\MangaComments;
use Backend\Models\MangaStatuses;
use Backend\Models\MangaUserStatuses;
use Backend\Models\MangaReadStatuses;
use Backend\Models\Database\Database;
use Backend\Validation\SelectValidator;

class MangaController extends BaseController
{
    protected Manga $mangaModel;
    protected MangaStatuses $mangaStatusesModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->mangaModel = new Manga($this->db);
        $this->mangaStatusesModel = new MangaStatuses($this->db);
    }

    public function index(): void
    {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 24;

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filter = $_GET['filter'] ?? '';
        $sort = $_GET['sort'] ?? 'created_at';

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $totalMangaCount = $this->mangaModel->getTotalMangaCount($search, $filter);
        $totalPages = ceil($totalMangaCount / $itemsPerPage);

        if ($totalMangaCount === 0) {
            $currentPage = 1;
        }

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $itemsPerPage;

        if ($offset < 0) {
            $offset = 0;
        }

        $mangaList = $this->mangaModel->getMangaWithPagination($itemsPerPage, $offset, $search, $filter, $sort);

        $statusModel = new MangaStatuses($this->db);
        $allStatuses = $statusModel->getAll();

        $this->render('user/manga/index', [
            'mangaList' => $mangaList,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'title' => 'Manga',
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
            'allStatuses' => $allStatuses,
        ]);
    }


    public function show($id): void
    {
        $manga = $this->mangaModel->getMangaById($id);
        if (!$manga) {
            http_response_code(404);
            echo "Manga not found";
            return;
        }

        $ratings = $this->mangaModel->getRatings($id);
        $manga['averageRating'] = round($ratings['average'], 2);
        $manga['totalRatings'] = $ratings['count'] ?? 0;

        $authors = $this->mangaModel->getAuthorNamesByMangaId($id);
        $artists = $this->mangaModel->getArtistNamesByMangaId($id);
        $genres = $this->mangaModel->getGenreNamesByMangaId($id);

        $mangaStatus = $this->mangaStatusesModel->getStatusById($manga['status_id']);

        $commentModel = new MangaComments($this->db);
        $comments = $commentModel->getCommentsByMangaId($id);

        $readStatusModel = new MangaReadStatuses($this->db);
        $readStatuses = $readStatusModel->getAll();

        $currentStatus = null;
        if (isset($_SESSION['user_id'])) {
            $userStatusModel = new MangaUserStatuses($this->db);
            $currentStatus = $userStatusModel->getStatusByUserIdAndMangaId($_SESSION['user_id'], $id);
        }

        $likeModel = new MangaCommentLikes($this->db);

        $this->render('user/manga/show', [
            'manga' => $manga,
            'authors' => $authors,
            'artists' => $artists,
            'genres' => $genres,
            'mangaStatus' => $mangaStatus,
            'comments' => $comments,
            'readStatuses' => $readStatuses,
            'currentStatus' => $currentStatus,
            'likeModel' => $likeModel,
        ]);
    }

    public function rate(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $mangaId = $_POST['manga_id'];
            $score = (int)$_POST['score'];

            if ($score >= 1 && $score <= 10) {
                $ratingModel = new MangaRatings($this->db);
                $success = $ratingModel->rateManga($userId, $mangaId, $score);

                if ($success) {
                    header("Location: /manga/$mangaId");
                    exit;
                } else {
                    echo "Failed to submit rating.";
                }
            } else {
                echo "Invalid rating.";
            }
        }
    }

    public function addComment(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $mangaId = $_POST['manga_id'];
            $text = trim($_POST['text']);

            if (!empty($text)) {
                $commentModel = new MangaComments($this->db);
                $success = $commentModel->addComment($userId, $mangaId, $text);

                if ($success) {
                    header("Location: /manga/$mangaId");
                    exit;
                } else {
                    echo "Failed to submit comment.";
                }
            } else {
                $_SESSION['error'] = "Comment cannot be empty.";
                header("Location: /manga/$mangaId");
                exit;
            }
        }
    }

    public function deleteComment(int $commentId): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentModel = new MangaComments($this->db);
            $comment = $commentModel->getCommentById($commentId);

            if ($comment && ($comment['user_id'] == $_SESSION['user_id'] || $_SESSION['role_id'] == 2)) {
                $success = $commentModel->deleteComment($commentId);

                if ($success) {
                    header("Location: /manga/{$comment['manga_id']}");
                    exit;
                } else {
                    echo "Failed to delete comment.";
                }
            } else {
                echo "You do not have permission to delete this comment.";
            }
        }
    }

    public function setReadStatus(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $mangaId = $_POST['manga_id'];
            $statusId = (int)$_POST['status_id'];

            $statusValidator = new SelectValidator($this->mangaStatusesModel);

            if (!$statusValidator->validate($statusId)) {
                $_SESSION['error'] = $statusValidator->getErrorMessage();
                header("Location: /manga/$mangaId");
                exit;
            }

            $userStatusModel = new MangaUserStatuses($this->db);
            $success = $userStatusModel->setStatus($userId, $mangaId, $statusId);

            if ($success) {
                header("Location: /manga/$mangaId");
                exit;
            } else {
                echo "Failed to set status.";
            }
        }
    }

    public function toggleLikeComment(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $commentId = $_POST['comment_id'];

            $likeModel = new MangaCommentLikes($this->db);

            if ($likeModel->userLikedComment($commentId, $userId)) {
                $likeModel->removeLike($commentId, $userId);
            } else {
                $likeModel->addLike($commentId, $userId);
            }

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}