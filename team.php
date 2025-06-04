<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../kursova/db/connect.php';
include "../kursova/includes/header.php";

function getCoachStaff($conn)
{
    $sql = "SELECT first_name, last_name, position, photo FROM CoachStaff";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getPlayersByPosition($conn, $position)
{
    $sql = "SELECT first_name, last_name, jersey_number, photo FROM Player WHERE position = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $position);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$coaches     = getCoachStaff($conn);
$goalkeepers = getPlayersByPosition($conn, 'Воротар');
$defenders   = getPlayersByPosition($conn, 'Захисник');
$midfielders = getPlayersByPosition($conn, 'Півзахисник');
$forwards    = getPlayersByPosition($conn, 'Нападник');

function renderCardSection($title, $items, $isCoach = false)
{
    echo "<section class='container my-5'>";
    echo "<h2 class='text-center mb-4 display-5 fw-bold'>{$title}</h2>";

    echo "<div class='row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-4 justify-content-center'>";

    if (empty($items)) {
        echo "<div class='col-12'><p class='text-center text-muted'>Список порожній.</p></div>";
    } else {
        foreach ($items as $item) {
            $photo_file = htmlspecialchars($item['photo']);
            $photo_path = "/kursova/photos/team/{$photo_file}";
            $display_photo_path = file_exists(__DIR__ . '/photos/team/' . $photo_file) ? $photo_path : '/kursova/photos/team/default_player.jpg';

            $name = htmlspecialchars($item['first_name'] . ' ' . $item['last_name']);
            $numberOrPos = $isCoach ? htmlspecialchars($item['position']) : '#' . htmlspecialchars($item['jersey_number']);

            echo "
            <div class='col'>
                <div class='card h-100 player-card shadow-sm rounded-3'>
                    <img src='{$display_photo_path}' class='card-img-top player-img' alt='{$name}'>
                    <div class='player-overlay'>
                        <div class='player-name'>{$name}</div>
                        <div class='player-subtext'>{$numberOrPos}</div>
                    </div>
                </div>
            </div>";
        }
    }
    echo "</div></section>";
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Команда</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../kursova/styles/team.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
</head>

<body>

    <?php
    renderCardSection("Тренерський склад", $coaches, true);
    renderCardSection("Воротарі", $goalkeepers);
    renderCardSection("Захисники", $defenders);
    renderCardSection("Півзахисники", $midfielders);
    renderCardSection("Нападники", $forwards);
    ?>

    <?php
    include "../kursova/includes/footer.php";
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>