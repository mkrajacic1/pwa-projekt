<?php
    function date_text_format ($datum) {
        $broj_mj_u_ime = [
                "01" => "siječnja",
                "02" => "veljače",
                "03" => "ožujka",
                "04" => "travnja",
                "05" => "svibnja",
                "06" => "lipnja",
                "07" => "srpnja",
                "08" => "kolovoza",
                "09" => "rujna",
                "10" => "listopada",
                "11" => "studenog",
                "12" => "prosinca"
            ];

            $god_mj_dan = explode("-", $datum);
            $datum = $god_mj_dan[2] . ". " . $broj_mj_u_ime[$god_mj_dan[1]] . " " . $god_mj_dan[0] . ".";

            return $datum;
    }
?>