<?php
require_once "bootstrap.php";

$data["result"] = false;
$data["total"] = 0;
header("Content-type: application/json");
if (isset($_GET["seatId"]) && isset($_GET["eventId"]) && isset($_GET["actionType"])) {
    $eventId = intval($_GET["eventId"]);
    $seatId = intval($_GET["seatId"]);
    $actionType = intval($_GET["actionType"]);
    try {
        switch ($actionType) {
            case 0:
                if (isset($_SESSION["email"])) {
                    $dbh->getCartsManager()->removeSeatCategoryFromCart($eventId, $seatId);
                } else {
                    unsetMatrixIfEmpty($_SESSION["cart"], $eventId, $seatId);
                }
                $data["result"] = true;
                break;
            case 1:
                if (isset($_SESSION["email"])) {
                    $dbh->getCartsManager()->decrementSeatTickets($eventId, $seatId);
                } else {
                    $_SESSION["cart"][$eventId][$seatId]--;
                    if ($_SESSION["cart"][$eventId][$seatId] === 0) {
                        unsetMatrixIfEmpty($_SESSION["cart"], $eventId, $seatId);
                    }
                }
                $data["result"] = true;
                break;
            case 2:
                if (isset($_SESSION["email"])) {
                    if ($dbh->getCartsManager()->incrementSeatTickets($eventId, $seatId)) {
                        $data["result"] = true;
                    }
                } else {
                    $seat = $dbh->getEventsManager()->getSeatInfo($eventId, $seatId);
                    if ($seat["occupiedSeats"] + $_SESSION["cart"][$eventId][$seatId] + 1 <= $seat["seats"]) {
                        $_SESSION["cart"][$eventId][$seatId]++;
                        $data["result"] = true;
                    }
                }
                break;
        }
        if ($data["result"] === true) {
            $tickets = $dbh->getCartsManager()->getLoggedUserTickets();
            $partialCosts = array();
            array_walk($tickets, function($e, $i) use (&$tickets, &$partialCosts) {
                $partialCosts[] = $e["amount"] * $e["price"];
            });
            $data["total"] = number_format(array_sum($partialCosts), 2);
        }
    } catch (\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($data);
?>