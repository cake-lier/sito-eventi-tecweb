<?php
require_once "bootstrap.php";

if (!isset($_SESSION["email"])) {
    if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
        $templateParams["cartBody"] = "empty_cart.php";
    } else if (!empty($_SESSION["cart"])) {
        $templateParams["cartBody"] = "cart_with_tickets.php";
        $templateParams["cartPaymentSection"] = "login_form.php";
        $templateParams["location"] = "cart.php";
        array_walk($_SESSION["cart"], function($seatCategories, $eventId) use (&$templateParams, $dbh) {
            $event = $dbh->getEventsManager()->getEventInfo($eventId);
            array_walk($seatCategories, function($amount, $seatId) use ($event, $eventId, &$templateParams, $dbh) {
                $seat = $dbh->getEventsManager()->getSeatInfo($eventId, $seatId);
                $templateParams["tickets"][] = ["eventId" => $eventId,
                                                "seatId" => $seatId,
                                                "eventName" => $event["name"],
                                                "eventPlace" => $event["place"],
                                                "dateTime" => $event["dateTime"],
                                                "amount" => $amount,
                                                "category" => $seat["name"],
                                                "price" => $seat["price"]
                                               ]; 
            });
        });
    }
} else {
    if (!$dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
        header("location: index.php");
    }
    $templateParams["tickets"] = $dbh->getCartsManager()->getLoggedUserTickets();
    if (count($templateParams["tickets"]) === 0) {
        $templateParams["cartBody"] = "empty_cart.php";
    } else {
        $templateParams["cartBody"] = "cart_with_tickets.php";
        $templateParams["cartPaymentSection"] = "payment_section.php";
        $partialCosts = array();
        array_walk($templateParams["tickets"], function($e) use (&$templateParams, &$partialCosts) {
            $partialCosts[] = $e["amount"] * $e["price"];
        });
        $templateParams["total"] = array_sum($partialCosts);
        $templateParams["user"] = $dbh->getUsersManager()->getLoggedUserLongProfile();
    }
}
$templateParams["title"] = "SeatHeat - Carrello";
$templateParams["name"] = "cart_base.php";
$templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "cart.js"];
require "template/base.php";
?>