document.getElementById("registracija").onclick = function(event) { 
    let slanjeForme = true; 

    // Ime korisnika mora biti uneseno 
    const poljeIme = document.getElementById("ime"); 
    const ime = document.getElementById("ime").value; 
    if (ime.length == 0) { 
        slanjeForme = false; 
        poljeIme.style.border="1px solid red"; 
        document.getElementById("ime-upozorenje").innerText="Unos imena obavezan!"; 
    } else { 
        poljeIme.style.border="1px solid #29ce00";
        document.getElementById("ime-upozorenje").innerText="";
    } 

    // Prezime korisnika mora biti uneseno 
    const poljePrezime = document.getElementById("prezime"); 
    const prezime = document.getElementById("prezime").value;
    if (prezime.length == 0) { 
        slanjeForme = false; 
        poljePrezime.style.border="1px solid red"; 
        document.getElementById("prezime-upozorenje").innerText="Unos prezimena obavezan!"; 
    } else { 
        poljePrezime.style.border="1px solid #29ce00"; 
        document.getElementById("prezime-upozorenje").innerText=""; 
    } 
    
    // Korisničko ime mora biti uneseno 
    const poljeUsername = document.getElementById("korIme"); 
    const username = document.getElementById("korIme").value; 
    if (username.length == 0) { 
        slanjeForme = false; 
        poljeUsername.style.border="1px solid red"; 
        document.getElementById("korIme-upozorenje").innerText="Unos korisničkog imena obavezan!"; 
    } else { 
        poljeUsername.style.border="1px solid #29ce00"; 
        document.getElementById("korIme-upozorenje").innerText=""; 
    } 

    // Provjera podudaranja lozinki 
    const poljePass = document.getElementById("lozinka"); 
    const pass = document.getElementById("lozinka").value; 
    const poljePassRep = document.getElementById("potvrda_lozinke"); 
    const passRep = document.getElementById("potvrda_lozinke").value; 
    if (pass != passRep) { 
        slanjeForme = false; 
        poljePass.style.border="1px solid red"; 
        poljePassRep.style.border="1px solid red"; 
        document.getElementById("loz-upozorenje").innerText="Lozinke se ne podudaraju!"; 
        document.getElementById("lozPotvrda-upozorenje").innerText="Lozinke se ne podudaraju!"; 
    } else if (pass.length == 0) {
        slanjeForme = false;
        poljePass.style.border="1px solid red"; 
        poljePassRep.style.border="1px solid red"; 
        document.getElementById("loz-upozorenje").innerText="Unos lozinke obavezan!"; 
        document.getElementById("lozPotvrda-upozorenje").innerText="Potvrdite lozinku!";
    } else { 
        poljePass.style.border="1px solid #29ce00"; 
        poljePassRep.style.border="1px solid #29ce00"; 
        document.getElementById("loz-upozorenje").innerText=""; 
        document.getElementById("lozPotvrda-upozorenje").innerText="";
    } 

    if (!slanjeForme) { 
        event.preventDefault(); 
    } 
};