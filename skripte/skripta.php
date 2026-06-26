<?php
    define('UPLPATH', '../images/');

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $naslov = $sazetak = $slika = $teskt = $kategorija = null;
        if (isset($_POST["naslov"])) {
            $naslov = $_POST["naslov"];
        }
        if (isset($_POST["sazetak"])) {
            $sazetak = $_POST["sazetak"];
        }
        if (isset($_FILES['slika']['name'])) {
            $slika = $_FILES['slika']['name'];
        }
        if (isset($_POST["tekst"])) {
            $tekst = $_POST["tekst"];
        }
        if (isset($_POST["kategorija"])) {
            $kategorija = $_POST["kategorija"];
        }
        if (isset($_POST["arhiviraj"])) {
            $arhiva = 1;
        }
        else {
            $arhiva = 0;
        }

        if ($naslov && $sazetak && $slika && $tekst && $kategorija) {
            include 'connect.php';
            include 'insert.php';
            include 'datum_format.php';

            $datum = date('Y-m-d');

            $target = "../images/" . $slika;
            if(move_uploaded_file($_FILES['slika']['tmp_name'], "$target")){
                unesi_clanak($dbc, $datum, $naslov, $sazetak, $slika, $tekst, $kategorija, $arhiva);
            }
            else {
                echo "Neuspijeli prijenos slike!";
            }

            $datum = date_text_format($datum);
        }
    }
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>Pregled članka</title>
    <meta name="author" content="Mislav Krajačić">
    <link rel="icon" href="../logo/favicon16.png" type="image/png">
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div class="header-content">
            <h1 class="main-heading">
                <a href="../index.php" class="logo-link">
                    <img src="../logo/logo-horizontal.png" alt="logo stranice">
                </a>
            </h1>
            <nav>
                <ul class="main-nav">
                    <li><a href="../index.php"><span class="content-text">POČETNA</span></a></li>
                    <li><a href="../podstranice/kategorija.php?id=Sport"><span class="content-text">SPORT</span></a></li>
                    <li><a href="../podstranice/kategorija.php?id=Kultura"><span class="content-text">KULTURA</span></a></li>
                    <li><a href="../podstranice/unos.php"><span class="content-text">UNOS</span></a></li>
                    <li><a href="../podstranice/administracija.php"><span class="content-text">ADMINISTRACIJA</span></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        
        <article class="single-article">
            <div class="category-header">
                <h2 class="content-text"><?php echo htmlspecialchars($kategorija)?></h2>
            </div>
            
            <div class="single-article-content">
                <div class="heading-wrapper">
                    <h3 class="single-article-heading"><?php echo htmlspecialchars($naslov)?></h3>
                    <p class="content-text date"><?php echo htmlspecialchars($datum) ?></p>
                </div>
                
                <div class="article-img-wrapper">
                    <?php
                        echo '<img src="' . UPLPATH . $slika . '" alt="slika članka">';
                    ?>
                </div>
                
                <div class="content-wrapper">
                    <p class="content-text article-text"><strong><?php echo nl2br(htmlspecialchars($sazetak))?></strong></p>
                    <p class="content-text article-text"><?php echo nl2br(htmlspecialchars($tekst))?></p>
                </div>
            </div>
        </article>
    </main>

    <footer>
        <div class="footer-content">
            <p class="content-text">© 2026 Mislav Krajačić</p>
            <p class="content-text">e-mail: mkrajacic@tvz.hr</p>
        </div>
    </footer>
</body>
</html>