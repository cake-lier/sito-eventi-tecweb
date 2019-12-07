<?php
require_once "bootstrap.php";

$templateParams["places"] = $dbh->getEventsManager()->getEventsPlaces();
$eventIds = !isset($_GET["keyword"]) || $_GET["keyword"] === ""
            ? $dbh->getEventsManager()->getEventIds()
            : $dbh->getEventsManager()->searchEventsForKeyword();
$templateParams["events"] = array();
array_walk($eventIds, function($id) use (&$templateParams, $dbh) {
    $templateParams["events"][] = array_merge(["id" => $id], $dbh->getEventsManager()->getEventInfo($id));
});
$templateParams["name"] = "search_form.php";
$templateParams["searchSecondSection"] = "events_list_display.php";
$templateParams["title"] = "SeatHeat - Search";

require "template/base.php";
?> 
