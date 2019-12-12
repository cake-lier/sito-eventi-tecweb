<?php
require_once "bootstrap.php";

$data = ["result" => false];
if (isset($_GET["seatId"]) && isset($_GET["eventId"]) && isset($_GET["amount"])) {
    $seatId = intval($_GET["seatId"]);
    $eventId = intval($_GET["eventId"]);
    $amount = intval($_GET["amount"]);
    try {
        if (!isset($_SESSION["email"])) {
            $_SESSION["cart"][] = [$seatId, $eventId, $amount];
            $data["result"] = true;
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
