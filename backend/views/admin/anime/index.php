<h2>Anime List</h2>
<a href="/admin/anime/create" class="btn btn-primary mb-3">Add Anime</a>
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
    <?php foreach ($animeList as $anime): ?>
        <tr>
            <td><?php echo htmlspecialchars($anime['id']); ?></td>
            <td>
                <img src="<?php echo htmlspecialchars($anime['image_url']); ?>" alt="Anime Image" width="50">
            </td>
            <td><?php echo htmlspecialchars($anime['name']); ?></td>
            <td class="text-truncate" style="max-width: 300px;">
                <?php echo htmlspecialchars($anime['description']); ?>
            </td>
            <td><?php echo htmlspecialchars($anime['release_date']); ?></td>
            <td><?php echo htmlspecialchars(number_format($anime['average_rating'], 2)); ?></td>
            <td>
                <a href="/admin/anime/<?php echo $anime['id']; ?>" class="text-decoration-none">
                    <i class="far fa-eye"></i>
                </a>
                <a href="/admin/anime/<?php echo $anime['id']; ?>/edit" class="text-decoration-none">
                    <i class="fas fa-pencil-alt px-4"></i>
                </a>
                <button type="button" class="border-0 bg-transparent text-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-item-name="<?php echo htmlspecialchars($anime['name']); ?>"
                        data-form-action="/admin/anime/<?php echo $anime['id']; ?>/delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
