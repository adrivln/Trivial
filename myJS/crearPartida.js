function crearPartida(){
    this.event.preventDefault();
    let description = document.getElementById("description").value;
    let resultado = document.getElementById("resultado");
    $.ajax({
        method: "POST",
        url: "/myPHP/crearPartida.php",
        data: {"description": description},
        dataType: "json",
        success: function (data) {
            if(data.estat === "OK"){
                resultado.innerText = "Partida afegida amb exit!"
                resultado.style.color = "green";
            } else{
                resultado.innerText = data.error;
                resultado.style.color = "red";
            }

        },
        error: function (jqXHR, textStatus, error) {
            resultado.innerHTML = jqXHR.responseText;
        }
    });
}