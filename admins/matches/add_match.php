<?php
session_start();
require_once "../db/connect_admin.php";
include "../includes/headerAdmin.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = trim($_POST['date'] ?? '');
    $stadium_id = (int)($_POST['stadium_id'] ?? 0);
    $home_team_id = (int)($_POST['home_team_id'] ?? 0);
    $away_team_id = (int)($_POST['away_team_id'] ?? 0);
    $score_home = $_POST['score_home'] !== '' ? (int)$_POST['score_home'] : null;
    $score_away = $_POST['score_away'] !== '' ? (int)$_POST['score_away'] : null;
    $status = trim($_POST['status'] ?? '');
    $tournament_id = (int)($_POST['tournament_id'] ?? 0);

    $errors = [];

    if (!strtotime($date)) $errors[] = 'Невірна дата.';
    if (!$stadium_id || !$home_team_id || !$away_team_id || !$tournament_id) $errors[] = 'Усі поля повинні бути заповнені.';
    if ($home_team_id === $away_team_id) $errors[] = 'Домашня та гостьова команда не можуть бути однаковими.';
    if (!in_array($status, ['planned', 'finished'])) $errors[] = 'Невірний статус матчу.';

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO Matches (date, stadium_id, home_team_id, away_team_id, score_home, score_away, status, tournament_id)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiiissi", $date, $stadium_id, $home_team_id, $away_team_id, $score_home, $score_away, $status, $tournament_id);

        if ($stmt->execute()) {
            header("Location: add_match.php?success=1");
            exit;
        } else {
            $errors[] = 'Помилка при додаванні матчу.';
        }
    }
}

$stadiums = $conn->query("SELECT stadium_id, stadium_name FROM Stadiums ORDER BY stadium_name")->fetch_all(MYSQLI_ASSOC);
$teams = $conn->query("SELECT team_id, team_name FROM Teams ORDER BY team_name")->fetch_all(MYSQLI_ASSOC);
$tournaments = $conn->query("SELECT tournament_id, name, season FROM Tournament ORDER BY name, season")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Додати матч</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .knopka {
            transition: transform 0.2s ease-out;
        }

        .knopka:hover {
            transform: scale3d(1.05, 1.05, 1.05);
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <div class="mx-auto" style="max-width: 600px; margin-bottom: 80px;">

            <form method="post" class="card p-4 shadow-sm bg-white">
                <h2 class="mb-4 text-center">Додати новий матч</h2>
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="alert alert-success">
                        Матч успішно додано!
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="date" class="form-label">Дата та час</label>
                    <input type="datetime-local" class="form-control" name="date" required>
                </div>

                <div class="mb-3">
                    <label for="stadium_id" class="form-label">Стадіон</label>
                    <select class="form-select" name="stadium_id" required>
                        <option value="">Оберіть стадіон</option>
                        <?php foreach ($stadiums as $s): ?>
                            <option value="<?= $s['stadium_id'] ?>"><?= htmlspecialchars($s['stadium_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Домашня команда</label>
                    <select class="form-select" name="home_team_id" required>
                        <option value="">Оберіть команду</option>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= $t['team_id'] ?>"><?= htmlspecialchars($t['team_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Гостьова команда</label>
                    <select class="form-select" name="away_team_id" required>
                        <option value="">Оберіть команду</option>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= $t['team_id'] ?>"><?= htmlspecialchars($t['team_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Рахунок домашніх</label>
                    <input type="number" name="score_home" class="form-control" min="0" max="99">
                </div>

                <div class="mb-3">
                    <label class="form-label">Рахунок гостей</label>
                    <input type="number" name="score_away" class="form-control" min="0" max="99">
                </div>

                <div class="mb-3">
                    <label class="form-label">Статус</label>
                    <select name="status" class="form-select" required>
                        <option value="">Оберіть статус</option>
                        <option value="planned">Запланований</option>
                        <option value="finished">Завершений</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Турнір</label>
                    <select class="form-select" name="tournament_id" required>
                        <option value="">Оберіть турнір</option>
                        <?php foreach ($tournaments as $t): ?>
                            <option value="<?= $t['tournament_id'] ?>"><?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['season']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success knopka">Додати матч</button>
                    <a href="matches.php" class="btn btn-secondary knopka">Скасувати</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const url = new URL(window.location);
            if (url.searchParams.has("success")) {
                url.searchParams.delete("success");
                window.history.replaceState({}, document.title, url.pathname);
            }
        }, 3000);
    </script>
</body>

</html>