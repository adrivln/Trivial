<?php
require 'connection.php';
global $conn;
$description = $_POST['description'];

//Valido campos vacios
if (trim($description) === ""){
    print_r(json_encode(array("estat" => "KO", "error" => "Tots els camps són obligatoris.")));
    exit;
}

try{
    $query = $conn->prepare("SELECT * FROM partides WHERE descripcio = :description");
    $query->bindParam(":description", $description);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($query->rowCount() == 1) {
        print_r(json_encode(array("estat" => "KO", "error" => "Ja existeix una partida amb aquesta descripció.")));
    } else {
        // Insertar partida
        $query = $conn->prepare("INSERT INTO partides (descripcio) VALUES (:description)");
        $query->bindParam(":description", $description, PDO::PARAM_STR);
        $query->execute();
        print_r(json_encode(array("estat" => "OK", "error" => "", "descripcion" => $description)));

    }

} catch (Exception $e) {
    echo $e->getMessage();
}