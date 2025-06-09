<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
session_start();
require_once '../users/db/connect.php';
// include "../../kursova/includes/header.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Імʼя користувача має бути від 3 до 50 символів.';
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/', $password)) {
        $errors[] = 'Пароль має містити щонайменше 8 символів, зокрема одну велику літеру, одну малу літеру, одну цифру та один спеціальний символ.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Користувач з таким імʼям вже існує.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $passwordHash);

            if ($insert->execute()) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $insert->insert_id;
                $_SESSION['username'] = $username;
                header("Location: ../matches.php");
                exit;
            } else {
                $errors[] = 'Помилка при створенні акаунта. Спробуйте ще раз.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Реєстрація користувача</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../photos/background_to_register.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .bg-white {
            background-color: rgba(255, 255, 255, 0.95) !important;
        }

        .knopka {
            transition: transform 0.2s ease-out;
        }

        .knopka:hover {
            transform: scale3d(1.05, 1.05, 1.05);
        }
    </style>
</head>

<body class="bg-light">
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="w-100" style="max-width: 500px;">


            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="p-4 bg-white border rounded shadow-sm">
                <h2 class="mb-4 text-center">Реєстрація фаната</h2>
                <div class="mb-3">
                    <label for="username" class="form-label">Імʼя користувача</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Введіть імʼя користувача" required value="<?= isset($username) ? htmlspecialchars($username, ENT_QUOTES, 'UTF-8') : '' ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Введіть складний пароль" required oninput="validatePassword(this.value)">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            click me
                        </button>
                    </div>
                    <div class="form-text">Пароль має відповідати наступним вимогам:</div>
                    <ul class="mt-2 small list-unstyled" id="password-checklist">
                        <li><input type="checkbox" id="len" disabled> Щонайменше 8 символів</li>
                        <li><input type="checkbox" id="low" disabled> Містить малу літеру</li>
                        <li><input type="checkbox" id="up" disabled> Містить велику літеру</li>
                        <li><input type="checkbox" id="num" disabled> Містить цифру</li>
                        <li><input type="checkbox" id="spec" disabled> Містить спецсимвол (!@#...)</li>
                    </ul>
                </div>

                <div class="text-center mt-3 d-flex justify-content-center flex-wrap">
                    <button type="submit" class="btn btn-primary knopka me-2 mb-2">Зареєструватися</button>
                    <a href="../../kursova/users/login_user.php" class="btn btn-primary knopka me-2 mb-2">Вхід</a>
                    <a href="/kursova/index.php" class="btn btn-secondary knopka mb-2">На головну</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validatePassword(password) {
            setCheckbox('len', password.length >= 8);
            setCheckbox('low', /[a-z]/.test(password));
            setCheckbox('up', /[A-Z]/.test(password));
            setCheckbox('num', /[0-9]/.test(password));
            setCheckbox('spec', /[^A-Za-z0-9]/.test(password));
        }

        function setCheckbox(id, isValid) {
            document.getElementById(id).checked = isValid;
        }

        function togglePassword() {
            const pwd = document.getElementById("password");
            pwd.type = pwd.type === "password" ? "text" : "password";
        }
    </script>
</body>

</html>