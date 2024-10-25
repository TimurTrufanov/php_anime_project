<h2>Users List</h2>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Avatar</th>
        <th>Username</th>
        <th>Email</th>
        <th>Birth Date</th>
        <th>Role</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td>
                <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="Avatar" width="50" height="50" class="rounded-circle">
            </td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['birth_date']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
