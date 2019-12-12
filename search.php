<?php
require_once "bootstrap.php";

$templateParams["places"] = $dbh->getEventsManager()->getEventsPlaces();
$keyword = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
$eventIds = $dbh->getEventsManager()->getEventIdsFiltered(0, 4, $keyword, false);
$templateParams["events"] = array();
array_walk($eventIds, function($id) use (&$templateParams, $dbh) {
    $templateParams["events"][] = array_merge(["id" => $id], $dbh->getEventsManager()->getEventInfo($id));
});
$templateParams["name"] = "search_form.php";
$templateParams["searchSecondSection"] = "events_list_display.php";
$templateParams["title"] = "SeatHeat - Cerca";
$templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "change_events_page.js"];

require "template/base.php";
?> 
