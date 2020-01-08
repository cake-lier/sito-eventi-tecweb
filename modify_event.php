<?php
require_once "bootstrap.php";

$data = ["result" => "Compilare tutto il form."];
header("Content-Type: application/json");
try {
    if (isset($_POST["dateTime"])
        && isset($_POST["message"])
        && isset($_POST["id"])
        && $dbh->getEventsManager()->isLoggedUserEventOwner(intval($_POST["id"]))) {
        $dateTime = str_replace("T", " ", $_POST["dateTime"]);
        $dbh->getEventsManager()->changeEventDate(intval($_POST["id"]), $dateTime, $_POST["message"]);
        $data["result"] = "Evento modificato.";
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $data["result"] = "Si è verificato un errore, si prega di riprovare più tardi.";
}
echo json_encode($data);
?>