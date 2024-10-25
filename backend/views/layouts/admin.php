<?php
ob_start();

$currentUrl = $_SERVER['REQUEST_URI'];

function isActive($url, $currentUrl, $exact = false): bool
{
    if ($exact) {
        return $currentUrl === $url;
    }
    return str_starts_with($currentUrl, $url);
}

?>
    <div class="d-flex flex-grow-1">
        <aside class="sidebar bg-dark text-white p-3" style="width: 250px;">
            <h4>Admin Panel</h4>
            <nav class="nav flex-column">
                <a href="/admin"
                   class="nav-link text-white <?php echo isActive('/admin', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/admin/anime"
                   class="nav-link text-white <?php echo isActive('/admin/anime', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-tv"></i> Anime
                </a>
                <a href="/admin/manga"
                   class="nav-link text-white <?php echo isActive('/admin/manga', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-book"></i> Manga
                </a>
                <a href="/admin/anime-statuses"
                   class="nav-link text-white <?php echo isActive('/admin/anime-statuses', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-list"></i> Anime Statuses
                </a>
                <a href="/admin/manga-statuses"
                   class="nav-link text-white <?php echo isActive('/admin/manga-statuses', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-list"></i> Manga Statuses
                </a>
                <a href="/admin/anime-view-statuses"
                   class="nav-link text-white <?php echo isActive('/admin/anime-view-statuses', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-list"></i> Anime View Statuses
                </a>
                <a href="/admin/manga-read-statuses"
                   class="nav-link text-white <?php echo isActive('/admin/manga-read-statuses', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-list"></i> Manga Read Statuses
                </a>
                <a href="/admin/genres"
                   class="nav-link text-white <?php echo isActive('/admin/genres', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-tags"></i> Genres
                </a>
                <a href="/admin/directors"
                   class="nav-link text-white <?php echo isActive('/admin/directors', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-video"></i> Directors
                </a>
                <a href="/admin/writers"
                   class="nav-link text-white <?php echo isActive('/admin/writers', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-pen-fancy"></i> Writers
                </a>
                <a href="/admin/artists"
                   class="nav-link text-white <?php echo isActive('/admin/artists', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-paint-brush"></i> Artists
                </a>
                <a href="/admin/authors"
                   class="nav-link text-white <?php echo isActive('/admin/authors', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-user-edit"></i> Authors
                </a>
                <a href="/admin/users"
                   class="nav-link text-white <?php echo isActive('/admin/users', $currentUrl, true) ? 'bg-primary' : ''; ?>">
                    <i class="fas fa-user"></i> Users
                </a>
            </nav>
        </aside>

        <div class="content flex-grow-1 p-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/admin">Admin Dashboard</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/admin"><i class="fas fa-user"></i> Admin</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <?php echo $content; ?>
        </div>
    </div>
<?php
$content = ob_get_clean();
$title = 'Admin Panel';

require __DIR__ . '/main.php';
