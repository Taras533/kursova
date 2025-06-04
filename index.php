<html>

<head>
    <title>FC Zhyrka</title>
    <link rel="stylesheet" href="styles/index.css">
</head>

<body>
    <?php
    include '/var/www/html/kursova/includes/header.php';
    ?>
    <section id="team-photo">
        <img src="photos/Zhyrka.jpg" alt="Фото команди Жвирка" />
    </section>

    <section id="news-slider">
        <div class="container">
            <h2>Останні новини</h2>
            <div class="slider">
                <div class="slide">Новина 1</div>
                <div class="slide">Новина 2</div>
                <div class="slide">Новина 3</div>
            </div>
        </div>
    </section>

    <section id="subscribe">
        <div class="container">
            <h2>Не пропускайте новини клубу!</h2>
            <p>Підпишіться на нашу розсилку:</p>
            <form action="subscribe.php" method="post">
                <input type="email" name="email" placeholder="Ваш email" required />
                <button type="submit">Підписатися</button>
            </form>
        </div>
    </section>

    <section id="sponsors">
        <div class="container">
            <h2>Наші спонсори</h2>
            <div class="sponsor-logos">
                <a href="https://kelme.ua/"><img src="photos/logo-kelme.png" alt="Спонсор 1" /></a>
                <img src="photos/logo_maxBUD.png" alt="Спонсор 2" />
                <img src="photos/logo_betonBUD.png" alt="Спонсор 3" />
            </div>
        </div>
    </section>
    <?php
    include '/var/www/html/kursova/includes/footer.php';
    ?>
</body>

</html>