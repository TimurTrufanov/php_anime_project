<div class="flex-grow-1 mb-4">
    <h2 class="text-center mt-4">Register</h2>
    <div class="container mt-5">
        <form action="/register" method="POST" class="col-md-6 offset-md-3">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>"
                       id="username" name="username"
                       value="<?php echo isset($formData['username']) ? htmlspecialchars($formData['username']) : ''; ?>"
                       required>
                <?php if (isset($errors['username'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                       id="email" name="email"
                       value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>"
                       required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="birth_date" class="form-label">Birth Date:</label>
                <input type="date" class="form-control <?php echo isset($errors['birth_date']) ? 'is-invalid' : ''; ?>"
                       id="birth_date" name="birth_date"
                       value="<?php echo isset($formData['birth_date']) ? htmlspecialchars($formData['birth_date']) : ''; ?>"
                       required>
                <?php if (isset($errors['birth_date'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['birth_date']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                       id="password" name="password" autocomplete="on" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password:</label>
                <input type="password"
                       class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                       id="confirm_password" name="confirm_password" autocomplete="on" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>

