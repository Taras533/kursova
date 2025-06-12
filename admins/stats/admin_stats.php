<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "../db/connect_admin.php";
include "../includes/headerAdmin.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit("Access denied.");
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $player_id = (int)($_POST['player_id'] ?? 0);
    $match_id = (int)($_POST['match_id'] ?? 0);
    $goals = max(0, (int)($_POST['goals'] ?? 0));
    $assists = max(0, (int)($_POST['assists'] ?? 0));
    $yellow_cards = max(0, (int)($_POST['yellow_cards'] ?? 0));
    $red_cards = max(0, (int)($_POST['red_cards'] ?? 0));
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;

    if ($player_id === 0 || $match_id === 0) {
        $errors[] = "Гравець та матч повинні бути обрані.";
    }

    if (empty($errors)) {
        if ($edit_id) {
            $stmt = $conn->prepare("UPDATE PlayerStats SET player_id = ?, match_id = ?, goals = ?, assists = ?, yellow_cards = ?, red_cards = ? WHERE stat_id = ?");
            $stmt->bind_param("iiiiiii", $player_id, $match_id, $goals, $assists, $yellow_cards, $red_cards, $edit_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO PlayerStats (player_id, match_id, goals, assists, yellow_cards, red_cards) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiii", $player_id, $match_id, $goals, $assists, $yellow_cards, $red_cards);
        }

        if (!$stmt->execute()) {
            $errors[] = "Помилка БД: можливо, запис вже існує.";
        } else {
            header("Location: admin_stats.php?success=1");
            exit;
        }
    }
}

$players = $conn->query("SELECT player_id, first_name, last_name FROM Player ORDER BY last_name")->fetch_all(MYSQLI_ASSOC);
$matches = $conn->query("SELECT match_id, DATE(date) as date FROM Matches WHERE status = 'finished' ORDER BY date DESC")->fetch_all(MYSQLI_ASSOC);
$stats = $conn->query("SELECT ps.*, p.first_name, p.last_name, DATE(m.date) AS date FROM PlayerStats ps JOIN Player p ON ps.player_id = p.player_id JOIN Matches m ON ps.match_id = m.match_id ORDER BY m.date DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Статистика гравців (адмін)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <h1 class="text-center mb-4">Управління статистикою гравців</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success">Успішно збережено.</div>
        <?php endif; ?>

        <form method="post" class="card p-4 mb-5 shadow-sm bg-white">
            <h4 class="mb-3">Додати запис</h4>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Гравець</label>
                    <select name="player_id" class="form-select" required>
                        <option value="">Оберіть</option>
                        <?php foreach ($players as $p): ?>
                            <option value="<?= $p['player_id'] ?>"><?= htmlspecialchars($p['last_name'] . ' ' . $p['first_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Матч</label>
                    <select name="match_id" class="form-select" required>
                        <option value="">Оберіть</option>
                        <?php foreach ($matches as $m): ?>
                            <option value="<?= $m['match_id'] ?>"><?= htmlspecialchars($m['date']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Голи</label>
                    <input type="number" name="goals" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Асисти</label>
                    <input type="number" name="assists" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Жовті картки</label>
                    <input type="number" name="yellow_cards" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Червоні картки</label>
                    <input type="number" name="red_cards" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Зберегти</button>
                </div>
            </div>
        </form>

        <div class="table-responsive shadow-sm">
            <table class="table table-bordered table-striped text-center align-middle bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Гравець</th>
                        <th>Матч</th>
                        <th>Голи</th>
                        <th>Асисти</th>
                        <th>Жовті</th>
                        <th>Червоні</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['last_name'] . ' ' . $row['first_name']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= (int)$row['goals'] ?></td>
                            <td><?= (int)$row['assists'] ?></td>
                            <td><?= (int)$row['yellow_cards'] ?></td>
                            <td><?= (int)$row['red_cards'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['stat_id'] ?>">Редагувати</button>
                            </td>
                        </tr>

                        <!-- Модальне вікно -->
                        <div class="modal fade" id="editModal<?= $row['stat_id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Редагування запису</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="edit_id" value="<?= $row['stat_id'] ?>">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Гравець</label>
                                                    <select name="player_id" class="form-select" required>
                                                        <?php foreach ($players as $p): ?>
                                                            <option value="<?= $p['player_id'] ?>" <?= $p['player_id'] == $row['player_id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['last_name'] . ' ' . $p['first_name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Матч</label>
                                                    <select name="match_id" class="form-select" required>
                                                        <?php foreach ($matches as $m): ?>
                                                            <option value="<?= $m['match_id'] ?>" <?= $m['match_id'] == $row['match_id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['date']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Голи</label>
                                                    <input type="number" name="goals" class="form-control" min="0" value="<?= (int)$row['goals'] ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Асисти</label>
                                                    <input type="number" name="assists" class="form-control" min="0" value="<?= (int)$row['assists'] ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Жовті</label>
                                                    <input type="number" name="yellow_cards" class="form-control" min="0" value="<?= (int)$row['yellow_cards'] ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Червоні</label>
                                                    <input type="number" name="red_cards" class="form-control" min="0" value="<?= (int)$row['red_cards'] ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Зберегти зміни</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>