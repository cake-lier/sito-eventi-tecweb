<?php
require_once "bootstrap.php";

$keyword = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
$free = isset($_GET["posti"]) && $_GET["posti"] === "free";
$place = isset($_GET["place"]) ? $_GET["place"] : "";
$date = isset($_GET["date"]) ? $_GET["date"] : "";
if (isset($_GET["promoter"]) && $_GET["promoter"] !== "" && $dbh->getUsersManager()->getPromoterEmail($_GET["promoter"]) !== false) {
    $promoter = $dbh->getUsersManager()->getPromoterEmail($_GET["promoter"]);
    $promoter = $promoter !== false && $promoter !== null ? $promoter["email"] : "";
} else {
    $promoter = "";
}
$tags = isset($_GET["tags"]) && $_GET["tags"] !== "" ? explode(" ", str_replace("#", "", $_GET["tags"])) : array();
$min = isset($_GET["min"]) ? $_GET["min"] : 0;
$count = isset($_GET["count"]) ? $_GET["count"] : 5;
try {
    $eventIdsUncategorized = $dbh->getEventsManager()->getEventIdsFiltered(-1, -1, $keyword, $free, $place, $date, $promoter);
    $filteredIds = array();
    array_walk($eventIdsUncategorized, function($id) use ($dbh, $tags, &$filteredIds) {
        if ($dbh->getEventsManager()->hasEventCategories($id, ...$tags)) {
            array_push($filteredIds, $id);
        }
    });
    $filteredIds = array_slice($filteredIds, $min, $count);
    $templateParams["events"] = array();
    array_walk($filteredIds, function($id) use ($dbh, &$templateParams) {
        $eventInfo = $dbh->getEventsManager()->getEventInfo($id);
        $eventInfo["dateTime"] = convertDateTimeToLocale($eventInfo["dateTime"]);
        $templateParams["events"][] 
            = array_merge(["id" => $id, "isLoggedUserEventOwner" => $dbh->getEventsManager()->isLoggedUserEventOwner($id)], 
                            $eventInfo);
    });
    $templateParams["places"] = $dbh->getEventsManager()->getEventsPlaces();
    $templateParams["promoters"] = array_column($dbh->getUsersManager()->getPromoters(), "organizationName");
    $templateParams["name"] = "search_form.php";
    $templateParams["searchSecondSection"] = "events_list_display.php";
    $templateParams["title"] = "SeatHeat - Cerca";
    $templateParams["js"] = [
        "https://code.jquery.com/jquery-3.4.1.min.js",
        JS_DIR . "common.js",
        JS_DIR . "search.js",
        JS_DIR . "change_displayed_events.js"
    ];
    if (!isset($_SESSION["email"])) {
        $templateParams["user_area_link"] = "login_page.php";
        $templateParams["user_area_alt"] = "Login";
        $templateParams["user_area_img"] = IMG_DIR . "login.png";
        $templateParams["user_area_class"] = "icon";
        $templateParams["showCart"] = true;
        $templateParams["showMyEvents"] = false;
        $templateParams["showCreateEvent"] = false;
        $templateParams["showLogout"] = false;
    } else {
        $templateParams["user_area_link"] = "user_area.php";
        $templateParams["user_area_alt"] = "Area personale";
        $templateParams["user_area_img"] = getProfileImage($dbh, $_SESSION["email"]);
        $templateParams["user_area_class"] = "profile_icon";
        $templateParams["showCart"] = $dbh->getUsersManager()->isCustomer($_SESSION["email"]);
        $templateParams["showMyEvents"] = !$dbh->getUsersManager()->isAdmin($_SESSION["email"]);
        $templateParams["showCreateEvent"] = $dbh->getUsersManager()->isPromoter($_SESSION["email"]);
        $templateParams["showLogout"] = true;
    }
    require "template/base.php";
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $templateParams["title"] = "Uh oh! Si ?? verificato un errore!";
    $templateParams["name"] = "template/error.php";
    require "template/error_base.php";
}
?> 
