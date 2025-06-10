<?php
session_start();
require_once "../db/connect_admin.php";
include "../includes/headerAdmin.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

$status = isset($_GET['status']) && in_array($_GET['status'], ['planned', 'finished']) ? $_GET['status'] : null;
$type = isset($_GET['tournament_type']) && in_array($_GET['tournament_type'], ['championship', 'cup', 'friendly']) ? $_GET['tournament_type'] : null;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$sql = "SELECT * FROM matches_admin_view WHERE 1=1";
$params = [];
$types = "";

if ($status) {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($type) {
    $sql .= " AND tournament_type = ?";
    $params[] = $type;
    $types .= "s";
}

$sql .= " ORDER BY date DESC";
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Керування матчами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Матчі</h2>
            <a href="add_match.php" class="btn btn-success">+ Додати новий матч</a>
        </div>

        <!-- Фільтри -->
        <div class="card mb-4 p-3">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Усі</option>
                        <option value="planned" <?= $status === 'planned' ? 'selected' : '' ?>>Заплановані</option>
                        <option value="finished" <?= $status === 'finished' ? 'selected' : '' ?>>Завершені</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tournament_type" class="form-label">Тип турніру</label>
                    <select name="tournament_type" id="tournament_type" class="form-select">
                        <option value="">Усі</option>
                        <option value="championship" <?= $type === 'championship' ? 'selected' : '' ?>>Чемпіонат</option>
                        <option value="cup" <?= $type === 'cup' ? 'selected' : '' ?>>Кубок</option>
                        <option value="friendly" <?= $type === 'friendly' ? 'selected' : '' ?>>Товариський</option>
                    </select>
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary">Фільтрувати</button>
                </div>
            </form>
        </div>

        <!-- Таблиця матчів -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Дата</th>
                        <th>Команди</th>
                        <th>Стадіон</th>
                        <th>Турнір</th>
                        <th>Тип</th>
                        <th>Статус</th>
                        <th>Рахунок</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($match = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($match['date']))) ?></td>
                            <td><?= htmlspecialchars($match['team_home']) ?> — <?= htmlspecialchars($match['team_away']) ?></td>
                            <td><?= htmlspecialchars($match['stadium']) ?></td>
                            <td><?= htmlspecialchars($match['tournament_name']) ?> (<?= htmlspecialchars($match['season']) ?>)</td>
                            <td><?= htmlspecialchars($match['tournament_type']) ?></td>
                            <td><?= htmlspecialchars($match['status']) ?></td>
                            <td><?= ($match['status'] === 'finished') ? ((int)$match['score_home'] . ' : ' . (int)$match['score_away']) : '—' ?></td>
                            <td class="text-center">
                                <a href="edit_match.php?id=<?= (int)$match['match_id'] ?>" class="btn btn-sm btn-warning">Редагувати</a>

                                <form action="delete_match.php" method="post" onsubmit="return confirm('Ви впевнені, що хочете видалити цей матч?')" class="d-inline">
                                    <input type="hidden" name="match_id" value="<?= (int)$match['match_id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$stmt->close();
$conn->close();
?>