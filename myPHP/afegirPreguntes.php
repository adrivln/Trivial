<?php
require "connection.php";
global $conn;
$descripcio = $_POST["descripcio"];
$tema = $_POST["tema"];
$resp1 = $_POST["resp1"];
$resp2 = $_POST["resp2"];
$resp3 = $_POST["resp3"];
$resp4 = $_POST["resp4"];
$solucio = $_POST["solucio"];

//Valido campos vacios
if (trim($descripcio) === "" || trim($tema) === "" || trim($resp1) === "" || trim($resp2) === "" || trim($resp3) === "" || trim($resp4) === "" || trim($solucio) === "") {
    print_r(json_encode(array("estat" => "KO", "error" => "Tots els camps són obligatoris.")));
    exit;
}

try {
    $query = $conn->prepare("SELECT * FROM preguntes WHERE descripcio = :descripcio");
    $query->bindParam(":descripcio", $descripcio, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($query->rowCount() > 0) {
        print_r(json_encode(array("estat" => "KO", "error" => "Aquesta pregunta ya existeix.")));
    } else {
        $query = $conn->prepare("INSERT INTO preguntes (descripcio, tema, resposta1, resposta2, resposta3, resposta4, solucio) VALUES (:descripcio, :tema, :resp1, :resp2, :resp3, :resp4, :solucio)");
        $query->bindParam(":descripcio", $descripcio, PDO::PARAM_STR);
        $query->bindParam(":tema", $tema, PDO::PARAM_STR);
        $query->bindParam(":resp1", $resp1, PDO::PARAM_STR);
        $query->bindParam(":resp2", $resp2, PDO::PARAM_STR);
        $query->bindParam(":resp3", $resp3, PDO::PARAM_STR);
        $query->bindParam(":resp4", $resp4, PDO::PARAM_STR);
        $query->bindParam(":solucio", $solucio, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        print_r(json_encode(array("estat" => "OK", "error" => "")));
    }
} catch (Exception $e) {
    print_r(json_encode(array("estat" => "KO", "error" => $e->getMessage())));
}