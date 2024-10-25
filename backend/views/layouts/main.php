<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title><?php echo $title ?? 'Default Title'; ?></title>
</head>
<body class="d-flex flex-column min-vh-100">

<header class="sticky-top d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 border-bottom px-4 bg-white shadow-sm">
    <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
        <li><a href="/" class="nav-link px-2 link-secondary">Main page</a></li>
        <li><a href="/anime" class="nav-link px-2 link-dark">Anime</a></li>
        <li><a href="/manga" class="nav-link px-2 link-dark">Manga</a></li>
    </ul>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="col-md-3 text-end d-flex align-items-center justify-content-end flex-grow-1">
            <a href="/change-info">
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="User Avatar" width="40"
                     height="40" class="rounded-circle me-2">
            </a>
            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
                <a href="/admin" class="btn btn-outline-primary me-2">Admin cabinet</a>
            <?php endif; ?>
            <a href="/change-info" class="btn btn-outline-primary me-2">Personal cabinet</a>
            <form action="/logout" method="POST">
                <button type="submit" class="btn btn-primary">Logout</button>
            </form>
        </div>
    <?php else: ?>
        <div class="col-md-3 text-end">
            <a href="/login" class="btn btn-outline-primary me-2">Login</a>
            <a href="/register" class="btn btn-primary">Sign-up</a>
        </div>
    <?php endif; ?>
</header>

<main class="flex-grow-1 d-flex">
    <?php echo $content; ?>
</main>

<footer class="bg-dark bg-gradient text-white py-3 mt-auto">
    <div class="text-center">
        &copy; 2024 My Anime Website
    </div>
</footer>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="itemName"></strong>?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const itemName = button.getAttribute('data-item-name');
        const formAction = button.getAttribute('data-form-action');
        const itemNameElement = deleteModal.querySelector('#itemName');
        const deleteForm = deleteModal.querySelector('#deleteForm');
        itemNameElement.textContent = itemName;
        deleteForm.action = formAction;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>
</html>