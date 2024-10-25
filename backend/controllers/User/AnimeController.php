<?php

namespace Backend\Controllers\User;

use Backend\Controllers\BaseController;
use Backend\Models\Anime;
use Backend\Models\AnimeCommentLikes;
use Backend\Models\AnimeRatings;
use Backend\Models\AnimeComments;
use Backend\Models\AnimeStatuses;
use Backend\Models\AnimeUserStatuses;
use Backend\Models\AnimeViewStatuses;
use Backend\Models\Database\Database;
use Backend\Validation\SelectValidator;

class AnimeController extends BaseController
{
    protected Anime $animeModel;
    protected AnimeStatuses $animeStatusesModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->animeModel = new Anime($this->db);
        $this->animeStatusesModel = new AnimeStatuses($this->db);
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

        $totalAnimeCount = $this->animeModel->getTotalAnimeCount($search, $filter);
        $totalPages = ceil($totalAnimeCount / $itemsPerPage);

        if ($totalAnimeCount === 0) {
            $currentPage = 1;
        }

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $itemsPerPage;

        if ($offset < 0) {
            $offset = 0;
        }

        $animeList = $this->animeModel->getAnimeWithPagination($itemsPerPage, $offset, $search, $filter, $sort);

        $statusModel = new AnimeStatuses($this->db);
        $allStatuses = $statusModel->getAll();

        $this->render('user/anime/index', [
            'animeList' => $animeList,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'title' => 'Anime',
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
            'allStatuses' => $allStatuses,
        ]);
    }


    public function show($id): void
    {
        $anime = $this->animeModel->getAnimeById($id);
        if (!$anime) {
            http_response_code(404);
            echo "Anime not found";
            return;
        }

        $ratings = $this->animeModel->getRatings($id);
        $anime['averageRating'] = round($ratings['average'], 2);
        $anime['totalRatings'] = $ratings['count'] ?? 0;

        $directors = $this->animeModel->getDirectorNamesByAnimeId($id);
        $writers = $this->animeModel->getWriterNamesByAnimeId($id);
        $genres = $this->animeModel->getGenreNamesByAnimeId($id);

        $animeStatus = $this->animeStatusesModel->getStatusById($anime['status_id']);

        $commentModel = new AnimeComments($this->db);
        $comments = $commentModel->getCommentsByAnimeId($id);

        $viewStatusModel = new AnimeViewStatuses($this->db);
        $viewStatuses = $viewStatusModel->getAll();

        $currentStatus = null;
        if (isset($_SESSION['user_id'])) {
            $userStatusModel = new AnimeUserStatuses($this->db);
            $currentStatus = $userStatusModel->getStatusByUserIdAndAnimeId($_SESSION['user_id'], $id);
        }

        $likeModel = new AnimeCommentLikes($this->db);

        $this->render('user/anime/show', [
            'anime' => $anime,
            'directors' => $directors,
            'writers' => $writers,
            'genres' => $genres,
            'animeStatus' => $animeStatus,
            'comments' => $comments,
            'viewStatuses' => $viewStatuses,
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
            $animeId = $_POST['anime_id'];
            $score = (int)$_POST['score'];

            if ($score >= 1 && $score <= 10) {
                $ratingModel = new AnimeRatings($this->db);
                $success = $ratingModel->rateAnime($userId, $animeId, $score);

                if ($success) {
                    header("Location: /anime/$animeId");
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
            $animeId = $_POST['anime_id'];
            $text = trim($_POST['text']);

            if (!empty($text)) {
                $commentModel = new AnimeComments($this->db);
                $success = $commentModel->addComment($userId, $animeId, $text);

                if ($success) {
                    header("Location: /anime/$animeId");
                    exit;
                } else {
                    echo "Failed to submit comment.";
                }
            } else {
                $_SESSION['error'] = "Comment cannot be empty.";
                header("Location: /anime/$animeId");
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
            $commentModel = new AnimeComments($this->db);
            $comment = $commentModel->getCommentById($commentId);

            if ($comment && ($comment['user_id'] == $_SESSION['user_id'] || $_SESSION['role_id'] == 2)) {
                $success = $commentModel->deleteComment($commentId);

                if ($success) {
                    header("Location: /anime/{$comment['anime_id']}");
                    exit;
                } else {
                    echo "Failed to delete comment.";
                }
            } else {
                echo "You do not have permission to delete this comment.";
            }
        }
    }

    public function setViewStatus(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $animeId = $_POST['anime_id'];
            $statusId = (int)$_POST['status_id'];

            $statusValidator = new SelectValidator($this->animeStatusesModel);

            if (!$statusValidator->validate($statusId)) {
                $_SESSION['error'] = $statusValidator->getErrorMessage();
                header("Location: /anime/$animeId");
                exit;
            }

            $userStatusModel = new AnimeUserStatuses($this->db);
            $success = $userStatusModel->setStatus($userId, $animeId, $statusId);

            if ($success) {
                header("Location: /anime/$animeId");
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

            $likeModel = new AnimeCommentLikes($this->db);

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