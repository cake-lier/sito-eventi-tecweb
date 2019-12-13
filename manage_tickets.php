<?php
require_once "bootstrap.php";

$data["result"] = false;
if (isset($_GET["seatId"]) && isset($_GET["eventId"]) && isset($_GET["actionType"])) {
    $eventId = intval($_GET["eventId"]);
    $seatId = intval($_GET["seatId"]);
    $actionType = intval($_GET["actionType"]);
    try {
        switch ($actionType) {
            case 0:
                $dbh->getCartsManager()->removeSeatCategoryFromCart($eventId, $seatId);
                $data["result"] = true;
                break;
            case 1:
                $dbh->getCartsManager()->decrementSeatTickets($eventId, $seatId);
                $data["result"] = true;
                break;
            case 2:
                if ($dbh->getCartsManager()->incrementSeatTickets($eventId, $seatId)) {
                    $data["result"] = true;
                }
                break;
        }
    } catch (\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($data);
header("Content-type: application/json");
?>