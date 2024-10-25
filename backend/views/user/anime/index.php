<div class="container">
    <h2>Anime</h2>

    <form method="GET" action="">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group flex-column w-100">
                    <label for="search">Search</label>
                    <div class="input-group w-100">
                        <input id="search" type="text" class="form-control" name="search"
                               placeholder="Search by name..."
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group flex-column w-100">
                    <label for="filter">Filter</label>
                    <select name="filter" id="filter" class="form-select w-100" onchange="this.form.submit()">
                        <option value="">All</option>
                        <?php foreach ($allStatuses as $status): ?>
                            <option value="<?php echo $status['id']; ?>" <?php echo $filter === (string)$status['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group flex-column w-100">
                    <label for=sort>Sort</label>
                    <select name="sort" id="sort" class="form-select w-100" onchange="this.form.submit()">
                        <option value="created_at" <?php echo $sort === 'created_at' ? 'selected' : ''; ?>>Date Added
                        </option>
                        <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name</option>
                        <option value="release_date" <?php echo $sort === 'release_date' ? 'selected' : ''; ?>>Release
                            Date
                        </option>
                        <option value="average_rating" <?php echo $sort === 'average_rating' ? 'selected' : ''; ?>>
                            Average
                            Rating
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <?php foreach ($animeList as $anime): ?>
            <div class="col-md-3">
                <div class="card mb-4">
                    <img src="<?php echo htmlspecialchars($anime['image_url']); ?>" class="card-img-top"
                         alt="<?php echo htmlspecialchars($anime['name']); ?>" height="400px">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($anime['name']); ?></h5>
                        <?php if (!empty($anime['release_date'])): ?>
                            <p class="card-text">Release
                                Date: <?php echo htmlspecialchars($anime['release_date']); ?></p>
                        <?php endif; ?>
                        <?php if ($anime['average_rating'] > 0): ?>
                            <p class="card-text">
                                <strong>Rating:</strong> <?php echo htmlspecialchars($anime['average_rating']) ?></p>
                        <?php endif; ?>
                        <a href="/anime/<?php echo $anime['id']; ?>" class="btn btn-outline-primary w-100">View
                            Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link"
                       href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&filter=<?= htmlspecialchars($filter) ?>&sort=<?= htmlspecialchars($sort) ?>"
                       aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            if ($currentPage > 3) {
                echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '&filter=' . htmlspecialchars($filter) . '&sort=' . htmlspecialchars($sort) . '">1</a></li>';
                if ($currentPage > 4) {
                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                }
            }

            for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <?php if ($i === $currentPage): ?>
                    <li class="page-item active"><a class="page-link" href="#"><?= $i ?></a></li>
                <?php else: ?>
                    <li class="page-item"><a class="page-link"
                                             href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= htmlspecialchars($filter) ?>&sort=<?= htmlspecialchars($sort) ?>"><?= $i ?></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>

            <?php
            if ($currentPage < $totalPages - 2) {
                if ($currentPage < $totalPages - 3) {
                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&search=' . urlencode($search) . '&filter=' . htmlspecialchars($filter) . '&sort=' . htmlspecialchars($sort) . '">' . $totalPages . '</a></li>';
            }
            ?>

            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link"
                       href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&filter=<?= htmlspecialchars($filter) ?>&sort=<?= htmlspecialchars($sort) ?>"
                       aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
