<?php
require_once "bootstrap.php";

if (!isset($_SESSION["email"])) {
    if (count($_SESSION["cart"]) === 0) {
        $templateParams["name"] = "empty_cart.php";
    } else {
        $templateParams["name"] = "cart_sections.php";
        $templateParams["cartPaymentSection"] = "login_form.php";
        array_walk($_SESSION["cart"], function($e) use (&$templateParams) {
            $templateParams["tickets"][] = array_merge($dbh->getEventsManager()->getSeatInfo($e["eventId"], $e["seatId"]),
                                                       $e["amount"]);
        });
    }
} else {
    if (!$dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
        header("location: index.php");
    }
    $templateParams["tickets"] = $dbh->getCartsManager()->getLoggedUserTickets();
    if (count($templateParams["tickets"]) === 0) {
        $templateParams["name"] = "empty_cart.php";
    } else {
        $templateParams["name"] = "cart_sections.php";
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
$templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "cart.js"];
require "template/base.php";
?>