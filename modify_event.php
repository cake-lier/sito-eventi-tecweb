<?php
    require_once "bootstrap.php";
    $result = "Compilare tutto il form.";
    header("Content-Type: application/json");
    if (isset($_POST["dateTime"])
        && isset($_POST["message"])
        && isset($_POST["id"])
        && $dbh->getEventsManager()->isLoggedUserEventOwner(intval($_POST["id"]))) {
        try {
            $qResult = $dbh->getEventsManager()->changeEventDate(intval($_POST["id"]), $_POST["dateTime"], $_POST["message"]);
            $result = "Evento modificato!";
        } catch (\Exception $e) {
            $result = "Problema nel modificare l'evento!"; // TODO: better error message
        }
    }
    echo json_encode(array("result" => $result));
?>