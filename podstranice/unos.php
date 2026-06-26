<?php 
    session_start();
    include '../skripte/connect.php';

    $pokusaj_prijave = false;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['prijava'])) {
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
        <title>Unos</title>
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
                        echo '<p class="content-text">Dobrodošli, <span class="greet-user">' . $_SESSION['korisnik'] .  "</span></p>";
                        if (!$admin_prijavljen) {
                            echo '<p class="content-text">Nažalost nemate pravo na administriranje sadržaja.</p>';
                        }
                        echo '</div>';
                        echo '<form name="logout" action="unos.php" method="post">';
                        echo '<button type="submit" name="logout" value="Odjavi se" class="btn-logout"><span class="content-text">Odjavi se</span></button>';
                        echo '</form>';
                        echo '</div>';

                        if ($admin_prijavljen) {
                            echo '<section class="form-container">
                                    <h3 class="content-text section-heading">Unos članka</h3>
                                    <form name="create-article" class="article-form" action="../skripte/skripta.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-item">
                                            <label for="naslov" class="content-text">Naslov članka</label>
                                            <input type="text" name="naslov" id="naslov" class="input-heading content-text" required maxlength="90">
                                        </div>
                                        <div class="form-item">
                                            <label for="sazetak" class="content-text">Sažetak članka</label>
                                            <textarea name="sazetak" id="sazetak" class="summary content-text" required maxlength="300"></textarea>
                                        </div>
                                        <div class="form-item">
                                            <label for="tekst" class="content-text">Tekst članka</label>
                                            <textarea name="tekst" id="tekst" class="full-text content-text" required></textarea>
                                        </div>
                                        <div class="form-item">
                                            <label for="kategorija" class="content-text">Kategorija članka</label>
                                            <select name="kategorija" id="kategorija" class="form-category content-text" required>
                                                <option value="" class="content-text" selected disabled hidden>Odaberite opciju</option>
                                                <option value="Kultura" class="content-text">Kultura</option>
                                                <option value="Sport" class="content-text">Sport</option>
                                            </select>
                                        </div>
                                        <div class="form-item">
                                            <label for="slika" class="content-text">Odaberite sliku:</label>
                                            <input type="file" name="slika" id="slika" class="file-upload" accept="image/*" required>
                                        </div>
                                        <div class="checkbox">
                                            <label for="arhiviraj" class="content-text">Spremiti u arhivu:</label>
                                            <input type="checkbox" name="arhiviraj" id="arhiviraj" value="arhiviraj">
                                        </div>
                                        <div class="form-buttons">
                                            <button type="submit" name="Unesi" id="unos" value="unos" class="btn-submit"><span class="content-text">Unesi članak</span></button>
                                            <button type="reset"  name="reset" value="Poništi" id="reset" class="btn-reset"><span class="content-text">Očisti unos</span></button>
                                        </div>
                                    </form>
                                </section>';
                        }
                }
                else {
                    echo '<section class="form-container">';
                    if ($pokusaj_prijave) {
                        echo '<p class="content-text failed-login">Krivo korisničko ime i/ili lozinka.</p>';
                    }
                    echo '<form name="user-login" class="user-form" action="unos.php" method="POST">';
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
                    echo '<p class="content-text user-tip">Nemate korisnički račun? <a href="registracija.php?izvor=unos" class="user-link">Registrirajte se</a></p>';
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