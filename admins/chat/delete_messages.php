<?php
session_start();
require_once "../db/connect_admin.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Invalid request method.";
    exit;
}

$message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;

if ($message_id <= 0) {
    http_response_code(400);
    echo "Invalid message ID.";
    exit;
}

$stmt = $conn->prepare("DELETE FROM chat_messages WHERE message_id = ?");
$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    echo "Успішно";
} else {
    http_response_code(500);
    echo "Помилка.";
}

$stmt->close();
$conn->close();
