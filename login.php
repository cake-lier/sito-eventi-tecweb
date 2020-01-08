<?php
require_once "bootstrap.php";

$location = "index.php";
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["landing_page"])) {
    $location = $_POST["landing_page"];
    try {
        if ($dbh->getUsersManager()->checkLogin($_POST["email"], $_POST["password"])) {
            $_SESSION["email"] = $_POST["email"];
            if (isset($_SESSION["cart"])) {
                if ($dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
                    array_walk($_SESSION["cart"], function($seatCategories, $eventId) use ($dbh) {
                        array_walk($seatCategories, function($amount, $seatId) use ($dbh, $eventId) {
                            $seat = $dbh->getEventsManager()->getSeatInfo($eventId, $seatId);
                            $freeSeats = $seat["seats"] - $seat["occupiedSeats"];
                            if ($amount > $freeSeats) {
                                $dbh->getCartsManager()->putTicketsIntoCart($eventId, $seatId, $freeSeats);
                                $_SESSION["cartError"] = true;
                            } else if (!$dbh->getCartsManager()->putTicketsIntoCart($eventId, $seatId, $amount)) {
                                $_SESSION["cartError"] = true;
                            }
                        });
                    });
                }
                unset($_SESSION["cart"]);
            }
            $location = "index.php";
        } else {
            $_SESSION["loginError"] = "Username o password errati";
        }
    } catch (\Exception $e) {
        $_SESSION["loginError"] = "Si è verificato un errore, si prega di riprovare più tardi";
    }
}
header("location: " . $location);
?>
