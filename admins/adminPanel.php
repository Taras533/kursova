<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../kursova/login.php");
    exit;
}

include "../admins/includes/headerAdmin.php";
?>

<h2 style="display: flex; align-items:center; justify-content:center; height:500px;">Адмін-панель! <br> Не спало думку, що сюди додати, тому буде так 😁</h2>


<?php
include "../admins/includes/footerAdmin.php";
?>