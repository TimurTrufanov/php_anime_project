<h2><?php echo htmlspecialchars($anime['name']); ?></h2>
<table class="table" style="max-width: 700px">
    <tr>
        <th>Id</th>
        <td class="w-75"><?php echo htmlspecialchars($anime['id']); ?></td>
    </tr>
    <tr>
        <th>Image</th>
        <td class="w-75"><img src="<?php echo htmlspecialchars($anime['image_url']); ?>" alt="Image" width="200"></td>
    </tr>
    <?php if (!empty($anime['description'])): ?>
        <tr>
            <th>Description</th>
            <td class="w-75"><?php echo htmlspecialchars($anime['description']); ?></td>
        </tr>
    <?php endif; ?>

    <?php if (!empty($anime['release_date'])): ?>
        <tr>
            <th>Release Date</th>
            <td class="w-75"><?php echo htmlspecialchars($anime['release_date']); ?></td>
        </tr>
    <?php endif; ?>
</table>
<a href="/admin/anime/<?php echo $anime['id']; ?>/edit" class="btn btn-primary">Edit</a>
<button type="button" class="btn btn-danger"
        data-bs-toggle="modal"
        data-bs-target="#deleteModal"
        data-item-name="<?php echo htmlspecialchars($anime['name']); ?>"
        data-form-action="/admin/anime/<?php echo $anime['id']; ?>/delete">
    Delete
</button>