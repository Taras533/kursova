<?php
require_once "../db/connect_admin.php";
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Метод не дозволений.");
}

$source     = $_POST['source'] ?? '';
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$position   = trim($_POST['position'] ?? '');

if ($first_name === '' || $last_name === '' || $position === '') {
    http_response_code(400);
    exit("Недостатньо даних для видалення.");
}

if (!in_array($source, ['player', 'coach'])) {
    http_response_code(400);
    exit("Невідоме джерело.");
}

$table = $source === 'player' ? 'Player' : 'CoachStaff';

$stmt = $conn->prepare("DELETE FROM $table WHERE first_name = ? AND last_name = ? AND position = ? LIMIT 1");
$stmt->bind_param("sss", $first_name, $last_name, $position);

if ($stmt->execute()) {
    header("Location: team_manage.php?success=2");
    exit;
} else {
    http_response_code(500);
    exit("Помилка при видаленні.");
}
