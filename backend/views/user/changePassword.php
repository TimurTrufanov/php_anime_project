<div class="container flex-grow-1 my-4">
    <nav class="nav nav-pills flex-column flex-sm-row mb-4">
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Change User Info') ? 'active' : ''; ?>"
           href="/change-info">Change User Info</a>
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Anime Statuses') ? 'active' : ''; ?>"
           href="/user-anime-statuses">View Anime Statuses</a>
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Manga Statuses') ? 'active' : ''; ?>"
           href="/user-manga-statuses">View Manga Statuses</a>
        <a class="flex-sm-fill text-sm-center nav-link <?php echo ($title === 'Change Password') ? 'active' : ''; ?>"
           href="/change-password">Change Password</a>
    </nav>

    <h2 class="text-center mt-4">Change Password</h2>

    <div class="container mt-5">
        <?php if ($success): ?>
            <div class="alert alert-success">Password successfully updated!</div>
        <?php endif; ?>

        <form action="/change-password" method="POST" class="col-md-6 offset-md-3">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password:</label>
                <input type="password"
                       class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>"
                       id="current_password" name="current_password" autocomplete="on">
                <?php if (isset($errors['current_password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['current_password']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password:</label>
                <input type="password"
                       class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>"
                       id="new_password" name="new_password" autocomplete="on">
                <?php if (isset($errors['new_password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['new_password']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password:</label>
                <input type="password"
                       class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                       id="confirm_password" name="confirm_password" autocomplete="on">
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
            <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn btn-primary w-100">Change Password</button>
        </form>
    </div>
</div>
