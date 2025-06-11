<?php
require "connection.php";
global $conn;

$tema = (int) $_POST["tema"];
$formatge = $_POST["formatge"];

if($formatge === "1"){
    $formatge = 1;
} else {
    $formatge = 0;
}

if($tema < 1 || $tema > 6){
    print_r(json_encode(array("estat" => "KO", "error" => "Tema invàlid.")));
    exit;
}

try{
    $query = $conn -> prepare("SELECT * FROM preguntes WHERE tema = :tema");
    $query -> bindParam(':tema', $tema, PDO::PARAM_INT);
    $query -> execute();

    if($query->rowCount() > 0){
        $arrayPreguntas = $query->fetchAll(PDO::FETCH_ASSOC);
        $respuesta = $arrayPreguntas[rand(0, count($arrayPreguntas)-1)];
        $respuesta["estat"] = "OK";
        print_r(json_encode($respuesta));
    } else {
        print_r(json_encode(array("estat" => "KO", "error" => "No s'ha trobat cap pregunta.")));
    }
} catch (Exception $e){
    print_r(json_encode(array("estat" => "KO", "error" => $e->getMessage())));
}