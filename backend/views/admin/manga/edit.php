<h2>Edit Manga</h2>
<form action="/admin/manga/<?php echo $manga['id']; ?>" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name"
               class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
               value="<?php echo htmlspecialchars($manga['name']); ?>">
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
        ><?php echo htmlspecialchars($manga['description']); ?></textarea>
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
               value="<?php echo htmlspecialchars($manga['release_date']); ?>">
        <?php if (isset($errors['release_date'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['release_date']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="image">Current Image</label>
        <div>
            <img src="<?php echo htmlspecialchars($manga['image_url']); ?>" alt="Manga Image" width="150">
        </div>
        <label for="image">Replace Image</label>
        <input type="file" id="image" name="image"
               class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>"
               accept="image/png, image/jpeg, image/gif">
        <?php if (isset($errors['image'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['image']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="chapters">Chapters</label>
        <input type="text" id="chapters" name="chapters"
               class="form-control <?php echo isset($errors['chapters']) ? 'is-invalid' : ''; ?>"
               value="<?php echo htmlspecialchars($manga['chapter_count']); ?>">
        <?php if (isset($errors['chapters'])): ?>
            <div class="invalid-feedback">
                <?php echo htmlspecialchars($errors['chapters']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="status_id">Status</label>
        <select id="status_id" name="status_id"
                class="form-control <?php echo isset($errors['status_id']) ? 'is-invalid' : ''; ?>">>
            <option value="" disabled <?php echo empty($manga['status_id']) ? 'selected' : ''; ?>>Select a status
            </option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?php echo $status['id']; ?>" <?php echo ($manga['status_id'] == $status['id']) ? 'selected' : ''; ?>>
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
        <label for="authors">Authors</label>
        <select id="authors" name="authors[]"
                class="form-control <?php echo isset($errors['authors']) ? 'is-invalid' : ''; ?>" multiple>
            <?php foreach ($authors as $author): ?>
                <option value="<?php echo $author['id']; ?>"
                    <?php echo (isset($selected_authors) && in_array($author['id'], $selected_authors)) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($author['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['authors'])): ?>
            <div class="invalid-feedback">
                <?php echo implode(', ', $errors['authors']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="artists">Artists</label>
        <select id="artists" name="artists[]"
                class="form-control <?php echo isset($errors['artists']) ? 'is-invalid' : ''; ?>" multiple>
            <?php foreach ($artists as $artist): ?>
                <option value="<?php echo $artist['id']; ?>"
                    <?php echo (isset($selected_artists) && in_array($artist['id'], $selected_artists)) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($artist['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['artists'])): ?>
            <div class="invalid-feedback">
                <?php echo implode(', ', $errors['artists']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="genres">Genres</label>
        <select id="genres" name="genres[]"
                class="form-control <?php echo isset($errors['genres']) ? 'is-invalid' : ''; ?>" multiple>
            <?php foreach ($genres as $genre): ?>
                <option value="<?php echo $genre['id']; ?>"
                    <?php echo (isset($selected_genres) && in_array($genre['id'], $selected_genres)) ? 'selected' : ''; ?>>
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

    <button type="submit" class="btn btn-primary mt-3">Update</button>
</form>
