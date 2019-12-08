<?php
require_once "bootstrap.php";

$eventId = $_GET["eventId"];
$data = ["result" => "failure"];
if (isset($eventId)) {
    try {
        $event = $dbh->getEventsManager()->getEventInfo($eventId);
        $data["freeSeats"] = $event["freeSeats"];
        $data["totalSeats"] = $event["totalSeats"];
        $data["result"] = "success";
    } catch(\Exception $e) {}
}

echo json_encode($data);
header("Content-type: application/json");
?>
