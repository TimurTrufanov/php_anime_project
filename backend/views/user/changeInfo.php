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

    <h2 class="text-center mt-4">User Profile</h2>

    <div class="container mt-5">
        <form action="/change-info" method="POST" enctype="multipart/form-data"
              class="col-md-6 offset-md-3">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>"
                       id="username" name="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>"
                       required>
                <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="birth_date" class="form-label">Birth Date:</label>
                <input type="date" class="form-control <?php echo isset($errors['birth_date']) ? 'is-invalid' : ''; ?>"
                       id="birth_date" name="birth_date"
                       value="<?php echo htmlspecialchars($currentUser['birth_date']); ?>"
                       required>
                <?php if (isset($errors['birth_date'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['birth_date']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="profile_photo" class="form-label">Profile Photo:</label>
                <input type="file"
                       class="form-control <?php echo isset($errors['profile_photo']) ? 'is-invalid' : ''; ?>"
                       id="profile_photo" name="profile_photo" accept="image/png, image/jpeg, image/gif">
                <?php if (isset($errors['profile_photo'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['profile_photo']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="user_avatar" width="150"
                     height="150" class="rounded-circle mb-3">
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn btn-primary w-100">Update Profile</button>
        </form>
    </div>
</div>

