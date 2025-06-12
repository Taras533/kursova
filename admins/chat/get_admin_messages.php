<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Access denied');
}

require_once "../db/connect_admin.php";

$stmt = $conn->prepare("
    SELECT c.message_id, c.message, c.sent_at, u.username
    FROM chat_messages c
    LEFT JOIN users u ON c.user_id = u.user_id
    WHERE c.sent_at >= NOW() - INTERVAL 1 DAY
    ORDER BY c.sent_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<head>
    <style>
        .chat-msg {
            padding: 12px;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 10px;
            position: relative;
        }

        .delete-btn {
            position: absolute;
            top: 8px;
            right: 12px;
            color: red;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .delete-btn:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<?php if (count($messages) > 0): ?>
    <?php foreach ($messages as $msg): ?>
        <div class="chat-msg position-relative border bg-light-subtle">
            <strong><?= htmlspecialchars($msg['username'] ?? 'Адмін') ?>:</strong>
            <span><?= nl2br(htmlspecialchars($msg['message'])) ?></span>
            <small class="text-muted d-block mt-1"><?= htmlspecialchars($msg['sent_at']) ?></small>

            <span class="delete-btn" onclick="deleteMessage(<?= (int)$msg['message_id'] ?>)" title="Видалити">
                🗑
            </span>
        </div>

    <?php endforeach; ?>
<?php else: ?>
    <p class="text-muted text-center">Немає повідомлень за останню добу.</p>
<?php endif; ?>