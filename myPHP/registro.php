<?php
require "connection.php";
global $conn;
$email = $_POST["nom"];
$password = $_POST["password"];

//Validacion de campos vacios
if (trim($email) === "" || trim($password) === "") {
    print_r(json_encode(array("estat" => "KO", "error" => "Tots els camps són obligatoris.")));
    exit;
}

//Dominio permitido
if (!str_ends_with($email, "@ies-sabadell.cat")) {
    print_r(json_encode(array("estat" => "KO", "error" => "Correu incorrecte.")));
    exit;
}

try{
    $query = $conn->prepare("SELECT * FROM usuaris_app WHERE nom = :nom");
    $query->bindParam(":nom", $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($query->rowCount() == 1) {
       print_r(json_encode(array("estat" => "KO", "error" => "L'usuari existeix.")));
    } else if (strlen($password) < 5) {
        print_r(json_encode(array("estat" => "KO", "error" => "La contrasenya ha de ser de 5 caracters mínim.")));
    } else {
        // Inserto usuario
        $query = $conn->prepare("INSERT INTO usuaris_app (nom, password, punts) VALUES (:nom, :password, 0)");
        $query->bindParam(":nom", $email, PDO::PARAM_STR);
        $query->bindParam(":password", $password, PDO::PARAM_STR);
        $query->execute();

        echo json_encode(["estat" => "OK", "error" => "", "usuari_app" => $email]);
    }

} catch (Exception $e) {
    echo $e->getMessage();
}