<?php
require "connection.php";
global $conn;

$idpregunta = (int) $_POST["idpregunta"];
$eleccio = (int) $_POST["eleccio"];
$formatge = $_POST["formatge"];
$partidaId = (int) $_POST["partidaId"];
$usuari = $_POST["usuari"];

//Convierto el quesito a int
if($formatge === "1"){
    $esFormatge = 1;
} else {
    $esFormatge = 0;
}

//valido que no hayan campos vacios
if(!$idpregunta || !$eleccio || !$partidaId || !$usuari){
    print_r(json_encode(array("estat" => "KO", "error" => "Dades incompletes")));
    exit;
}

//Obtengo solucion correcta y tema
try{
    //Preparo array de quesitos
    $formatgesArray = array();
    for($i = 1; $i <= 6; $i++){
        $formatgesArray[$i] = false;
    }
    $query = $conn->prepare("SELECT solucio, tema FROM preguntes WHERE idpreguntes = :idpregunta");
    $query->bindParam(":idpregunta", $idpregunta, PDO::PARAM_INT);
    $query->execute();

    if($query->rowCount() !== 1){
        print_r(json_encode(array("estat" => "KO", "error" => "Pregunta no trobada")));
    }

    $pregunta = $query->fetch(PDO::FETCH_ASSOC);
    $solucio = (int) $pregunta["solucio"];
    $tema = (int) $pregunta["tema"];

    //Compruebo si la eleccion es correcta
    if($eleccio !== $solucio){
        print_r(json_encode(array("estat" => "KO", "error" => "Respota incorrecta. Torna-ho a intentar.")));
        exit;
    }

    //Si es quesito actualizo tabla partides_usuaris
    if($esFormatge === 1){
        $queryUsuari = $conn->prepare("SELECT id FROM usuaris_app WHERE nom = :usuari");
        $queryUsuari->bindParam(":usuari", $usuari, PDO::PARAM_STR);
        $queryUsuari->execute();

        if($queryUsuari->rowCount() !== 1){
            print_r(json_encode(array("estat" => "KO", "error" => "Usuari no trobat.")));
        }
        $rowUsuari = $queryUsuari->fetch(PDO::FETCH_ASSOC);
        $idUsuari = (int) $rowUsuari["id"];
        //Compruebo si hay registro en partides_usuaris
        $queryPartidesUsuari = $conn->prepare("SELECT formatges FROM partides_usuaris WHERE idpartida = :partidaId AND idusuari = :idUsuari");
        $queryPartidesUsuari->bindParam(":partidaId", $partidaId, PDO::PARAM_INT);
        $queryPartidesUsuari->bindParam(":idUsuari", $idUsuari, PDO::PARAM_INT);
        $queryPartidesUsuari->execute();

        if($queryPartidesUsuari->rowCount() === 0){
            //inserto nuevo registro
            $formatgesArray[$tema] = true;
            $jsonFormatgesArray = json_encode($formatgesArray);

            $queryInsert = $conn->prepare("INSERT INTO partides_usuaris(idusuari, idpartida, formatges) VALUES (:idUsuari, :partidaId, :formatges)");
            $queryInsert->bindParam(":idUsuari", $idUsuari, PDO::PARAM_INT);
            $queryInsert->bindParam(":partidaId", $partidaId, PDO::PARAM_INT);
            $queryInsert->bindParam(":formatges", $jsonFormatgesArray, PDO::PARAM_STR);
            $queryInsert->execute();

        } else{
            //Si existe actualizo
            $rowPartidaUsuari = $queryPartidesUsuari->fetch(PDO::FETCH_ASSOC);
            $formatgesArray = json_decode($rowPartidaUsuari["formatges"], true);
            if(! is_array($formatgesArray)){
                $formatgesArray = array();
                for($i = 1; $i <= 6; $i++){
                    $formatgesArray[$i] = false;
                }
            }
            $formatgesArray[$tema] = true;
            $jsonFormatgesArray = json_encode($formatgesArray);

            $queryUpdate = $conn->prepare("UPDATE partides_usuaris SET formatges = :formatges WHERE idpartida = :partidaId AND idusuari = :idUsuari");
            $queryUpdate->bindParam(":formatges", $jsonFormatgesArray, PDO::PARAM_STR);
            $queryUpdate->bindParam(":partidaId", $partidaId, PDO::PARAM_INT);
            $queryUpdate->bindParam(":idUsuari", $idUsuari, PDO::PARAM_INT);
            $queryUpdate->execute();
        }
        //Cuento los quesitos que tiene
        $countFormatges = 0;
        foreach ($formatgesArray as $tincFormatge) {
            if($tincFormatge === true){
                $countFormatges++;
            }
        }
        //Verificacion partida acabada
        if($countFormatges === 6){
            $partidaAcabada = true;
        } else {
            $partidaAcabada = false;
        }
    } else {
        $countFormatges = 0;
        $partidaAcabada = false;
    }

    $respuesta = array(
        "estat"           => "OK",
        "correcte"        => true,
        "formatge"        => (bool) $esFormatge,
        "countFormatges"  => $countFormatges,
        "partidaAcabada"  => $partidaAcabada
    );
    print_r(json_encode($respuesta));

} catch (Exception $e) {
    print_r(json_encode(array("estat" => "KO", "error" => $e->getMessage())));
}