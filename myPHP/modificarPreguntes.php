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
$id = $_POST["id"];

//Valido campos vacios
if (trim($descripcio) === "" || trim($tema) === "" || trim($resp1) === "" || trim($resp2) === "" || trim($resp3) === "" || trim($resp4) === "" || trim($solucio) === "" || trim($id) === "") {
    print_r(json_encode(array("estat" => "KO", "error" => "Tots els camps són obligatoris.")));
    exit;
}

try{
    $query = $conn->prepare("SELECT * FROM preguntes WHERE idpreguntes = :id");
    $query->bindParam(":id", $id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if($query->rowCount() > 0){
        $query = $conn->prepare("UPDATE preguntes SET descripcio = :descripcio, tema = :tema, resposta1 = :resp1, resposta2 = :resp2, resposta3 = :resp3, resposta4 = :resp4, solucio = :solucio WHERE idpreguntes = :id");
        $query->bindParam(":descripcio", $descripcio, PDO::PARAM_STR);
        $query->bindParam(":tema", $tema, PDO::PARAM_STR);
        $query->bindParam(":resp1", $resp1, PDO::PARAM_STR);
        $query->bindParam(":resp2", $resp2, PDO::PARAM_STR);
        $query->bindParam(":resp3", $resp3, PDO::PARAM_STR);
        $query->bindParam(":resp4", $resp4, PDO::PARAM_STR);
        $query->bindParam(":solucio", $solucio, PDO::PARAM_STR);
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        print_r(json_encode(array("estat" => "OK", "error" => "")));
    } else {
        print_r(json_encode(array("estat" => "KO", "error" => "No s'ha trobat la pregunta.")));
    }

} catch (exception $e){
    print_r(json_encode(array("estat" => "KO", "error" => $e->getMessage())));
}
