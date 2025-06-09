<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Вхід фаната</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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

<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="w-100" style="max-width: 500px;">




            <form method="post" action="login_user_process.php" class="p-4 bg-white border rounded shadow-sm">
                <h2 class="mb-4 text-center">Вхід фаната</h2>
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($_SESSION['login_error']) ?>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="mb-3">
                    <label class="form-label">Імʼя користувача:</label>
                    <input type="text" name="username" class="form-control" placeholder="Введіть логін" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль:</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Введіть пароль" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">click me</button>
                    </div>
                </div>
                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary knopka">Увійти</button>
                    <a href="register_user.php" class="btn btn-secondary mt-2 knopka">Реєстрація</a>
                    <a href="/kursova/index.php" class="btn btn-secondary mt-2 knopka">На головну</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById("password");
            pwd.type = pwd.type === "password" ? "text" : "password";
        }
    </script>
</body>

</html>