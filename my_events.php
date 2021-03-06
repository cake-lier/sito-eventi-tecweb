<?php
require_once "bootstrap.php";

try {
    if (isset($_SESSION["email"]) && !$dbh->getUsersManager()->isAdmin($_SESSION["email"])) {
        //Base Template
        $templateParams["title"] = "SeatHeat - I miei eventi";
        $templateParams["name"] = "events_list_display.php";
        $templateParams["js"] = [
                                     "https://code.jquery.com/jquery-3.4.1.min.js",
                                     JS_DIR . "common.js"
                                ];
        $templateParams["events"] = array();
        if ($dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
            $eventIds = array_column($dbh->getEventsManager()->getPurchasedEvents(), "id");
            $templateParams["js"][] = JS_DIR . "change_displayed_purchased_events.js";
        } else {
            // it's necessarily a promoter
            $min = isset($_GET["min"]) ? $_GET["min"] : 0;
            $count = isset($_GET["count"]) ? $_GET["count"] : 5;
            $eventIds = $dbh->getEventsManager()->getEventIdsFiltered($min, $min + $count, "", false, "", "", $_SESSION["email"]);
            $templateParams["js"][] = JS_DIR . "change_displayed_created_events.js";
        }
        array_walk($eventIds, function($id) use (&$templateParams, $dbh) {
            $info = $dbh->getEventsManager()->getEventInfo($id);
            $info["dateTime"] = convertDateTimeToLocale($info["dateTime"]);
            $templateParams["events"][] 
                = array_merge(["id" => $id, "isLoggedUserEventOwner" => $dbh->getEventsManager()->isLoggedUserEventOwner($id)],
                              $info);
        });
        $templateParams["user_area_link"] = "user_area.php";
        $templateParams["user_area_alt"] = "Area personale";
        $templateParams["user_area_img"] = getProfileImage($dbh, $_SESSION["email"]);
        $templateParams["user_area_class"] = "profile_icon";
        $templateParams["showCart"] = $dbh->getUsersManager()->isCustomer($_SESSION["email"]);
        $templateParams["showMyEvents"] = true;
        $templateParams["showCreateEvent"] = $dbh->getUsersManager()->isPromoter($_SESSION["email"]);
        $templateParams["showLogout"] = true;
        require 'template/base.php';
    } else {
        header("location: index.php");
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $templateParams["title"] = "Uh oh! Si ?? verificato un errore";
    $templateParams["name"] = "template/error.php";
    require 'template/error_base.php';
}
?>