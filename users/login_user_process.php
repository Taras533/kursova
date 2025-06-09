<?php
session_start();
require_once '../users/db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['login_error'] = "Недійсний CSRF-токен.";
        header("Location: login_user.php");
        exit;
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Будь ласка, заповніть усі поля.";
        header("Location: login_user.php");
        exit;
    }

    if (!preg_match("/^[a-zA-Z0-9_]{3,50}$/", $username)) {
        $_SESSION['login_error'] = "Невірний формат логіна.";
        header("Location: login_user.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            session_regenerate_id(true);
            header("Location: chat.php");
            exit;
        }
    }

    $_SESSION['login_error'] = "Неправильні облікові дані.";
    header("Location: login_user.php");
    exit;
}
