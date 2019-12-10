<?php
require_once "bootstrap.php";

$data = ["result" => false];
if (isset($_GET["seatId"]) && isset($_GET["eventId"]) && isset($_GET["amount"])) {
    try {
        $seatId = $_GET["seatId"];
        $eventId = $_GET["eventId"];
        $amount = $_GET["amount"];
        if ($dbh->getCartsManager()->putTicketsIntoCart($eventId, $seatId, $amount)) {
            $data["result"] = true;
        }
    } catch(\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($result);
header("Content-type: application/json");
?>
