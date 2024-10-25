<h2>Add Anime</h2>
<form action="/admin/anime/create" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name"
               class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
               value="<?php echo htmlspecialchars($anime['name'] ?? ''); ?>">
        <?php if (isset($errors['name'])): ?>
            <div class="invalid-feedback">
                <?php echo implode(', ', $errors['name']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" style="resize: none;"
                  class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>"
        ><?php echo htmlspecialchars($anime['description'] ?? ''); ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['description']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="release_date">Release Date</label>
        <input type="date" id="release_date" name="release_date"
               class="form-control <?php echo isset($errors['release_date']) ? 'is-invalid' : ''; ?>"
               value="<?php echo htmlspecialchars($anime['release_date'] ?? ''); ?>">
        <?php if (isset($errors['release_date'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['release_date']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="image">Image</label>
        <input type="file" name="image" id="image"
               class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>"
               accept="image/png, image/jpeg, image/gif">
        <?php if (isset($errors['image'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['image']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="trailer">Trailer</label>
        <input type="text" id="trailer" name="trailer"
               class="form-control <?php echo isset($errors['trailer']) ? 'is-invalid' : ''; ?>"
               value="<?php echo htmlspecialchars($anime['trailer'] ?? ''); ?>">
        <?php if (isset($errors['trailer'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['trailer']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="episode_duration">Episode Duration</label>
        <input type="text" id="episode_duration" name="episode_duration"
               class="form-control <?php echo isset($errors['episode_duration']) ? 'is-invalid' : ''; ?>"
               value="<?php echo htmlspecialchars($anime['episode_duration'] ?? ''); ?>">
        <?php if (isset($errors['episode_duration'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['episode_duration']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="status_id">Status</label>
        <select id="status_id" name="status_id"
                class="form-control <?php echo isset($errors['status_id']) ? 'is-invalid' : ''; ?>">
            <option value="" disabled selected>Select a status</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?php echo $status['id']; ?>"
                    <?php echo (isset($anime['status_id']) && $anime['status_id'] == $status['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($status['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['status_id'])): ?>
            <div class="invalid-feedback">
                <?php echo $errors['status_id']; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="directors">Directors</label>
        <select id="directors" name="directors[]"
                class="form-control <?php echo isset($errors['directors']) ? 'is-invalid' : ''; ?>" multiple>
            <?php foreach ($directors as $director): ?>
                <option value="<?php echo $director['id']; ?>"
                    <?php echo (isset($anime['directors']) && in_array($director['id'], $anime['directors'])) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($director['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['directors'])): ?>
            <div class="invalid-feedback">
                <?php echo implode(', ', $errors['directors']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="writers">Writers</label>
        <select id="writers" name="writers[]"
                class="form-control <?php echo isset($errors['writers']) ? 'is-invalid' : ''; ?>" multiple>
            <?php foreach ($writers as $writer): ?>
                <option value="<?php echo $writer['id']; ?>"
                    <?php echo (isset($anime['writers']) && in_array($writer['id'], $anime['writers'])) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($writer['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['writers'])): ?>
            <div class="invalid-feedback">
                <?php echo implode(', ', $errors['writers']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="genres">Genres</label>
        <select id="genres" name="genres[]"
                class="form-control <?php echo isset($errors['genres']) ? 'is-invalid' : ''; ?>" multiple>
            <?php foreach ($genres as $genre): ?>
                <option value="<?php echo $genre['id']; ?>"
                    <?php echo (isset($anime['genres']) && in_array($genre['id'], $anime['genres'])) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($genre['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['genres'])): ?>
            <div class="invalid-feedback">
                <?php echo implode(', ', $errors['genres']); ?>
            </div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Create</button>
</form>