<?php
session_start();
require_once 'db/connect.php';
include "../kursova/includes/header.php";
$valid_types = ['championship', 'cup', 'friendly'];


$tournament_type = '';
if (isset($_GET['type']) && in_array($_GET['type'], $valid_types, true)) {
    $tournament_type = $_GET['type'];
}

$sql = "SELECT * FROM player_stats_view";
$params = [];
$types = '';

if ($tournament_type !== '') {
    $sql .= " WHERE tournament_type = ?";
    $params[] = $tournament_type;
    $types .= 's';
}

$sql .= " ORDER BY total_goals DESC, total_assists DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Статистика гравців</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">Статистика гравців</h1>

        <form method="get" class="mb-4 d-flex justify-content-center gap-3 flex-wrap">
            <select name="type" class="form-select w-auto">
                <option value="">Усі типи турнірів</option>
                <option value="championship" <?= $tournament_type === 'championship' ? 'selected' : '' ?>>Чемпіонат</option>
                <option value="cup" <?= $tournament_type === 'cup' ? 'selected' : '' ?>>Кубок</option>
                <option value="friendly" <?= $tournament_type === 'friendly' ? 'selected' : '' ?>>Товариський</option>
            </select>
            <button type="submit" class="btn btn-primary">Фільтрувати</button>
        </form>

        <?php if (count($stats) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center align-middle shadow-sm bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th>Ім’я</th>
                            <th>Прізвище</th>
                            <th>Тип турніру</th>
                            <th>Матчів</th>
                            <th>Голи</th>
                            <th>Асисти</th>
                            <th>Жовті</th>
                            <th>Червоні</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['first_name']) ?></td>
                                <td><?= htmlspecialchars($s['last_name']) ?></td>
                                <td><?= htmlspecialchars($s['tournament_name']) ?></td>
                                <td><?= (int)$s['matches_played'] ?></td>
                                <td><?= (int)$s['total_goals'] ?></td>
                                <td><?= (int)$s['total_assists'] ?></td>
                                <td><?= (int)$s['total_yellow_cards'] ?></td>
                                <td><?= (int)$s['total_red_cards'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Немає доступної статистики для обраного типу турніру.</p>
        <?php endif; ?>
    </div>
    <?php include "../kursova/includes/footer.php"; ?>
</body>

</html>