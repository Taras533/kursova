<?php
require_once "../db/connect_admin.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $birth_date   = $_POST['birth_date'] ?? '';
    $nationality  = trim($_POST['nationality'] ?? '');
    $position     = trim($_POST['position'] ?? '');
    $jersey_number = $_POST['jersey_number'] ?? null;
    $photo_path = null;

    if ($first_name === '' || $last_name === '' || $birth_date === '' || $nationality === '' || $position === '') {
        http_response_code(400);
        exit("Всі поля мають бути заповнені.");
    }

    if (
        !preg_match('/^[\p{L}\s\-]{2,50}$/u', $first_name) ||
        !preg_match('/^[\p{L}\s\-]{2,50}$/u', $last_name) ||
        !preg_match('/^[\p{L}\s\-]{2,50}$/u', $nationality) ||
        !preg_match('/^[\p{L}\s\-]{2,50}$/u', $position)
    ) {
        http_response_code(400);
        exit("Некоректний формат одного з текстових полів.");
    }

    if (!DateTime::createFromFormat('Y-m-d', $birth_date)) {
        http_response_code(400);
        exit("Неправильна дата.");
    }

    if (!preg_match('/^[0-9]{1,2}$/', $jersey_number)) {
        http_response_code(400);
        exit("Номер футболки має бути числом від 1 до 99.");
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_type = mime_content_type($file_tmp);

        if (!in_array($file_type, $allowed_types)) {
            http_response_code(400);
            exit("Недопустимий тип файлу.");
        }

        if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            http_response_code(400);
            exit("Фото завелике. Макс. розмір — 2MB.");
        }

        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid("player_") . "." . $ext;
        $destination = "../../photos/team/" . $new_filename;

        if (!move_uploaded_file($file_tmp, $destination)) {
            http_response_code(500);
            exit("Не вдалося зберегти фото.");
        }

        $photo_path =  $new_filename;
    }

    $stmt = $conn->prepare("INSERT INTO Player (first_name, last_name, birth_date, nationality, position, jersey_number, photo)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $first_name, $last_name, $birth_date, $nationality, $position, $jersey_number, $photo_path);

    if ($stmt->execute()) {
        header("Location: team_manage.php?success=1");
        exit;
    } else {
        http_response_code(500);
        exit("Помилка при збереженні.");
    }
}

http_response_code(405);
exit("Метод не дозволений.");
