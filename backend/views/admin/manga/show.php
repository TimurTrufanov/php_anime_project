<h2><?php echo htmlspecialchars($manga['name']); ?></h2>
<table class="table" style="max-width: 700px">
    <tr>
        <th>Id</th>
        <td class="w-75"><?php echo htmlspecialchars($manga['id']); ?></td>
    </tr>
    <tr>
        <th>Image</th>
        <td class="w-75"><img src="<?php echo htmlspecialchars($manga['image_url']); ?>" alt="Image" width="200"></td>
    </tr>
    <?php if (!empty($manga['description'])): ?>
        <tr>
            <th>Description</th>
            <td class="w-75"><?php echo htmlspecialchars($manga['description']); ?></td>
        </tr>
    <?php endif; ?>

    <?php if (!empty($manga['release_date'])): ?>
        <tr>
            <th>Release Date</th>
            <td class="w-75"><?php echo htmlspecialchars($manga['release_date']); ?></td>
        </tr>
    <?php endif; ?>
</table>
<a href="/admin/manga/<?php echo $manga['id']; ?>/edit" class="btn btn-primary">Edit</a>
<button type="button" class="btn btn-danger"
        data-bs-toggle="modal"
        data-bs-target="#deleteModal"
        data-item-name="<?php echo htmlspecialchars($manga['name']); ?>"
        data-form-action="/admin/manga/<?php echo $manga['id']; ?>/delete">
    Delete
</button>