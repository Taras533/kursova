<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login_user.php");
    exit;
}
require_once '../users/db/connect.php';

$stmt = $conn->prepare("
    SELECT c.message, c.sent_at, u.username
    FROM chat_messages c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.sent_at >= NOW() - INTERVAL 1 DAY
    ORDER BY c.sent_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="card mb-4 shadow-sm">
    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
        <?php if (count($messages) > 0): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="mb-2">
                    <strong><?= htmlspecialchars($msg['username']) ?>:</strong>
                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    <small class="text-muted float-end"><?= htmlspecialchars($msg['sent_at']) ?></small>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted text-center">Немає повідомлень за останню добу.</p>
        <?php endif; ?>
    </div>
</div>