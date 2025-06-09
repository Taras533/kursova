<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login_user.php");
    exit;
}

require_once '../users/db/connect.php';
include "../../kursova/includes/header.php";

// Обробка надсилання повідомлення
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $message = strip_tags($message); // видаляє HTML

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $message);
        $stmt->execute();
    }

    // Після вставки: redirect, щоб уникнути дублювання
    header("Location: chat.php");
    exit;
}

// Отримання повідомлень за останню добу
$stmt = $conn->prepare("SELECT c.message, c.sent_at, u.username 
                        FROM chat_messages c 
                        JOIN users u ON c.user_id = u.user_id 
                        WHERE c.sent_at >= NOW() - INTERVAL 1 DAY 
                        ORDER BY c.sent_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Чат фанатів</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-center mb-4">Чат фанатів</h2>

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

        <form method="post" class="card p-3 shadow-sm">
            <div class="mb-3">
                <label for="message" class="form-label">Ваше повідомлення</label>
                <textarea name="message" id="message" rows="3" class="form-control" required maxlength="1000" placeholder="Напишіть щось..."></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Надіслати</button>
            </div>
        </form>
    </div>
</body>

</html>