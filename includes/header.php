<?php session_start(); ?>

<head>
  <title>ФК Жвирка</title>
  <link rel="stylesheet" href="/kursova/styles/header.css">
</head>

<body>
  <header>
    <div class="container">
      <a href="/kursova/index.php">
        <img src="/kursova/photos/logo-removebg-preview.png" alt="Логотип клубу Жвирка" class="logo" />
      </a>
      <nav>
        <ul>
          <li><a href="/kursova/team.php">Команда</a></li>
          <li><a href="/kursova/standings.php">Турнірна таблиця</a></li>
          <li><a href="/kursova/matches.php">Матчі</a></li>

          <?php if (isset($_SESSION['user_logged_in'])): ?>
            <li><a href="/kursova/users/chat.php">Чат</a></li>
            <li><a href="/kursova/users/logout_user.php">Вихід</a></li>

          <?php elseif (isset($_SESSION['admin_logged_in'])): ?>
            <li><a href="/kursova/admins/adminPanel.php">Адмін панель</a></li>
            <li><a href="/kursova/admins/logout_admin.php">Вихід</a></li>

          <?php else: ?>
            <li><a href="/kursova/users/register_user.php">Реєстрація</a></li>
            <li><a href="/kursova/users/login_user.php">Вхід фаната</a></li>
            <li><a href="/kursova/login.php">Вхід адміна</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>



  <?php
  /*   Той шо нормально фуричив тіки забери   <?php  ?>

  <head>
    <title>ФК Жвирка</title>
    <link rel="stylesheet" href="styles/header.css">
  </head>

  <body>
    <header>
      <div class="container">
        <a href="index.php"><img
            src="photos/logo-removebg-preview.png"
            alt="Логотип клубу Жвирка"
            class="logo" /></a>
        <nav>
          <ul>
            <li><a href="../../kursova/team.php">Команда</a></li>
            <li><a href="../../kursova/standings.php">Турнірна таблиця</a></li>
            <li><a href="#">Чат</a></li>
            <li><a href="../../kursova/matches.php">Матчі</a></li>
            <li><a href="../../kursova/users/register_user.php">Реєстрація / Вхід</a></li>
            <li><a href="../../kursova/login.php">Вхід</a></li>
          </ul>
        </nav>
      </div>
    </header> */

  ?>




  <!-- <head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<header>
  <nav class="navbar navbar-expand-lg bg-light border-bottom shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="photos/logo-removebg-preview.png" alt="Логотип клубу Жвирка" style="height: 60px;" class="me-2" />
        <span class="fw-bold">ФК Жвирка</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Перемкнути навігацію">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav gap-2">
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="../../kursova/team.php">Команда</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="../../kursova/standings.php">Турнірна таблиця</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="#">Новини</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="../../kursova/matches.php">Матчі</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-primary ms-lg-3" href="../../kursova/login.php">Вхід</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-p5ntn39PSnYJg1mTXmC0dEbgKBPc1+ekfx38cDsoOi1eETmANNS7tHzBT+v3vd94"
  crossorigin="anonymous"></script> -->