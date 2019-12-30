<?php
require_once "bootstrap.php";

try {
    if (isset($_SESSION["email"]) && !$dbh->getUsersManager()->isAdmin($_SESSION["email"])) {
        //Base Template
        $templateParams["title"] = "SeatHeat - I miei eventi";
        $templateParams["name"] = "events_list_display.php";
        $templateParams["events"] = array();
        if ($dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
            $eventIds = array_column($dbh->getEventsManager()->getPurchasedEvents(), "id");
        } else {
            // it's necessarily a promoter
            $eventIds = $dbh->getEventsManager()->getEventIdsFiltered(0, 4, "", false, "", "", $_SESSION["email"]);
        }
        array_walk($eventIds, function($id) use (&$templateParams, $dbh) {
            $info = $dbh->getEventsManager()->getEventInfo($id);
            $templateParams["events"][] 
                = array_merge(["id" => $id, "isLoggedUserEventOwner" => $dbh->getEventsManager()->isLoggedUserEventOwner($id)],
                              $info);
        });
        $templateParams["js"] = [
                                     "https://code.jquery.com/jquery-3.4.1.min.js",
                                     JS_DIR . "common.js",
                                     JS_DIR . "change_displayed_events.js",
                                ];
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
    $templateParams["title"] = "Uh oh! Si è verificato un errore";
    $templateParams["name"] = "template/error.php";
    require 'template/error_base.php';
}
?>