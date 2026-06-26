document.getElementById("prijava").onclick = function(event) {
    let slanjeForme = true;
    
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

    // Lozinka mora biti unesena
    const poljePass = document.getElementById("lozinka"); 
    const pass = document.getElementById("lozinka").value;
    if (pass.length == 0) {
        slanjeForme = false;
        poljePass.style.border="1px solid red"; 
        document.getElementById("loz-upozorenje").innerText="Unos lozinke obavezan!";
    } else { 
        poljePass.style.border="1px solid #29ce00"; 
        document.getElementById("loz-upozorenje").innerText="";
    }

    if (!slanjeForme) { 
        event.preventDefault(); 
    } 
};