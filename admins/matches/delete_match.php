<?php
session_start();
require_once "../db/connect_admin.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Метод не дозволений.");
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    exit("Недійсний CSRF токен.");
}

$match_id = (int)($_POST['match_id'] ?? 0);
if ($match_id <= 0) {
    http_response_code(400);
    exit("Невірний ID матчу.");
}

$stmt = $conn->prepare("DELETE FROM Matches WHERE match_id = ?");
$stmt->bind_param("i", $match_id);

if ($stmt->execute()) {
    header("Location: matches.php?deleted=1");
    exit;
} else {
    http_response_code(500);
    exit("Не вдалося видалити запис.");
}
