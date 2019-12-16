<?php
    require_once "bootstrap.php";

    $keyword = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
    $free = isset($_GET["posti"]) && $_GET["posti"] === "free";
    $place = isset($_GET["place"]) ? $_GET["place"] : "";
    $date = isset($_GET["date"]) ? $_GET["date"] : "";
    $tags = isset($_GET["tags"]) && $_GET["tags"] !== "" ? explode(" ", str_replace("#", "", $_GET["tags"])) : array();
    $min = isset($_GET["min"]) ? $_GET["min"] : 0;
    $count = isset($_GET["count"]) ? $_GET["count"] : 5;
    $eventIdsUncategorized = $dbh->getEventsManager()->getEventIdsFiltered($min, $min + $count, $keyword, $free, $place, 
                                                                           $date);
    $templateParams["events"] = array();
    array_walk($eventIdsUncategorized, function($id) use ($dbh, $tags, &$templateParams) {
        if ($dbh->getEventsManager()->hasEventCategories($id, ...$tags)) {
            $templateParams["events"][] = array_merge(["id" => $id], $dbh->getEventsManager()->getEventInfo($id));
        }
    });
    $templateParams["places"] = $dbh->getEventsManager()->getEventsPlaces();
    $templateParams["name"] = "search_form.php";
    $templateParams["searchSecondSection"] = "events_list_display.php";
    $templateParams["title"] = "SeatHeat - Cerca";
    $templateParams["js"] = [
        "https://code.jquery.com/jquery-3.4.1.min.js",
        JS_DIR . "search.js",
        JS_DIR . "change_displayed_events.js"
    ];
    require "template/base.php";
?> 
