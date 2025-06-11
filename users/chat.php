<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login_user.php");
    exit;
}

require_once '../users/db/connect.php';
include "../../kursova/includes/header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $message = strip_tags($message);

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $message);
        $stmt->execute();
    }

    header("Location: chat.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Чат фанатів</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        #chat-messages-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .chat-msg.my-msg {
            background-color: #e7f7e7;
            border-left: 4px solid #28a745;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-center mb-4">Чат фанатів</h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-body" id="chat-messages-container">
                Завантаження повідомлень...
            </div>
        </div>

        <form method="post" class="card p-3 shadow-sm">
            <div class="mb-3">
                <label for="message" class="form-label">Ваше повідомлення</label>
                <textarea name="message" id="message" rows="3" class="form-control" required maxlength="1000"
                    placeholder="Напишіть щось..."></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Надіслати</button>
            </div>
        </form>
    </div>

    <script>
        function fetchMessages() {
            fetch('get_messages.php')
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.text();
                })
                .then(html => {
                    const container = document.getElementById('chat-messages-container');
                    const atBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 10;
                    container.innerHTML = html;
                    if (atBottom) {
                        container.scrollTop = container.scrollHeight;
                    }
                })
                .catch(err => console.error('Помилка завантаження чату:', err));
        }

        fetchMessages();
        setInterval(fetchMessages, 10000);
    </script>
</body>

</html>