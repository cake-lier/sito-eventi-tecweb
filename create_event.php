<?php
require_once "bootstrap.php";
    
$data = ["result" => "Compilare tutto il form e assicurarsi di essere loggati"];
header("Content-Type: application/json");
try {
    if (isset($_SESSION["email"]) 
        && $dbh->getUsersManager()->isPromoter($_SESSION["email"])
        && isset($_POST["name"])
        && isset($_POST["place"])
        && isset($_POST["dateTime"])
        && isset($_POST["description"])
        && isset($_POST["eventCategories"])
        && isset($_POST["seatCategories"])) {
        $dbh->getEventsManager()->createEvent($_POST["name"], $_POST["place"], $_POST["dateTime"], $_POST["description"],
                                              $_SESSION["email"], $_POST["seatCategories"], $_POST["eventCategories"],
                                              $_POST["website"]);
        $data["result"] = "Evento creato!";
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $data["result"] = "Si è verificato un errore, si prega di riprovare più tardi";
}
echo json_encode($data);
?>