<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../db/connect_admin.php";
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login.php");
    exit;
}

include "../includes/headerAdmin.php";

if ($conn->connect_error) {
    die("Підключення не вдалося: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM standings_admin_view ORDER BY position ASC");

$stmt->execute();
$result = $stmt->get_result();
$teams = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Турнірна таблиця — Адмінпанель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.2s ease-in-out;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Керування турнірною таблицею</h1>

        <table class="table table-bordered text-center align-middle table-hover">
            <thead class="table-dark">
                <tr>
                    <th>№</th>
                    <th>Команда</th>
                    <th>І</th>
                    <th>П</th>
                    <th>Н</th>
                    <th>П</th>
                    <th>ЗМ</th>
                    <th>ПМ</th>
                    <th>Р/М</th>
                    <th>О</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($teams as $team): ?>
                    <tr>
                        <td><?= htmlspecialchars($team['position']) ?></td>
                        <td><?= htmlspecialchars($team['team_name']) ?></td>
                        <td><?= htmlspecialchars($team['matches_played']) ?></td>
                        <td><?= htmlspecialchars($team['wins']) ?></td>
                        <td><?= htmlspecialchars($team['draws']) ?></td>
                        <td><?= htmlspecialchars($team['losses']) ?></td>
                        <td><?= htmlspecialchars($team['goals_for']) ?></td>
                        <td><?= htmlspecialchars($team['goals_against']) ?></td>
                        <td><?= htmlspecialchars($team['goal_diff']) ?></td>
                        <td><?= htmlspecialchars($team['points']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary"
                                onclick='openEditModal(<?= json_encode($team, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                Редагувати
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="edit_standing.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Редагування команди</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрити"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="team_id">
                    <input type="hidden" name="team_name" id="team_name_display">

                    <div class="mb-3">
                        <label for="wins" class="form-label">Перемоги (В):</label>
                        <input type="number" class="form-control" name="wins" id="wins" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="draws" class="form-label">Нічиї (Н):</label>
                        <input type="number" class="form-control" name="draws" id="draws" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="losses" class="form-label">Поразки (П):</label>
                        <input type="number" class="form-control" name="losses" id="losses" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="goals_for" class="form-label">Забиті м'ячі (ЗМ):</label>
                        <input type="number" class="form-control" name="goals_for" id="goals_for" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="goals_against" class="form-label">Пропущені м'ячі (ПМ):</label>
                        <input type="number" class="form-control" name="goals_against" id="goals_against" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Зберегти</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(team) {
            document.getElementById('team_id').value = team.id;
            document.getElementById('team_name_display').value = team.team_name;
            document.getElementById('wins').value = team.wins;
            document.getElementById('draws').value = team.draws;
            document.getElementById('losses').value = team.losses;
            document.getElementById('goals_for').value = team.goals_for;
            document.getElementById('goals_against').value = team.goals_against;
            document.getElementById('editModalLabel').innerText = "Редагування: " + team.team_name;

            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        }
    </script>

    <?php
    include "../../admins/includes/footerAdmin.php";
    ?>
</body>

</html>