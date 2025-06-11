let angleAcumulat = 0;

function clica() {
    document.getElementById("mensaje").innerText = ""; //Añadido para eliminar el mensaje de validacion

    document.getElementById("pieContainer").style.animation = "none";
    document.getElementById("resultat").innerText = "";
    let voltes = Math.floor(Math.random() * 5) + 15;
    let angleFinal = angleAcumulat + (voltes * 360 + Math.floor(Math.random() * 360));

    let angleRelatiu = (angleFinal % 360);

    // Determinar la secció seleccionada
    let seccio = "";
    let seccio2 = "";
    if (angleRelatiu >= 0 && angleRelatiu < 60) {
        seccio = "Hotpink";
    } else if (angleRelatiu >= 60 && angleRelatiu < 120) {
        seccio = "Vermell";
    } else if (angleRelatiu >= 120 && angleRelatiu < 180) {
        seccio = "Groc";
    } else if (angleRelatiu >= 180 && angleRelatiu < 240) {
        seccio = "Lime";
    } else if (angleRelatiu >= 240 && angleRelatiu < 300) {
        seccio = "Cyan";
    } else {
        seccio = "Fuchsia";
    }

    if (angleRelatiu >= 0 && angleRelatiu < 90) {
        seccio2 = "Groc";
    } else if (angleRelatiu >= 90 && angleRelatiu < 180) {
        seccio2 = "Lime";
    } else if (angleRelatiu >= 180 && angleRelatiu < 270) {
        seccio2 = "Cyan";
    } else {
        seccio2 = "Fuchsia";
    }

    let nomAnimacio = "gira_" + Date.now();
    let estil = document.createElement("style");
    estil.innerHTML = "@keyframes " + nomAnimacio + " {" +
        "from { transform: rotate(0deg); }" +
        " to { transform: rotate(" + angleFinal + "deg); }" + "}";

    document.head.appendChild(estil);
    document.getElementById("pieContainer").style.animation = nomAnimacio + " 5s ease-out forwards";
    document.getElementById("pieContainer2").style.animation = nomAnimacio + " 5s ease-out forwards";
    angleAcumulat = angleRelatiu;
    setTimeout(() => {
        document.getElementById("resultat").innerHTML = seccio + "<br/>";
        document.getElementById("resultat").innerHTML += seccio2 + "<br/>";
        gestionarResultado();
    }, 5000);

}

function validarPartida() {
    this.event.preventDefault();

    let id = document.getElementById("partidaId").value;
    let sesion = localStorage.getItem("session");
    let mensaje = document.getElementById("mensaje");
    let boton = document.getElementById("btnGirar");

    //Login obligatorio
    if (!sesion) {
        mensaje.style.color = "red";
        mensaje.innerText = "Has de fer login abans.";
        return;
    }
    //ID obligatorio
    if (!id) {
        mensaje.style.color = "red";
        mensaje.innerText = "Introdueix l'ID de la partida.";
        return;
    }

    $.ajax({
        method: "POST",
        url: "/myPHP/validarPartida.php",
        data: {"id": id, "usuari": sesion},
        dataType: "json",
        success: function (data) {
            if (data.estat === "OK") {
                mensaje.style.color = "green";
                mensaje.innerText = "Validat: ja pots girar la ruleta!";
                boton.disabled = false;
            } else {
                mensaje.style.color = "red";
                mensaje.innerText = data.error;
                boton.disabled = true;
            }
        },
        error: function (jqXHR, status, err) {
            mensaje.style.color = "red";
            mensaje.innerText = "Error de connexió amb el servidor.";
            boton.disabled = true;
        }
    });
}

let tema = 0;

function gestionarResultado() {
    // leo el contenido de clica y lo guardo
    let contingut = document.getElementById("resultat").innerText.trim().split("\n");
    let temaColor = contingut[0];
    let accioColor = contingut[1];


    if (temaColor === "Fuchsia") {
        tema = 1;
    } else if (temaColor === "Cyan") {
        tema = 2;
    } else if (temaColor === "Lime") {
        tema = 3;
    } else if (temaColor === "Groc") {
        tema = 4;
    } else if (temaColor === "Vermell") {
        tema = 5;
    } else if (temaColor === "Hotpink") {
        tema = 6;
    }

    //gestiono si es suma o resta
    if (accioColor === "Groc" || accioColor === "Lime") {
        sumarRestarPuntos(accioColor);
    } else {
        // por defecto gestiono que es pregunta
        let esFormatge = (accioColor === "Cyan");
        gestionarPreguntas(tema, esFormatge);
    }

    return tema;

}

function sumarRestarPuntos(accioColor) {
    let usuari = localStorage.getItem("session");
    let mensaje = document.getElementById("mensaje");
    let puntos;

    if (accioColor === "Groc") {
        puntos = 50;
    } else { //Lime
        puntos = -50;
    }

    $.ajax({
        method: "POST",
        url: "/myPHP/actualizarPuntos.php",
        data: {"usuari": usuari, "puntos": puntos},
        dataType: "json",
        success: function (data) {
            if (data.estat === "OK") {
                mensaje.style.color = "green";
                mensaje.innerText = "Punts actualitzats! Ara tens: " + data.nousPunts;
            } else {
                mensaje.style.color = "red";
                mensaje.innerText = data.error;
            }
        },
        error: function (jqXHR, status, err) {
            mensaje.style.color = "red";
            mensaje.innerText = jqXHR.responseText;
        }
    });
}

