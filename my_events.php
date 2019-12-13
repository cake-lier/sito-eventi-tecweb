<?php
    require_once "bootstrap.php";

    if (isset($_SESSION["email"]) && !$dbh->getUsersManager()->isAdmin($_SESSION["email"])) {
        //Base Template
        $templateParams["title"] = "SeatHeat - I miei eventi";
        $templateParams["name"] = "events_list_display.php";
        $templateParams["events"] = array();
        if ($dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
            $eventIds = array_column($dbh->getEventsManager()->getPurchasedEvents(), "id");
        } else {
            // it's necessarily a promoter
            $eventIds = $dbh->getEventsManager()->getEventIdsFiltered(0, 4, "", false, null, null, $_SESSION["email"]);
        }
        array_walk($eventIds, function($id) use (&$templateParams, $dbh) {
            $info = $dbh->getEventsManager()->getEventInfo($id);
            $templateParams["events"][] = array_merge(["id" => $id], $info);
        });
        $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "change_events_page.js"];
    } else {
        header("location: index.php");
    }

    require 'template/base.php';
?>