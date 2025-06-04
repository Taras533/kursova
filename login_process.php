<?php
session_start();
require '../kursova/db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
        $_SESSION['login_error'] = "Помилка безпеки: Відсутній CSRF-токен.";
        header("Location: login.php");
        exit;
    }

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['login_error'] = "Помилка безпеки: Недійсний CSRF-токен.";
        header("Location: login.php");
        exit;
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username)) {
        $_SESSION['login_error'] = "Ім'я користувача не може бути порожнім.";
        header("Location: login.php");
        exit;
    }
    if (empty($password)) {
        $_SESSION['login_error'] = "Пароль не може бути порожнім.";
        header("Location: login.php");
        exit;
    }

    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $_SESSION['login_error'] = "Неправильні облікові дані.";
        header("Location: login.php");
        exit;
    }
    /*
    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        die("Неправильні облікові дані.");
    }
    */

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();


        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $id;
            session_regenerate_id(true);
            header("Location: ../kursova/admins/adminPanel.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Неправильні облікові дані.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "Неправильні облікові дані.";
        header("Location: login.php");
        exit;
    }

    $stmt->close();
}




/*
if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();


    if (password_verify($password, $hashed_password)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $id;
        session_regenerate_id(true);
        header("Location: /admins/adminPanel.php");
        exit;
    } else {
        echo "Неправильні облікові дані";
    }
} else {
    echo "Неправильні облікові дані";
}
    */