function gestionarPreguntas(tema, esFormatge) {
    let mensaje = document.getElementById("mensaje");
    let resultat = document.getElementById("resultat");

    mensaje.innerText = "";
    resultat.innerHTML = "";

    let formatge = "0";
    if (esFormatge) {
        formatge = "1";
    }

    $.ajax({
        method: "POST",
        url: "/myPHP/obtenirPregunta.php",
        data: {"tema": tema, "formatge": formatge},
        dataType: "json",
        success: function (data) {
            if (data.estat === "OK") {
                resultat.innerHTML = "";
                
                //creo titulo
                let titol = document.createElement("h4");
                titol.innerText = data.descripcio;
                resultat.appendChild(titol);

                //creo lista
                let lista = document.createElement("ul");
                lista.classList.add("list-group", "text-start");

                //funcion para cada respuesta
                function afegirResposta(text, idx) {
                    let li = document.createElement("li");
                    li.classList.add("list-group-item", "d-flex", "align-items-center");
                    li.innerHTML = `
                                     <input type="radio"
                                        name="opcioPregunta"
                                        value="${idx}"
                                        class="form-check-input me-2">
                                     <span class="respuesta-text">${text}</span>
                                        `;
                    //funcion para marcar de verde respuesta seleccionada
                    li.onclick = function() {
                        lista.querySelectorAll("li").forEach(item =>
                            item.classList.remove("list-group-item-success")
                        );
                        li.classList.add("list-group-item-success");
                        li.querySelector("input").checked = true;
                    };
                    lista.appendChild(li);
                }

                afegirResposta(data.resposta1, 1);
                afegirResposta(data.resposta2, 2);
                afegirResposta(data.resposta3, 3);
                afegirResposta(data.resposta4, 4);

                resultat.appendChild(lista);

                // creo el boton de comprovar respuesta
                let btn = document.createElement("button");
                btn.classList.add("btn", "btn-secondary", "mt-2");
                btn.innerText = "Comprovar resposta";
                btn.onclick = function () {
                    comprovarResposta(data.idpreguntes, tema, esFormatge);
                };
                resultat.appendChild(btn);
            } else {
                mensaje.style.color = "red";
                mensaje.innerText = data.error;
            }
        },
        error: function (jqXHR, status, err) {
            mensaje.style.color = "red";
            mensaje.innerText = jqXHR.responseText;
        }
    });
}

function comprovarResposta(idpreguntes, tema, esFormatge) {
    let radios = document.getElementsByName("opcioPregunta");
    let seleccionada = document.querySelector("input[name='opcioPregunta']:checked");
    let mensaje = document.getElementById("mensaje");
    let resultat= document.getElementById("resultat");
    if (!seleccionada) {
        mensaje.style.color = "red";
        mensaje.innerText = "Has de seleccionar una opció.";
        return;
    }

    // si es respuesta correcta
    function sumar50Punts(callback) {
        $.ajax({
            method: "POST",
            url: "/myPHP/actualizarPuntos.php",
            data: {
                "usuari": localStorage.getItem("session"),
                "puntos": 50    // sempre +50 al encertar pregunta
            },
            dataType: "json",
            success: function(pData) {
                if (pData.estat === "OK") {
                    callback(pData.nousPunts);
                } else {
                    mensaje.style.color = "red";
                    mensaje.innerText = pData.error;
                }
            },
            error: function() {
                mensaje.style.color = "red";
                mensaje.innerText = "Error de connexió al servidor.";
            }
        });
    }

    let formatgeVal = "0";
    if (esFormatge === true) {
        formatgeVal = "1";
    }

    $.ajax({
        method: "POST",
        url: "/myPHP/comprovarResposta.php",
        data: {
            "idpregunta": idpreguntes,
            "eleccio": seleccionada.value,
            "formatge": formatgeVal,
            "partidaId": document.getElementById("partidaId").value,
            "usuari": localStorage.getItem("session")
        },
        dataType: "json",
        success: function (data) {
            if (data.correcte) {
                // primero, sumo los 50 puntos
                sumar50Punts(function(nousPunts) {
                    let text = "Resposta correcta! Has sumat 50 punts (Total: " + nousPunts + ").";
                    if (data.formatge) {
                        text += " Has guanyat un formatge (" + data.countFormatges + "/6).";
                        if (data.partidaAcabada) {
                            text += " Has completat tots els formatges! Felicitats!";
                        }
                    }
                    mensaje.style.color = "green";
                    mensaje.innerText = text;
                    // Deshabilito boton y opciones
                    for (let j = 0; j < radios.length; j++) radios[j].disabled = true;
                    let btns = resultat.getElementsByTagName("button");
                    if (btns.length > 0) btns[0].disabled = true;
                });
            } else {
                mensaje.style.color = "red";
                mensaje.innerText = data.error;
                for (let j = 0; j < radios.length; j++) radios[j].disabled = true;
                let btns = resultat.getElementsByTagName("button");
                if (btns.length > 0) btns[0].disabled = true;
            }
        },
        error: function () {
            mensaje.style.color = "red";
            mensaje.innerText = "Error de connexió al servidor.";
        }
    });
}
