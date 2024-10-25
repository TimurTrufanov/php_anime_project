<div class="container flex-grow-1 my-4">
    <nav class="nav nav-pills flex-column flex-sm-row mb-4">
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Change User Info') ? 'active' : ''; ?>"
           href="/change-info">Change User Info</a>
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Anime Statuses') ? 'active' : ''; ?>"
           href="/user-anime-statuses">View Anime Statuses</a>
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Manga Statuses') ? 'active' : ''; ?>"
           href="/user-manga-statuses">View Manga Statuses</a>
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Change Password') ? 'active' : ''; ?>"
           href="/change-password">Change Password</a>
    </nav>

    <h2 class="mt-4 text-center">Your Anime View Statuses:</h2>

    <?php if (empty($statuses)): ?>
        <p>List is empty.</p>
    <?php else: ?>
        <?php foreach ($statuses as $statusName => $animes): ?>
            <h4><?php echo htmlspecialchars($statusName); ?>:</h4>
            <ol>
                <?php if (empty($animes)): ?>
                    <li>list is empty</li>
                <?php else: ?>
                    <?php foreach ($animes as $anime): ?>
                        <li>
                            <div class="d-flex justify-content-between">
                                <a class="text-decoration-none text-dark" href="/anime/<?php echo $anime['anime_id']; ?>">
                                    <?php echo htmlspecialchars($anime['title']); ?>
                                </a>
                                Rating: <?php echo htmlspecialchars($anime['rating']); ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ol>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
