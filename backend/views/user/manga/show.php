<div class="container">
    <div class="row mt-4">
        <div class="col-md-3">
            <img src="<?php echo htmlspecialchars($manga['image_url']); ?>"
                 alt="<?php echo htmlspecialchars($manga['name']); ?>" class="img-fluid" width="100%">

            <div class="text-center">
                <h4>Rating:</h4>
                <div>
                    <?php
                    $averageRating = $manga['averageRating'];
                    for ($i = 1; $i <= 10; $i++): ?>
                        <span class="star" data-score="<?= $i ?>"
                              style="font-size: 24px; cursor: <?php echo isset($_SESSION['user_id']) ? 'pointer' : 'default'; ?>; color: <?php echo $i <= $averageRating ? 'orange' : 'gray'; ?>">
                            ★
                        </span>
                    <?php endfor; ?>
                </div>
                <span><?php echo htmlspecialchars($manga['averageRating']) ?> (<span
                            id="rating-count"><?php echo htmlspecialchars($manga['totalRatings']); ?></span> ratings)</span>

                <form id="rating-form" method="POST" action="/manga/rate">
                    <input type="hidden" id="score-input" name="score" value="">
                    <input type="hidden" name="manga_id" value="<?php echo $manga['id']; ?>">
                </form>
            </div>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-warning mt-4">
                    <strong>Please log in or register to set your viewing status.</strong>
                </div>
            <?php else: ?>
                <div class="mt-4">
                    <h3>Select Read Status:</h3>
                    <form method="POST" action="/manga/set-read-status">
                        <input type="hidden" name="manga_id" value="<?php echo $manga['id']; ?>">
                        <div class="form-group">
                            <label for="status">Read Status</label>
                            <select id="status" name="status_id"
                                    class="form-control <?php echo isset($errors['error']) ? 'is-invalid' : ''; ?>">
                                <?php foreach ($readStatuses as $status): ?>
                                    <option value="<?php echo $status['id']; ?>"
                                        <?php if ($currentStatus == $status['id']): ?> selected <?php endif; ?>>
                                        <?php echo htmlspecialchars($status['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="mt-2 alert alert-danger">
                                    <?php echo htmlspecialchars($_SESSION['error']); ?>
                                    <?php unset($_SESSION['error']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="w-100 btn btn-primary mt-2">Set Status</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-9">
            <h2><?php echo htmlspecialchars($manga['name']); ?></h2>

            <?php if (!empty($manga['description'])): ?>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($manga['description']); ?></p>
            <?php endif; ?>

            <?php if (!empty($manga['release_date'])): ?>
                <p><strong>Release Date:</strong> <?php echo htmlspecialchars($manga['release_date']); ?></p>
            <?php endif; ?>

            <?php if (!empty($manga['chapters'])): ?>
                <p><strong>Chapters: </strong> <?php echo htmlspecialchars($manga['chapters']); ?></p>
            <?php endif; ?>

            <?php if (!empty($mangaStatus['name'])): ?>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($mangaStatus['name']); ?></p>
            <?php endif; ?>

            <?php if (!empty($authors)): ?>
                <p><strong>Authors:</strong> <?php echo htmlspecialchars(implode(', ', $authors)); ?></p>
            <?php endif; ?>

            <?php if (!empty($artists)): ?>
                <p><strong>Artists:</strong> <?php echo htmlspecialchars(implode(', ', $artists)); ?></p>
            <?php endif; ?>

            <?php if (!empty($genres)): ?>
                <p><strong>Genres:</strong> <?php echo htmlspecialchars(implode(', ', $genres)); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mx-4">
        <div class="mt-4">
            <h3>Leave a Comment:</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="/manga/add-comment">
                    <input type="hidden" name="manga_id" value="<?php echo $manga['id']; ?>">
                    <div class="form-group">
                        <label for="text">Add Comment</label>
                        <textarea id="text" name="text" class="form-control" style="resize: none;" rows="3"></textarea>
                    </div>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger mt-2">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary mt-2">Submit</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Please log in or register to leave a comment.</strong>
                </div>
            <?php endif; ?>
        </div>

        <h3 class="mt-4 mb-2">Comments:</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="d-flex gap-3">
                <img src="<?php echo htmlspecialchars($comment['avatar_url']); ?>" alt="User Avatar" width="50"
                     height="50" class="rounded-circle">
                <div class="w-100" style="max-width: calc(100% - 130px);">
                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                    <small><?php echo htmlspecialchars($comment['created_at']); ?></small>
                    <p class="mb-2" style="word-wrap: break-word;">
                        <?php echo htmlspecialchars($comment['text']); ?>
                    </p>
                    <div>
                        <form method="POST" action="/manga/toggle-like-comment" style="display:inline;">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit" class="btn p-0" style="text-decoration: none; cursor: <?php echo isset($_SESSION['user_id']) ? 'pointer' : 'default'; ?>;">
                                <i class="fa-heart <?php echo (isset($_SESSION['user_id']) && $likeModel->userLikedComment($comment['id'], $_SESSION['user_id'])) ? 'fa-solid' : 'fa-regular'; ?>"
                                   style="color: red;"></i>
                                <?php echo $likeModel->getLikesCount($comment['id']) > 0 ? $likeModel->getLikesCount($comment['id']) : '0'; ?>
                            </button>
                        </form>
                    </div>
                </div>
                <?php if (($comment['user_id'] == ($_SESSION['user_id'] ?? null)) || (($_SESSION['role_id'] ?? null) == 2)): ?>
                    <div>
                        <button type="button" class="btn btn-link text-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-item-name="this comment"
                                data-form-action="/manga/delete-comment/<?php echo $comment['id']; ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
        star.addEventListener('click', function () {
            document.getElementById('score-input').value = this.dataset.score;
            document.getElementById('rating-form').submit();
        });
    });
</script>
