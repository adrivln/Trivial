<?php
require "connection.php";
global $conn;

$usuari = $_POST["usuari"];
$puntos = $_POST["puntos"];

if(!$usuari){
    print_r(json_encode(array("estat" => "KO", "error" => "Usuari no especificat.")));
    exit;
}

try{
    //sumar o restar
    $query = $conn->prepare("UPDATE usuaris_app SET punts = punts + :puntos WHERE nom = :usuari");
    $query->bindParam(":puntos", $puntos, PDO::PARAM_INT);
    $query->bindParam(":usuari", $usuari, PDO::PARAM_STR);
    $ok = $query->execute();

    if($ok){
        $query2 = $conn->prepare("SELECT punts FROM usuaris_app WHERE nom = :usuari");
        $query2->bindParam(":usuari", $usuari, PDO::PARAM_STR);
        $query2->execute();
        $row = $query2->fetch(PDO::FETCH_ASSOC);
        print_r(json_encode(array("estat" => "OK", "nousPunts" => $row["punts"])));
    } else {
        print_r(json_encode(array("estat" => "KO", "error" => "No s'ha pogut actualitzar els punts.")));
    }

} catch (Exception $e){
    print_r(json_encode(array("estat" => "KO", "error" => $e->getMessage())));
}