<?php
require_once "bootstrap.php";

$data = ["result" => false];
if (isset($_GET["eventId"])) {
    $eventId = $_GET["eventId"];
    try {
        $event = $dbh->getEventsManager()->getEventInfo($eventId);
        $data["freeSeats"] = $event["freeSeats"];
        $data["totalSeats"] = $event["totalSeats"];
        $data["result"] = true;
    } catch(\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($data);
header("Content-type: application/json");
?>
