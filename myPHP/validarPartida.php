<?php
session_start();
require "connection.php";
global $conn;

$id = $_POST["id"];
$email = $_POST["usuari"];

// valido campos vacios
if ($id === "" || $email === "") {
    print_r(json_encode(["estat" => "KO", "error" => "ID i usuari són obligatoris."]));
    exit;
}

try {
    //miro si existe la partida
    $q1 = $conn->prepare("SELECT 1 FROM partides WHERE idpartides = :id");
    $q1->bindParam(":id", $id, PDO::PARAM_INT);
    $q1->execute();
    if ($q1->rowCount() === 0) {
        print_r(json_encode(array("estat" => "KO", "error" => "La partida no existeix.")));
        exit;
    }

    // consulto quesos
    $q2 = $conn->prepare("SELECT pu.formatges FROM partides_usuaris pu JOIN usuaris_app ua ON ua.id = pu.idusuari WHERE pu.idpartida = :id AND ua.nom = :email");
    $q2->bindParam(":id",$id,PDO::PARAM_INT);
    $q2->bindParam(":email",$email,PDO::PARAM_STR);
    $q2->execute();
    $row = $q2->fetch(PDO::FETCH_ASSOC);

    // si no existeix registro ok
    if (!$row) {
        print_r(json_encode(array("estat" => "OK")));
        exit;
    }

    // si existe compruebo si tienes los 6 quesitos
    $quesitos = json_decode($row["formatges"], true);
    if (is_array($quesitos) && count(array_filter($quesitos)) === 6) {
        print_r(json_encode(array("estat" => "KO", "error" => "Ja has completat tots els formatges.")));
    } else {
        print_r(json_encode(array("estat" => "OK")));
    }
} catch (Exception $e) {
    print_r(json_encode(array("estat" => "KO", "error" => "Error intern: " . $e->getMessage())));
}