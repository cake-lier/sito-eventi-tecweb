<?php
require_once "bootstrap.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $templateParams["event"] = $dbh->getEventsManager()->getEventInfo($id);
    $templateParams["places"] = $dbh->getEventsManager()->getEventsPlaces();
    $templateParams["name"] = "search_form.php";
    $templateParams["searchSecondSection"] = "event_display.php";
    $templateParams["title"] = "SeatHeat - Evento: " . $templateParams["event"]["name"];
    $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "purchase_flip.js"];
    require "template/base.php";
} else {
    header("location: ./search.php?keyword=\"\"");
}
?>