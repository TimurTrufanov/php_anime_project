<h2><?php echo htmlspecialchars($entityName); ?> List</h2>
<a href="/admin/<?php echo $entityUrl; ?>/create" class="btn btn-primary mb-3">Add <?php echo htmlspecialchars($entityName); ?></a>
<table class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['id']); ?></td>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td>
                <a href="/admin/<?php echo $entityUrl; ?>/<?php echo $item['id']; ?>" class="text-decoration-none">
                    <i class="far fa-eye"></i>
                </a>
                <a href="/admin/<?php echo $entityUrl; ?>/<?php echo $item['id']; ?>/edit" class="text-decoration-none">
                    <i class="fas fa-pencil-alt px-4"></i>
                </a>
                <button type="button" class="border-0 bg-transparent text-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                        data-form-action="/admin/<?php echo $entityUrl; ?>/<?php echo $item['id']; ?>/delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
