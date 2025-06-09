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
            <h2>Cпонсори</h2>
            <div class="sponsor-logos">
                <a href="https://kelme.ua/"><img src="photos/sponsors/logo-kelme-removebg-preview.png" alt="Kelme" /></a>
                <img src="photos/sponsors/logo_maxBUD.png" alt="MaxBud" />
                <img src="photos/sponsors/sponsor_Sok_Bud-Beton-removebg-preview.png" alt="Сокаль Буд-бетон" />
            </div>
        </div>
    </section>
    <?php
    include '/var/www/html/kursova/includes/footer.php';
    ?>
</body>

</html>