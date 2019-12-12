<?php
require_once "bootstrap.php";

if (isset($_SESSION["email"]) && $dbh->getUsersManager()->isPromoter($_SESSION["email"])) {
    $templateParams["title"] = "SeatHeat - Nuovo evento";
    $templateParams["name"] = "create_event_form.php";
    $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "change_events_page.js"];
    require "template/base.php";
} else {
    header("location: index.php");
}
?>
