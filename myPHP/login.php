<?php
require "connection.php";
global $conn;
$email = $_POST["nom"];
$password = $_POST["password"];

//Valido campos vacios
if (trim($email) === "" || trim($password) === "") {
    print_r(json_encode(array("estat" => "KO", "error" => "Tots els camps són obligatoris.")));
    exit;
}
try{
    $query = $conn->prepare("SELECT * FROM usuaris_app WHERE nom = :nom");
    $query->bindParam(":nom", $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if($query->rowCount() == 1){
        if($result["password"] == $password){
            print_r(json_encode(Array("estat" => "OK", "error" => "", "usuari_app" => $email)));
        } else {
            print_r(json_encode(Array("estat" => "KO", "error" => "Credencial incorrecte.", "usuari_app" => $email)));
        }
    } else {
        print_r(json_encode(Array("estat" => "KO", "error" => "Usuari no existeix", "usuari_app" => $email)));

    }

} catch(Exception $e) {
    echo $e->getMessage();
}