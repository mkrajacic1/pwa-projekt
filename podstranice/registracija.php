<?php
    $registracija_provedena = false;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $registracija_uspjesna = $podudaranje_lozinka = $novi_korisnik = false;
        $ime = $prezime = $korisnicko_ime = $lozinka = null;
        $msg = "Greška pri registraciji.";

        if (isset($_POST["ime"])) {
            $ime = $_POST["ime"];
        }
        if (isset($_POST["prezime"])) {
            $prezime = $_POST["prezime"];
        }
        if (isset($_POST["korisnicko_ime"])) {
            $korisnicko_ime = $_POST["korisnicko_ime"];
        }
        if (isset($_POST["lozinka"])) {
            $lozinka = $_POST["lozinka"];

            if (isset($_POST['potvrda_lozinke']) && $lozinka == $_POST['potvrda_lozinke']) {
                $podudaranje_lozinka = true;
            }
            else {
                $msg = "Lozinke se ne podudaraju, neuspijela registracija.";
            }
        }

        if ($korisnicko_ime and $podudaranje_lozinka) {
            include '../skripte/connect.php';

            $sql = "SELECT id FROM korisnik WHERE korisnicko_ime = ?";
            $stmt = mysqli_prepare($dbc, $sql);
            mysqli_stmt_bind_param($stmt, "s", $korisnicko_ime);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $msg = "Korisnik već postoji.";
                }
                else {
                    $novi_korisnik = true;
                }
            }
            mysqli_stmt_close($stmt);

            if ($novi_korisnik) {
                $lozinka_hash = password_hash($lozinka, CRYPT_BLOWFISH);
                $razina = 0;

                $sql = "INSERT INTO korisnik (ime, prezime, korisnicko_ime, lozinka, razina) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($dbc, $sql);
                mysqli_stmt_bind_param($stmt, "ssssi", $ime, $prezime, $korisnicko_ime, $lozinka_hash, $razina);
                if (mysqli_stmt_execute($stmt)) {
                    $registracija_uspjesna = true;
                    $msg = "Registracija uspješna!";
                }
                mysqli_stmt_close($stmt);
            }

            mysqli_close($dbc);
        }

        $izvor = 'administracija';
        if (isset($_POST['izvor'])) {
            $poslano = $_POST['izvor'];
            if ($poslano == 'unos') {
                $izvor = 'unos';
            }
        }

        $registracija_provedena = true;
    }   
    else {
        $izvor = 'administracija';
        if (isset($_GET['izvor'])) {
            $poslano = $_GET['izvor'];
            if ($poslano == 'unos') {
                $izvor = 'unos';
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <title>Registracija</title>
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
            <section class="form-container">
                <form name="register-user" class="user-form" action="registracija.php" method="POST">
                    <h3 class="content-text user-form-heading">Registriracija</h3>
                    <?php echo '<input type="hidden" name="izvor" value="' . $izvor . '">';?>

                    <div class="form-item">
                        <label for="ime" class="content-text">Ime</label>
                        <input type="text" name="ime" id="ime" class="user-data content-text" maxlength="32">
                        <p id="ime-upozorenje" class="form-warning"></p>
                    </div>
                    <div class="form-item">
                        <label for="prezime" class="content-text">Prezime</label>
                        <input type="text" name="prezime" id="prezime" class="user-data content-text" maxlength="32">
                        <p id="prezime-upozorenje" class="form-warning"></p>
                    </div>
                    <div class="form-item">
                        <label for="korIme" class="content-text">Korisničko ime</label>
                        <input type="text" name="korisnicko_ime" id="korIme" class="user-data content-text" maxlength="32">
                        <p id="korIme-upozorenje" class="form-warning"></p>
                    </div>
                    <div class="form-item">
                        <label for="lozinka" class="content-text">Lozinka</label>
                        <input type="password" name="lozinka" id="lozinka" class="user-data content-text">
                        <p id="loz-upozorenje" class="form-warning"></p>
                    </div>
                    <div class="form-item">
                        <label for="potvrda_lozinke" class="content-text">Potvrdite lozinku</label>
                        <input type="password" name="potvrda_lozinke" id="potvrda_lozinke" class="user-data content-text">
                        <p id="lozPotvrda-upozorenje" class="form-warning"></p>
                    </div>
                    <button type="submit" class="btn-user" name="registracija" id="registracija" value="registracija"><span class="content-text">Registriraj se</span></button>
                    
                    <?php echo '<p class="content-text user-tip">Već ste registrirani? <a href="' . $izvor . '.php' . '" class="user-link">Prijavite se</a></p>'; ?>
                </form>
                <?php
                if ($registracija_provedena) {
                    if ($registracija_uspjesna) {
                        echo '<p class="registration-msg content-text">' . $msg . ' <a href="' . $izvor . '.php' . '" class="user-link">Prijavite se</a></p>';
                    }
                    else {
                        echo '<p class="registration-msg content-text" style="color:#db3636;">' . $msg . '</p>';
                    }
                }
                ?>
            </section>
        </main>

        <footer>
            <div class="footer-content">
                <p class="content-text">© 2026 Mislav Krajačić</p>
                <p class="content-text">e-mail: mkrajacic@tvz.hr</p>
            </div>
        </footer>

        <script src="../skripte/validacijaReg.js"></script>
    </body>
</html>