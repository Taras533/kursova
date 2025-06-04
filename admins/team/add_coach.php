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

function validate_text($value)
{
    $value = trim($value ?? '');
    return (preg_match('/^[\p{L}\s\-]{2,50}$/u', $value)) ? $value : null;
}

$first_name = validate_text($_POST['first_name'] ?? '');
$last_name  = validate_text($_POST['last_name'] ?? '');
$position   = validate_text($_POST['position'] ?? '');

if (!$first_name || !$last_name || !$position) {
    http_response_code(400);
    exit("Некоректні або відсутні дані.");
}

$photo_path = null;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $file_tmp  = $_FILES['photo']['tmp_name'];
    $file_type = mime_content_type($file_tmp);

    if (!in_array($file_type, $allowed_types)) {
        http_response_code(400);
        exit("Недопустимий тип файлу.");
    }

    if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
        http_response_code(400);
        exit("Фото перевищує 2MB.");
    }

    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid("coach_") . "." . $ext;
    $destination = "../../photos/team/" . $new_filename;

    if (!move_uploaded_file($file_tmp, $destination)) {
        http_response_code(500);
        exit("Не вдалося зберегти фото.");
    }

    $photo_path = $new_filename;
}

$stmt = $conn->prepare("INSERT INTO CoachStaff (first_name, last_name, position, photo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $first_name, $last_name, $position, $photo_path);

if ($stmt->execute()) {
    header("Location: team_manage.php?success=1");
    exit;
} else {
    http_response_code(500);
    exit("Помилка збереження у базу.");
}
