function crearPregunta() {
    this.event.preventDefault();
    let descripcio = document.getElementById("descripcio").value;
    let tema = document.getElementById("tema").value;
    let resp1 = document.getElementById("resp1").value;
    let resp2 = document.getElementById("resp2").value;
    let resp3 = document.getElementById("resp3").value;
    let resp4 = document.getElementById("resp4").value;
    let solucio = document.getElementById("solucio").value;
    let resultado = document.getElementById("resultado");

    $.ajax({
        method: "POST",
        url: "../myPHP/afegirPreguntes.php",
        data: {
            "descripcio": descripcio,
            "tema": tema,
            "resp1": resp1,
            "resp2": resp2,
            "resp3": resp3,
            "resp4": resp4,
            "solucio": solucio
        },
        dataType: "json",
        success: function (data) {
            if (data.estat === "OK") {
                resultado.innerText= "Pregunta afegida amb exit!";
                resultado.style.color = "green";
            } else {
                resultado.innerText = data.error;
                resultado.style.color = "red";
            }
        },
        error: function (jqXHR, textStatus, error) {
            resultado.innerHTML = jqXHR.responseText;
        }
    });
}

function modificarPregunta() {
    this.event.preventDefault();
    let descripcio = document.getElementById("descripcio").value;
    let tema = document.getElementById("tema").value;
    let resp1 = document.getElementById("resp1").value;
    let resp2 = document.getElementById("resp2").value;
    let resp3 = document.getElementById("resp3").value;
    let resp4 = document.getElementById("resp4").value;
    let solucio = document.getElementById("solucio").value;
    let id = document.getElementById("id").value;
    let resultado = document.getElementById("resultado");
    $.ajax({
        method: "POST",
        url: "../myPHP/modificarPreguntes.php",
        data: {
            "descripcio": descripcio,
            "tema": tema,
            "resp1": resp1,
            "resp2": resp2,
            "resp3": resp3,
            "resp4": resp4,
            "solucio": solucio,
            "id": id
        },
        dataType: "json",
        success: function (data) {
            if(data.estat === "OK"){
                resultado.innerText = "Modificació actualitzada correctament.";
                resultado.style.color = "green";
            } else {
                resultado.innerText = data.error;
                resultado.style.color = "red";
            }

        },
        error: function (jqXHR, textStatus, error) {
            resultado.innerHTML = jqXHR.responseText;
        }
    });
}