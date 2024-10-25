<h2>Manga List</h2>
<a href="/admin/manga/create" class="btn btn-primary mb-3">Add Manga</a>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Id</th>
        <th>Image</th>
        <th>Name</th>
        <th>Description</th>
        <th>Release Date</th>
        <th>Average Rating</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($mangaList as $manga): ?>
        <tr>
            <td><?php echo htmlspecialchars($manga['id']); ?></td>
            <td>
                <img src="<?php echo htmlspecialchars($manga['image_url']); ?>" alt="Manga Image" width="50">
            </td>
            <td><?php echo htmlspecialchars($manga['name']); ?></td>
            <td class="text-truncate" style="max-width: 300px;">
                <?php echo htmlspecialchars($manga['description']); ?>
            </td>
            <td><?php echo htmlspecialchars($manga['release_date']); ?></td>
            <td><?php echo htmlspecialchars(number_format($manga['average_rating'], 2)); ?></td>
            <td>
                <a href="/admin/manga/<?php echo $manga['id']; ?>" class="text-decoration-none">
                    <i class="far fa-eye"></i>
                </a>
                <a href="/admin/manga/<?php echo $manga['id']; ?>/edit" class="text-decoration-none">
                    <i class="fas fa-pencil-alt px-4"></i>
                </a>
                <button type="button" class="border-0 bg-transparent text-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-item-name="<?php echo htmlspecialchars($manga['name']); ?>"
                        data-form-action="/admin/manga/<?php echo $manga['id']; ?>/delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
