<?php
    require_once "bootstrap.php";
    $result = "Compilare tutto il form, prego, e assicurarsi di essere loggati!";
    header("Content-Type: application/json");
    if (isset($_SESSION["email"]) 
        && $dbh->getUsersManager()->isPromoter($_SESSION["email"])
        && isset($_POST["name"])
        && isset($_POST["place"])
        && isset($_POST["dateTime"])
        && isset($_POST["description"])
        // TODO: event categories
        && isset($_POST["seatCategories"])) {
        try {
            $qResult = $dbh->getEventsManager()->createEvent($_POST["name"], $_POST["place"], $_POST["dateTime"],
                                                                $_POST["description"], $_SESSION["email"], $_POST["seatCategories"], array(), $_POST["website"]);
            if ($qResult) {
                $result = "Evento creato!";
            } else {
                $result = "Problema nel creare un nuovo evento!";
            }
        } catch (\Exception $e) {
            $result = "Problema nel creare un nuovo evento!";
        }
    }
    echo json_encode(array("result" => $result));
?>