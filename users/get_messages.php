<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    http_response_code(403);
    exit('Unauthorized');
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

<?php if (count($messages) > 0): ?>
    <?php foreach ($messages as $msg): ?>
        <?php
        $isMine = isset($_SESSION['username']) && $_SESSION['username'] === $msg['username'];
        $msgClass = $isMine ? 'chat-msg my-msg' : 'chat-msg';
        ?>
        <div class="mb-2 <?= $msgClass ?>">
            <strong><?= htmlspecialchars($msg['username']) ?>:</strong>
            <?= nl2br(htmlspecialchars($msg['message'])) ?>
            <small class="text-muted float-end"><?= htmlspecialchars($msg['sent_at']) ?></small>
        </div>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-muted text-center">Немає повідомлень за останню добу.</p>
<?php endif; ?>