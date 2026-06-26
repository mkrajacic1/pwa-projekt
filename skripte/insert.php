<?php
    function unesi_clanak($dbc, $datum, $naslov, $sazetak, $slika, $tekst, $kategorija, $arhiva) {
        $sql = "INSERT INTO clanci (datum, naslov, sazetak, slika, tekst, kategorija, arhiva) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $stmt = mysqli_prepare($dbc, $sql);

        mysqli_stmt_bind_param($stmt, "ssssssi", $datum,  $naslov, $sazetak, $slika, $tekst, $kategorija, $arhiva);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Neuspijeli unos!";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($dbc);
    }
?>