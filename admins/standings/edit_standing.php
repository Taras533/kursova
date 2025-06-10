<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../db/connect_admin.php";
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit("Access denied.");
}

function get_uint($key)
{
    return (isset($_POST[$key]) && ctype_digit($_POST[$key])) ? (int)$_POST[$key] : null;
}

$id            = get_uint('id');
$wins          = get_uint('wins');
$draws         = get_uint('draws');
$losses        = get_uint('losses');
$goals_for     = get_uint('goals_for');
$goals_against = get_uint('goals_against');

if ($id === null || $wins === null || $draws === null || $losses === null || $goals_for === null || $goals_against === null) {
    http_response_code(400);
    exit("Недійсні або неповні дані.");
}

$stmt = $conn->prepare("
    UPDATE Standings
    SET wins = ?, draws = ?, losses = ?, goals_for = ?, goals_against = ?
    WHERE id = ?
");

if ($stmt === false) {
    http_response_code(500);
    exit("Помилка підготовки запиту: " . $conn->error);
}

$stmt->bind_param("iiiiii", $wins, $draws, $losses, $goals_for, $goals_against, $id);

if ($stmt->execute()) {
    header("Location: standings_manage.php?success=1");
    exit;
} else {
    http_response_code(500);
    exit("Помилка оновлення запису: " . $stmt->error);
}

$stmt->close();
$conn->close();
