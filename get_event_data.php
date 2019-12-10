<?php
require_once "bootstrap.php";

$data = ["result" => false];
$keyword = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
$place = isset($_GET["place"]) ? $_GET["place"] : null;
$date = isset($_GET["date"]) ? $_GET["date"] : null;
$free = isset($_GET["free"]) ? $_GET["free"] : false;
if (isset($_GET["min"]) && isset($_GET["count"])) {
    $min = $_GET["min"];
    $count = $_GET["count"];
    try {
        $eventIds = $dbh->getEventsManager()->getEventIdsFiltered($min, $min + $count - 1, $keyword, $free, $place, $date);
        $data["events"] = array();
        array_walk($eventIds, function($id) use (&$data, $dbh) {
            $data["events"][] = array_merge(["id" => $id], $dbh->getEventsManager()->getEventInfo($id));
        });
    } catch(\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($data);
header("Content-type: application/json");
?>
