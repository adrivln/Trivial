// mostrar o no boton de logout
const logoutNavItem = document.getElementById("nav-logout");
if (localStorage.getItem("session")) {
    if (logoutNavItem) logoutNavItem.style.display = "block";
} else {
    if (logoutNavItem) logoutNavItem.style.display = "none";
}

// funcion logout
function logout() {
    localStorage.removeItem("session");
    sessionStorage.setItem("flashMessage", "Sessió tancada correctament.");

}