function registro() {
    this.event.preventDefault();
    let nombre = document.getElementById("nameR").value;
    let password = document.getElementById("passR").value;
    let resultado = document.getElementById("resultado");

    $.ajax({
        type: "POST",
        url: "/myPHP/registro.php",
        data: {"nom": nombre, "password": password},
        dataType: "json",
        success: function (data) {
            if (data.estat === "OK") {
                resultado.innerText= "Registrat amb exit!";
                resultado.style.color = "green";
                setTimeout(() => window.location.href = "Login.html", 800);
            } else {
                resultado.innerText = data.error;
                resultado.style.color = "red";
            }
        },
        error: function (jqXHR, textStatus, err) {
            resultado.style.color = "red";
            resultado.innerHTML = jqXHR.responseText;
        }
    });
}