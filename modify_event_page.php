<?php
require_once "bootstrap.php";

if (isset($_GET["id"]) 
    && $dbh->getEventsManager()->isLoggedUserEventOwner(intval($_GET["id"]))) {
    $templateParams["title"] = "SeatHeat - Modifica evento";
    $templateParams["name"] = "modify_event_form.php";
    $templateParams["event"] = $dbh->getEventsManager()->getEventInfo(intval($_GET["id"]));
    $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "modify_event_page.js"];
    require "template/base.php";
} else {
    header("location: index.php");
}
?>
