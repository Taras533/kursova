<?php
session_start();
require_once "../db/connect_admin.php";
include "../includes/headerAdmin.php";


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit("Access denied.");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $message = strip_tags($message);
    $csrf = $_POST['csrf_token'] ?? '';

    if (hash_equals($_SESSION['csrf_token'], $csrf) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
        $admin_id = 0;
        $stmt->bind_param("is", $admin_id, $message);
        $stmt->execute();
        header("Location: admin_chat.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Адмінський чат</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        #chat-messages-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .chat-msg {
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 10px;
            position: relative;
        }

        .chat-msg .delete-btn {
            position: absolute;
            top: 5px;
            right: 10px;
            color: #dc3545;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-center mb-4">Адмінська модерація чату</h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-body" id="chat-messages-container">
                Завантаження повідомлень...
            </div>
        </div>

        <form method="post" class="card p-3 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="mb-3">
                <label for="message" class="form-label">Нове повідомлення</label>
                <textarea name="message" id="message" rows="3" class="form-control" required maxlength="1000"
                    placeholder="Введіть повідомлення від імені адміністратора..."></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Надіслати</button>
            </div>
        </form>
    </div>

    <script>
        function fetchAdminMessages() {
            fetch('get_admin_messages.php')
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.text();
                })
                .then(html => {
                    const container = document.getElementById('chat-messages-container');
                    const atBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 10;
                    container.innerHTML = html;
                    if (atBottom) container.scrollTop = container.scrollHeight;
                })
                .catch(err => console.error('Помилка завантаження чату:', err));
        }

        fetchAdminMessages();
        setInterval(fetchAdminMessages, 10000);

        function deleteMessage(messageId) {
            if (!confirm('Ви дійсно хочете видалити це повідомлення?')) return;
            fetch('delete_messages.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    message_id: messageId,
                    csrf_token: '<?= $_SESSION['csrf_token'] ?>'
                })
            }).then(fetchAdminMessages);
        }
    </script>
</body>

</html>