<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
include "../kursova/includes/header.php";
?>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../kursova/styles/login.css">

<div class="container py-5 d-flex justify-content-center">
    <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
        <?php
        if (isset($_SESSION['login_error'])) {
            echo '<div class="alert alert-danger text-center" role="alert">';
            echo htmlspecialchars($_SESSION['login_error']);
            echo '</div>';
            unset($_SESSION['login_error']);
        }
        ?>
        <h4 class="text-center mb-4">Вхід в адмін-панель</h4>
        <form method="post" action="login_process.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="mb-3">
                <label class="form-label">Логін користувача:</label>
                <input type="text" name="username" class="form-control" placeholder="Введіть логін" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль:</label>
                <input type="password" name="password" class="form-control" placeholder="Введіть пароль" required>
            </div>
            <div class="d-grid">
                <input type="submit" value="Увійти" class="btn btn-primary knopka">
                <a href="../kursova/users/register_user.php" class="btn btn-secondary knopka" style="margin-top: 10px;">Реєстрація</a>
            </div>
        </form>
    </div>
</div>

<?php
include "../kursova/includes/footer.php";
?>