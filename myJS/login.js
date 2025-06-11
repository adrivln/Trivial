function logear(){
    this.event.preventDefault();
    let nombre = document.getElementById("nameL").value.trim();
    let password = document.getElementById("passL").value.trim();
    let resultado = document.getElementById("resultado");

    $.ajax({
        method: "POST",
        url: "/myPHP/login.php",
        data: {"nom": nombre, "password": password},
        dataType : "json",
        success: function (data){
            if(data.estat === "OK"){
                resultado.innerText = "Sessió iniciada correctament " + data.usuari_app;
                localStorage.setItem("session", data.usuari_app);
                resultado.style.color = "green";
                setTimeout(() => window.location.href = "Inicio.html", 800);
            } else{
                resultado.style.color = "red";
                resultado.innerText = data.error;

            }
        },
        error: function(jqXHR, status, err) {
            resultado.style.color = "red";
            resultado.innerHTML = jqXHR.responseText;
        }
    })
}
