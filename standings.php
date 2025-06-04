<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db/connect.php';
include "../kursova/includes/header.php";

$stmt = $conn->prepare("SELECT * FROM standings_user_view");
$stmt->execute();
$result = $stmt->get_result();
$teams = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Турнірна таблиця</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="../kursova/styles/standings.css"> -->
</head>

<body class="bg-light text-dark">

    <section class="container my-5">
        <div class="bg-white shadow rounded p-4">
            <h1 class="text-center mb-4 text-primary fw-bold">Турнірна таблиця: Вища ліга</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th data-bs-toggle="tooltip" title="Місце">№</th>
                            <th>Команда</th>
                            <th data-bs-toggle="tooltip" title="Ігри">І</th>
                            <th data-bs-toggle="tooltip" title="Перемоги">П</th>
                            <th data-bs-toggle="tooltip" title="Поразки">П</th>
                            <th data-bs-toggle="tooltip" title="Нічиї">Н</th>
                            <th data-bs-toggle="tooltip" title="Забиті м'ячі">ЗМ</th>
                            <th data-bs-toggle="tooltip" title="Пропущені м'ячі">ПМ</th>
                            <th data-bs-toggle="tooltip" title="Різниця м'ячів">Р/М</th>
                            <th data-bs-toggle="tooltip" title="Очки">О</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teams as $team): ?>
                            <tr>
                                <td><?= htmlspecialchars($team['position']) ?></td>
                                <td><?= htmlspecialchars($team['team_name']) ?></td>
                                <td><?= htmlspecialchars($team['matches_played']) ?></td>
                                <td><?= htmlspecialchars($team['wins']) ?></td>
                                <td><?= htmlspecialchars($team['losses']) ?></td>
                                <td><?= htmlspecialchars($team['draws']) ?></td>
                                <td><?= htmlspecialchars($team['goals_for']) ?></td>
                                <td><?= htmlspecialchars($team['goals_against']) ?></td>
                                <td><?= htmlspecialchars($team['goal_diff']) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($team['points']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-p5ntn39PSnYJg1mTXmC0dEbgKBPc1+ekfx38cDsoOi1eETmANNS7tHzBT+v3vd94" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

    <?php include "../kursova/includes/footer.php"; ?>

</body>

</html>