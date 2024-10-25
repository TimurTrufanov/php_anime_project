<div class="flex-grow-1 container">
    <h1><?php echo htmlspecialchars($welcomeMessage); ?></h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <?php else: ?>
        <p>Click the buttons to register or login:</p>
    <?php endif; ?>
</div>
