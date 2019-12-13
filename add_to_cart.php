<?php
require_once "bootstrap.php";

$data = ["result" => false];
if (isset($_GET["seatId"]) && isset($_GET["eventId"]) && isset($_GET["amount"])) {
    $seatId = intval($_GET["seatId"]);
    $eventId = intval($_GET["eventId"]);
    $amount = intval($_GET["amount"]);
    try {
        if (!isset($_SESSION["email"])) {
            if (!isset($_SESSION["cart"][$eventId])) {
                $_SESSION["cart"][$eventId] = array();
            }
            $seat = $dbh->getEventsManager()->getSeatInfo($eventId, $seatId);
            if (!isset($_SESSION["cart"][$eventId][$seatId])) {
                if ($amount + $seat["occupiedSeats"] <= $seat["seats"]) {
                    $_SESSION["cart"][$eventId][$seatId] = $amount;
                    $data["result"] = true;
                }
            } else if ($amount + $seat["occupiedSeats"] + $_SESSION["cart"][$eventId][$seatId] <= $seat["seats"]) {
                $_SESSION["cart"][$eventId][$seatId] += $amount;
                $data["result"] = true;
            }
        } else if ($dbh->getCartsManager()->putTicketsIntoCart($eventId, $seatId, $amount)) {
            $data["result"] = true;
        }
    } catch(\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($data);
header("Content-type: application/json");
?>
