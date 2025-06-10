<?php
require_once 'db/connect.php';
include "../kursova/includes/header.php";

$status = isset($_GET['status']) && in_array($_GET['status'], ['planned', 'finished']) ? $_GET['status'] : null;
$type = isset($_GET['tournament_type']) && in_array($_GET['tournament_type'], ['championship', 'cup', 'friendly']) ? $_GET['tournament_type'] : null;

$sql = "SELECT *
        FROM matches_user_view
        WHERE 1=1";

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

$active_status_all = !$status ? 'active' : '';
$active_status_planned = ($status === 'planned') ? 'active' : '';
$active_status_finished = ($status === 'finished') ? 'active' : '';

$active_type_all = !$type ? 'active' : '';
$active_type_championship = ($type === 'championship') ? 'active' : '';
$active_type_cup = ($type === 'cup') ? 'active' : '';
$active_type_friendly = ($type === 'friendly') ? 'active' : '';

?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Матчі</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../kursova/styles/matches.css">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Матчі</h1>

        <div class="d-flex justify-content-center mb-4">
            <div class="card p-3 shadow-sm custom-filter-card">
                <div class="d-flex justify-content-around flex-wrap">
                    <div class="filter-group me-3 mb-2 mb-md-0">
                        <strong class="filter-label">Статус:</strong>
                        <div class="btn-group" role="group" aria-label="Status filter">
                            <a class="btn btn-outline-primary btn-sm <?= $active_status_planned ?>" href="?status=planned<?= ($type ? '&tournament_type=' . htmlspecialchars($type) : '') ?>">Заплановані</a>
                            <a class="btn btn-outline-primary btn-sm <?= $active_status_finished ?>" href="?status=finished<?= ($type ? '&tournament_type=' . htmlspecialchars($type) : '') ?>">Завершені</a>
                            <a class="btn btn-outline-primary btn-sm <?= $active_status_all ?>" href="?<?= ($type ? 'tournament_type=' . htmlspecialchars($type) : '') ?>">Усі</a>
                        </div>
                    </div>

                    <div class="filter-group mb-2 mb-md-0">
                        <strong class="filter-label">Тип турніру:</strong>
                        <div class="btn-group" role="group" aria-label="Tournament type filter">
                            <a class="btn btn-outline-success btn-sm <?= $active_type_championship ?>" href="?tournament_type=championship<?= ($status ? '&status=' . htmlspecialchars($status) : '') ?>">Чемпіонат</a>
                            <a class="btn btn-outline-success btn-sm <?= $active_type_cup ?>" href="?tournament_type=cup<?= ($status ? '&status=' . htmlspecialchars($status) : '') ?>">Кубок</a>
                            <a class="btn btn-outline-success btn-sm <?= $active_type_friendly ?>" href="?tournament_type=friendly<?= ($status ? '&status=' . htmlspecialchars($status) : '') ?>">Товариські</a>
                            <a class="btn btn-outline-success btn-sm <?= $active_type_all ?>" href="?<?= ($status ? 'status=' . htmlspecialchars($status) : '') ?>">Усі</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($match = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm match-card">
                            <div class="card-body">
                                <h5 class="card-title match-title">
                                    <?= htmlspecialchars($match['team_home']) ?> — <?= htmlspecialchars($match['team_away']) ?>
                                </h5>
                                <p class="card-text"><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($match['date'])) ?></p>
                                <p class="card-text"><strong>Стадіон:</strong> <?= htmlspecialchars($match['stadium']) ?></p>
                                <p class="card-text"><strong>Турнір:</strong> <?= htmlspecialchars($match['tournament_name']) ?> (<?= htmlspecialchars($match['season']) ?>)</p>
                                <?php if ($match['status'] === 'finished'): ?>
                                    <p class="card-text match-score" style="text-align: center;"><strong>Рахунок:</strong> <?= (int)$match['score_home'] ?> : <?= (int)$match['score_away'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Немає матчів, які відповідають вашим критеріям.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    include "../kursova/includes/footer.php";
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>