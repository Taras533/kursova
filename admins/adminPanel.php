<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../kursova/login.php");
    exit;
}

include "../admins/includes/headerAdmin.php";
?>

<div class="container text-center mt-5">
    <img src="../../kursova/photos/admin.png" alt="Admin" class="img-fluid admin-img mx-auto d-block" style="width: 450px;
    height: auto;">
</div>


<?php
include "../admins/includes/footerAdmin.php";
?>