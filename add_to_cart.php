<?php
require_once "bootstrap.php";

$seatId = $_GET["seatId"];
$eventId = $_GET["eventId"];
$amount = $_GET["amount"];
$result = "failure";
if (isset($id) && isset($eventId) && isset($amount)) {
    try {
        if ($dbh->getCartsManager()->putTicketsIntoCart($eventId, $seatId, $amount)) {
            $result = "success";
        }
    } catch(\Exception $e) {}
}

echo json_encode($result);
header("Content-type: application/json");
?>
