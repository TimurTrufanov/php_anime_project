<h2>Add <?php echo htmlspecialchars($entityName); ?></h2>
<form action="/admin/<?php echo $entityUrl; ?>/create" method="POST">
    <div class="form-group">
        <label for="name" class="form-label">Name</label>
        <input type="text" id="name" name="name" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>">
        <?php if (isset($errors['name'])): ?>
            <div class="invalid-feedback">
                <?php echo $errors['name']; ?>
            </div>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary mt-2">Create</button>
</form>
