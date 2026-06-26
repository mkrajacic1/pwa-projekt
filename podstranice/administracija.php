<?php
    session_start();

    include '../skripte/connect.php';
    include '../skripte/datum_format.php';
    define("UPLPATH", "../images/");

    $sql = "SELECT * FROM clanci";
    if(!($result = mysqli_query($dbc, $sql)))  {
        mysqli_close($dbc);
        die("Neuspiješan dohvat podataka");
    }

    $pokusaj_prijave = false;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['update'])) {
            $id = $_POST['id'];
            $naslov = $_POST['naslov'];
            $sazetak = $_POST['sazetak'];
            $tekst = $_POST['tekst'];
            $kategorija = $_POST['kategorija'];
            $arhiva = isset($_POST['arhiviraj']) ? 1 : 0;

            $slika = $_POST['stara-slika'];
            if ($_FILES['slika']['error'] == UPLOAD_ERR_OK) {
                $slika = $_FILES['slika']['name'];
                $target = UPLPATH . $slika;
                if(!move_uploaded_file($_FILES['slika']['tmp_name'], "$target")){
                   echo "Neuspijeli prijenos slike!";
                }
            }

            $sql = "UPDATE clanci SET naslov = ?, sazetak = ?, tekst = ?, slika = ?, kategorija = ?, arhiva = ? WHERE id = ?";
            $stmt = mysqli_prepare($dbc, $sql);
            mysqli_stmt_bind_param($stmt, "sssssii", $naslov, $sazetak, $tekst, $slika, $kategorija, $arhiva, $id);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Neuspijelo ažuriranje!";
            }
            mysqli_stmt_close($stmt);
        }
        elseif (isset($_POST['delete'])) {
            $id = $_POST['id'];

            $sql = "DELETE FROM clanci WHERE id = ?";
            $stmt = mysqli_prepare($dbc, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Neuspijelo brisanje!";
            }
            mysqli_stmt_close($stmt);
        }
        elseif (isset($_POST['prijava'])) {
            $pokusaj_prijave = true;
            $korisnicko_ime = $lozinka = null;
            if (isset($_POST['korisnicko_ime'])) {
                $korisnicko_ime = $_POST['korisnicko_ime'];
            }
            if (isset($_POST['lozinka'])) {
                $lozinka = $_POST['lozinka'];
            }

            if ($korisnicko_ime && $lozinka) {
                $sql = "SELECT korisnicko_ime, lozinka, razina FROM korisnik WHERE korisnicko_ime = ?";
                $stmt = mysqli_prepare($dbc, $sql);
                mysqli_stmt_bind_param($stmt, "s", $korisnicko_ime);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) > 0) {
                        mysqli_stmt_bind_result($stmt, $korisnicko_ime, $lozinka_hash, $razina);
                        $ispravna_lozinka = false;
                        if (mysqli_stmt_fetch($stmt)){
                            $ispravna_lozinka = password_verify($lozinka, $lozinka_hash);
                        }

                        if ($ispravna_lozinka) {
                            $_SESSION['korisnik'] = $korisnicko_ime;
                            $_SESSION['razina_prava'] = $razina;
                        }
                    }
                }
            } 
        }
        elseif (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
        }
    }

    $korisnik_prijavljen = isset($_SESSION['korisnik']) && isset($_SESSION['razina_prava']);
    $admin_prijavljen = $korisnik_prijavljen && $_SESSION['razina_prava'] == 1;

    if($admin_prijavljen) {
        $msg = "Imate administratorske ovlasti.";
    }
    elseif ($korisnik_prijavljen) {
        $msg = "Nažalost nemate pravo na administriranje sadržaja.";
    }
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <title>Administracija</title>
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
                        <li><a href="kategorija.php?id=Sport"><span class="content-text">SPORT</span></a></li>
                        <li><a href="kategorija.php?id=Kultura"><span class="content-text">KULTURA</span></a></li>
                        <li><a href="unos.php"><span class="content-text">UNOS</span></a></li>
                        <li><a href="administracija.php"><span class="content-text">ADMINISTRACIJA</span></a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
                <?php
                    if ($korisnik_prijavljen) {
                        echo '<div class="login-confirmation">';
                        echo '<div class="login-info">';
                        echo '<p class="content-text">Dobrodošli, <span class="greet-user">'. $_SESSION['korisnik'] .  '</span></p>';
                        if (!$admin_prijavljen) {
                            echo '<p class="content-text">Nažalost nemate pravo na administriranje sadržaja.</p>';
                        }
                        echo '</div>';
                        echo '<form name="logout" action="administracija.php" method="post">';
                        echo '<button type="submit" name="logout" value="Odjavi se" class="btn-logout"><span class="content-text">Odjavi se</span></button>';
                        echo '</form>';
                        echo '</div>';

                        if ($admin_prijavljen) {
                            echo '<section class="form-container">';
                            echo '<h3 class="content-text section-heading">Uređivanje članaka</h3>';
                            while($row = mysqli_fetch_array($result)) {
                                echo '<form name="edit-article" class="article-form edit-form" action="administracija.php" method="POST" enctype="multipart/form-data">';
                                echo '<div class="form-item">';
                                echo '<label for="naslov" class="content-text">Naslov članka</label>';
                                echo '<input type="text" name="naslov" id="naslov" value="' . $row['naslov'] . '" class="input-heading content-text" required maxlength="90">';
                                echo '</div>';
                                echo '<div class="form-item">';
                                echo '<label for="sazetak" class="content-text">Sažetak članka</label>';
                                echo '<textarea name="sazetak" id="sazetak" class="summary content-text" required maxlength="300">' . $row['sazetak'] . '</textarea>';
                                echo '</div>';
                                echo '<div class="form-item">';
                                echo '<label for="tekst" class="content-text">Tekst članka</label>';
                                echo '<textarea name="tekst" id="tekst" class="full-text content-text" required>' . $row['tekst'] . '</textarea>';
                                echo '</div>';
                                echo '<div class="form-item">';
                                echo '<label for="kategorija" class="content-text">Kategorija članka</label>';
                                echo '<select name="kategorija" id="kategorija" class="form-category content-text" required>';
                                echo '<option value="Kultura" class="content-text"' . ($row['kategorija'] == "Kultura" ? " selected" : "") . '>Kultura</option>';
                                echo '<option value="Sport" class="content-text"' . ($row['kategorija'] == "Sport" ? " selected" : "") . '>Sport</option>';
                                echo '</select>';
                                echo '</div>';
                                echo '<div class="form-item">';
                                echo '<label for="slika" class="content-text">Odaberite sliku:</label>';
                                echo '<input type="file" name="slika" id="slika" accept="image/*">';
                                echo '<input type ="hidden" name="stara-slika" value="' . $row['slika'] . '">';
                                echo '<p class="content-text">Trenutna slika:</p>';
                                echo '<img src="' . UPLPATH . $row['slika'] . '" alt="pregled trenutne slike" width="100px" height="75px">';
                                echo '</div>';
                                echo '<div class="checkbox">';
                                echo '<label for="arhiviraj" class="content-text">Spremiti u arhivu:</label>';

                                $checked = $row['arhiva'] ? " checked" : "";
                                echo '<input type="checkbox" name="arhiviraj" id="arhiviraj" value="arhiviraj"' . $checked . '>';
                                echo '</div>';

                                echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                                echo '<div class="form-buttons">';
                                echo '<button type="submit" name="update" value="Izmjeni" id="izmjena" class="btn-submit"><span class="content-text">Izmjeni</span></button>';
                                echo '<button type="submit" name="delete" value="Izbriši" id="brisanje" class="btn-delete"><span class="content-text">Izbriši</span></button>';
                                echo '<button type="reset" name="reset" value="Poništi" id="reset" class="btn-reset"><span class="content-text">Poništi</span></button>';
                                echo '</div>';
                                echo '</form>';
                            }
                            echo '</section>';
                        }
                    }
                    else {
                        echo '<section class="form-container">';
                        if ($pokusaj_prijave) {
                            echo '<p class="content-text failed-login">Krivo korisničko ime i/ili lozinka.</p>';
                        }
                        echo '<form name="user-login" class="user-form" action="administracija.php" method="POST">';
                        echo '<h3 class="content-text user-form-heading">Prijava</h3>';
                        echo '<div class="form-item">';
                        echo '<label for="korIme" class="content-text">Korisničko ime</label>';
                        echo '<input type="text" name="korisnicko_ime" id="korIme" class="user-data content-text" maxlength="32">';
                        echo '<p id="korIme-upozorenje" class="form-warning"></p>';
                        echo '</div>';
                        echo '<div class="form-item">';
                        echo '<label for="lozinka" class="content-text">Lozinka</label>';
                        echo '<input type="password" name="lozinka" id="lozinka" class="user-data content-text">';
                        echo '<p id="loz-upozorenje" class="form-warning"></p>';
                        echo '</div>';
                        echo '<button type="submit" name="prijava" id="prijava" value="Prijavi se" class="btn-user"><span class="content-text">Prijavi se</span></button>';
                        echo '<p class="content-text user-tip">Nemate korisnički račun? <a href="registracija.php?izvor=administracija" class="user-link">Registrirajte se</a></p>';
                        echo '</form>';
                        echo '</section>';
                    }
                ?>
        </main>

        <footer>
            <div class="footer-content">
                <p class="content-text">© 2026 Mislav Krajačić</p>
                <p class="content-text">e-mail: mkrajacic@tvz.hr</p>
            </div>
        </footer>

        <script src="../skripte/validacijaLogin.js"></script>
    </body>


    
</html>

<?php
    mysqli_close($dbc);
?>