<h2><?php echo htmlspecialchars($entityName); ?></h2>
<table class="table">
    <tr>
        <th>ID</th>
        <td><?php echo htmlspecialchars($item['id']); ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
    </tr>
</table>
<a href="/admin/<?php echo $entityUrl; ?>/<?php echo $item['id']; ?>/edit" class="btn btn-success">Edit</a>
<button type="button" class="btn btn-danger"
        data-bs-toggle="modal"
        data-bs-target="#deleteModal"
        data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
        data-form-action="/admin/<?php echo $entityUrl; ?>/<?php echo $item['id']; ?>/delete">
    Delete
</button>
