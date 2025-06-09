<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../users/db/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_logged_in'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Неавторизований доступ']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Метод не дозволений']);
    exit;
}

$message = trim($_POST['message'] ?? '');

if (empty($message) || strlen($message) > 1000) {
    echo json_encode(['status' => 'error', 'message' => 'Повідомлення не може бути порожнім або надто довгим']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO chat_messages (user_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $_SESSION['user_id'], $message);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Повідомлення надіслано']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Помилка при збереженні']);
}
