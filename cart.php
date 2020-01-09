<?php
require_once "bootstrap.php";

try {
    if (!isset($_SESSION["email"])) {
        if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
            useEmptyCartTemplate($templateParams, $dbh);
        } else if (!empty($_SESSION["cart"])) {
            $templateParams["cartBody"] = "cart_with_tickets.php";
            $templateParams["cartPaymentSection"] = "login_form.php";
            $templateParams["location"] = "cart.php";
            array_walk($_SESSION["cart"], function($seatCategories, $eventId) use (&$templateParams, $dbh) {
                $event = $dbh->getEventsManager()->getEventInfo($eventId);
                array_walk($seatCategories, function($amount, $seatId) use ($event, $eventId, &$templateParams, $dbh) {
                    $seat = $dbh->getEventsManager()->getSeatInfo($eventId, $seatId);
                    $templateParams["tickets"][] = [
                                                        "eventId" => $eventId,
                                                        "seatId" => $seatId,
                                                        "eventName" => $event["name"],
                                                        "eventPlace" => $event["place"],
                                                        "dateTime" => convertDateTimeToLocale($event["dateTime"]),
                                                        "amount" => $amount,
                                                        "category" => $seat["seatName"],
                                                        "price" => $seat["price"]
                                                    ]; 
                });
            });
        }
        $templateParams["user_area_link"] = "login_page.php";
        $templateParams["user_area_alt"] = "Login";
        $templateParams["user_area_img"] = IMG_DIR . "login.png";
        $templateParams["user_area_class"] = "icon";
        $templateParams["showCart"] = true;
        $templateParams["showMyEvents"] = false;
        $templateParams["showCreateEvent"] = false;
        $templateParams["showLogout"] = false;
    } else {
        if (!$dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
            header("location: index.php");
        }
        $templateParams["tickets"] = $dbh->getCartsManager()->getLoggedUserTickets();
        if (count($templateParams["tickets"]) === 0) {
            useEmptyCartTemplate($templateParams, $dbh);
        } else {
            $templateParams["cartBody"] = "cart_with_tickets.php";
            $templateParams["cartPaymentSection"] = "payment_section.php";
            $partialCosts = array();
            array_walk($templateParams["tickets"], function($e, $i) use (&$templateParams, &$partialCosts) {
                $partialCosts[] = $e["amount"] * $e["price"];
                $templateParams["tickets"][$i]["dateTime"] = convertDateTimeToLocale($e["dateTime"]);
            });
            $templateParams["total"] = number_format(array_sum($partialCosts), 2);
            $templateParams["user"] = $dbh->getUsersManager()->getLoggedUserLongProfile();
        }
        $templateParams["user_area_link"] = "user_area.php";
        $templateParams["user_area_alt"] = "Area personale";
        $templateParams["user_area_img"] = getProfileImage($dbh, $_SESSION["email"]);
        $templateParams["user_area_class"] = "profile_icon";
        $templateParams["showCart"] = $dbh->getUsersManager()->isCustomer($_SESSION["email"]);
        $templateParams["showMyEvents"] = !$dbh->getUsersManager()->isAdmin($_SESSION["email"]);
        $templateParams["showCreateEvent"] = $dbh->getUsersManager()->isPromoter($_SESSION["email"]);
        $templateParams["showLogout"] = true;
    }
    $templateParams["title"] = "SeatHeat - Carrello";
    $templateParams["name"] = "cart_base.php";
    $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "common.js", JS_DIR . "cart.js"];
    require "template/base.php";
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $templateParams["title"] = "Uh oh! Si è verificato un errore!";
    $templateParams["name"] = "template/error.php";
    require "template/error_base.php";
}
?>