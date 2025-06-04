<?php
$host = 'localhost';
$user = 'user';
$pass = 'user';
$db = 'FootballClub';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");
