<?php
    include 'skripte/connect.php';
    define('UPLPATH', 'images/');
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <title>Početna</title>
        <meta name="author" content="Mislav Krajačić">
        <link rel="icon" href="logo/favicon16.png" type="image/png">
        <link rel="stylesheet" href="style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    </head>

    <body>
        <header>
            <div class="header-content">
                <h1 class="main-heading">
                    <a href="index.php" class="logo-link">
                        <img src="logo/logo-horizontal.png" alt="logo stranice">
                    </a>
                </h1>
                <nav>
                    <ul class="main-nav">
                        <li><a href="index.php"><span class="content-text">POČETNA</span></a></li>
                        <li><a href="podstranice/kategorija.php?id=Sport"><span class="content-text">SPORT</span></a></li>
                        <li><a href="podstranice/kategorija.php?id=Kultura"><span class="content-text">KULTURA</span></a></li>
                        <li><a href="podstranice/unos.php"><span class="content-text">UNOS</span></a></li>
                        <li><a href="podstranice/administracija.php"><span class="content-text">ADMINISTRACIJA</span></a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
            <div class="news-categories">
                <section class="category">
                    <div class="category-header">
                        <h2 class="content-text">Sport</h2>
                    </div>

                    <div class="articles">
                    <?php
                        $sql = "SELECT id, datum, naslov, sazetak, slika FROM clanci WHERE kategorija = 'Sport' AND arhiva = 0 ORDER BY datum DESC LIMIT 3";
                        $result = mysqli_query($dbc, $sql);

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<article class="card">';
                            echo '<div class="img-wrapper">';
                            echo '<img src="' . UPLPATH . $row['slika'] . '" alt="slika članka">';
                            echo '</div>';
                            echo '<div class="card-content">';
                            echo '<a href="podstranice/clanak.php?id=' . $row['id'] . '" class="article-link">';
                            echo '<h3 class="article-heading">' . $row['naslov'] . '</h3 class="article-heading">';
                            echo '</a>';
                            echo '<p class="content-text article-text">' . $row['sazetak'] . '</p>';
                            echo '</div>';
                            echo '</article>';
                        }
                    ?>
                    </div>
                </section>

                <section class="category">
                    <div class="category-header">
                        <h2 class="content-text">Kultura</h2>
                    </div>

                    <div class="articles">
                        <?php
                        $sql = "SELECT id, datum, naslov, sazetak, slika FROM clanci WHERE kategorija = 'Kultura' AND arhiva = 0 ORDER BY datum DESC LIMIT 3";
                        $result = mysqli_query($dbc, $sql);

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<article class="card">';
                            echo '<div class="img-wrapper">';
                            echo '<img src="' . UPLPATH . $row['slika'] . '" alt="slika članka">';
                            echo '</div>';
                            echo '<div class="card-content">';
                            echo '<a href="podstranice/clanak.php?id=' . $row['id'] . '" class="article-link">';
                            echo '<h3 class="article-heading">' . $row['naslov'] . '</h3 class="article-heading">';
                            echo '</a>';
                            echo '<p class="content-text article-text">' . $row['sazetak'] . '</p>';
                            echo '</div>';
                            echo '</article>';
                        }
                        ?>
                    </div>
                </section>
            </div>
        </main>

        <footer>
            <div class="footer-content">
                <p class="content-text">© 2026 Mislav Krajačić</p>
                <p class="content-text">e-mail: mkrajacic@tvz.hr</p>
            </div>
        </footer>
    </body>
</html>

<?php
    mysqli_close($dbc);
?>