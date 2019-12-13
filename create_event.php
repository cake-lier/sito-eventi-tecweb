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
        && isset($_POST["eventCategories"])
        && isset($_POST["seatCategories"])) {
        try {
            $qResult = $dbh->getEventsManager()->createEvent($_POST["name"], $_POST["place"], $_POST["dateTime"],
                                                                $_POST["description"], $_SESSION["email"], $_POST["seatCategories"], $_POST["eventCategories"], $_POST["website"]);
            $result = "Evento creato!";
        } catch (\Exception $e) {
            $result = "Problema nel creare un nuovo evento! Il database potrebbe avere dei problemi, potresti non essere autorizzato
                        o magari hai sbagliato a inserire la data?"; // TODO: better error message
        }
    }
    echo json_encode(array("result" => $result));
?>