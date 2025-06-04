<?php
require_once "../db/connect_admin.php";
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

include "../includes/headerAdmin.php"; // Переконайтеся, що headerAdmin.php вже підключає Bootstrap CSS

function fetchPlayers($conn, $nameFilter, $positionFilter)
{
    $sql = "SELECT * FROM Player WHERE 1";
    $params = [];
    $types = '';

    if ($nameFilter !== '') {
        $sql .= " AND (first_name LIKE CONCAT('%', ?, '%') OR last_name LIKE CONCAT('%', ?, '%'))";
        $params[] = $nameFilter;
        $params[] = $nameFilter;
        $types .= 'ss';
    }

    if ($positionFilter !== '') {
        $sql .= " AND position = ?";
        $params[] = $positionFilter;
        $types .= 's';
    }

    $sql .= " ORDER BY last_name ASC";

    // Повертаємо всі результати
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $players = $result->fetch_all(MYSQLI_ASSOC);

    // Рахуємо загальну кількість
    $stmt_total = $conn->prepare(str_replace('SELECT *', 'SELECT COUNT(*)', $sql));
    if ($params) {
        $stmt_total->bind_param($types, ...$params);
    }
    $stmt_total->execute();
    $total_rows = $stmt_total->get_result()->fetch_row()[0];

    return [$players, $total_rows];
}

function fetchCoaches($conn, $nameFilter)
{
    $sql = "SELECT * FROM CoachStaff WHERE 1";
    $params = [];
    $types = '';

    if ($nameFilter !== '') {
        $sql .= " AND (first_name LIKE CONCAT('%', ?, '%') OR last_name LIKE CONCAT('%', ?, '%'))";
        $params[] = $nameFilter;
        $params[] = $nameFilter;
        $types .= 'ss';
    }

    $sql .= " ORDER BY last_name ASC";
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$search_player = trim($_GET['search_player'] ?? '');
$filter_position = trim($_GET['filter_position'] ?? '');
$search_coach = trim($_GET['search_coach'] ?? '');

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

list($all_players, $total_players) = fetchPlayers($conn, $search_player, $filter_position);
$players = array_slice($all_players, $offset, $limit);
$total_pages = ceil($total_players / $limit);

$coaches = fetchCoaches($conn, $search_coach);
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Керування складом</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/team_manage.css">
</head>

<body>
    <div class="container my-4">
        <h1 class="mb-4 text-center">Керування складом</h1>

        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Гравці</h2>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <button class="btn btn-success mb-2 mb-md-0" onclick="openAddPlayerModal()">+ Додати гравця</button>
                    <form method="get" class="d-flex align-items-center flex-wrap">
                        <input type="text" name="search_player" placeholder="Пошук по ПІБ"
                            class="form-control me-2 mb-2 mb-md-0" style="max-width: 200px;"
                            value="<?= htmlspecialchars($search_player) ?>">
                        <select name="filter_position" class="form-select me-2 mb-2 mb-md-0" style="max-width: 150px;"
                            onchange="this.form.submit()">
                            <option value="">Усі позиції</option>
                            <?php
                            $positions = ['Воротар', 'Захисник', 'Півзахисник', 'Нападник'];
                            foreach ($positions as $pos) {
                                $selected = ($filter_position === $pos) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($pos) . "' $selected>" . htmlspecialchars($pos) . "</option>";
                            }
                            ?>
                        </select>
                        <!-- <button type="submit" class="btn btn-outline-primary mb-2 mb-md-0">Пошук/Фільтр</button> -->
                    </form>
                </div>

                <?php if (!empty($players)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ім’я</th>
                                    <th>Прізвище</th>
                                    <th>Позиція</th>
                                    <th>Дата народження</th>
                                    <th>Номер</th>
                                    <th>Дії</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($players as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['first_name']) ?></td>
                                        <td><?= htmlspecialchars($p['last_name']) ?></td>
                                        <td><?= htmlspecialchars($p['position']) ?></td>
                                        <td><?= htmlspecialchars($p['birth_date']) ?></td>
                                        <td><?= htmlspecialchars($p['jersey_number']) ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-warning btn-sm me-2" onclick="openEditPlayerModal(<?= htmlspecialchars(json_encode($p)) ?>)">Редагувати</button>
                                                <form method="post" action="delete.php" onsubmit="return confirm('Ви впевнені, що хочете видалити гравця <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>?');" style="display:inline;">
                                                    <input type="hidden" name="source" value="player">
                                                    <input type="hidden" name="player_id" value="<?= htmlspecialchars($p['player_id']) ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Попередня</a>
                                </li>
                            <?php endif; ?>
                            <li class="page-item disabled"><span class="page-link">Сторінка <?= $page ?> з <?= $total_pages ?></span></li>
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Наступна</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>

                <?php else: ?>
                    <p class="text-center text-muted">Немає гравців за заданими критеріями.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Тренерський склад</h2>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <button class="btn btn-success mb-2 mb-md-0" onclick="openAddCoachModal()">+ Додати тренера</button>
                    <form method="get" class="d-flex align-items-center flex-wrap">
                        <input type="text" name="search_coach" placeholder="Пошук по ПІБ"
                            class="form-control me-2 mb-2 mb-md-0" style="max-width: 200px;"
                            value="<?= htmlspecialchars($search_coach) ?>">
                        <button type="submit" class="btn btn-outline-primary mb-2 mb-md-0">Пошук</button>
                    </form>
                </div>

                <?php if (!empty($coaches)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ім’я</th>
                                    <th>Прізвище</th>
                                    <th>Посада</th>
                                    <th>Дії</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coaches as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['first_name']) ?></td>
                                        <td><?= htmlspecialchars($c['last_name']) ?></td>
                                        <td><?= htmlspecialchars($c['position']) ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-warning btn-sm me-2" onclick="openEditCoachModal(<?= htmlspecialchars(json_encode($c)) ?>)">Редагувати</button>
                                                <form method="post" action="delete.php" onsubmit="return confirm('Ви впевнені, що хочете видалити тренера <?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?>?');" style="display:inline;">
                                                    <input type="hidden" name="source" value="coach">
                                                    <input type="hidden" name="coach_id" value="<?= htmlspecialchars($c['coach_id']) ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">Немає тренерів за заданими критеріями.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="modal-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="team_modal.js"></script>
</body>

</html>