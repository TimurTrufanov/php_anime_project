<h2>Edit <?php echo htmlspecialchars($entityName); ?></h2>
<form action="/admin/<?php echo $entityUrl; ?>/<?php echo $item['id']; ?>" method="POST">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" name="name"
               id="name" value="<?php echo htmlspecialchars($item['name']); ?>">
        <?php if (isset($errors['name'])): ?>
            <div class="invalid-feedback">
                <?php echo $errors['name']; ?>
            </div>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary mt-2">Update</button>
</form>
