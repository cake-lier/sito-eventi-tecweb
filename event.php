<?php
require_once "bootstrap.php";

try {
    if (isset($_GET["id"])) {
        $id = intval($_GET["id"]);
        $templateParams["event"] = $dbh->getEventsManager()->getEventInfo($id);
        $templateParams["event"]["dateTime"] = convertDateTimeToLocale($templateParams["event"]["dateTime"]);
        $templateParams["places"] = $dbh->getEventsManager()->getEventsPlaces();
        $templateParams["name"] = "search_form.php";
        $templateParams["searchSecondSection"] = "event_display.php";
        $templateParams["title"] = "SeatHeat - Evento: " . $templateParams["event"]["name"];
        $templateParams["js"] = [
            "https://code.jquery.com/jquery-3.4.1.min.js",
            JS_DIR . "search.js"
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
            $templateParams["isLoggedUserCustomer"] = true;
            $templateParams["js"][] = JS_DIR . "purchase_flip.js";
        } else {
            $templateParams["user_area_link"] = "user_area.php";
            $templateParams["user_area_alt"] = "Area personale";
            $templateParams["user_area_img"] = getProfileImage($dbh, $_SESSION["email"]);
            $templateParams["user_area_class"] = "profile_icon";
            $templateParams["showLogout"] = true;
            if ($dbh->getUsersManager()->isCustomer($_SESSION["email"])) {
                $templateParams["isLoggedUserCustomer"] = true;
                $templateParams["js"][] = JS_DIR . "purchase_flip.js";
                $templateParams["showCart"] = true;
                $templateParams["showMyEvents"] = true;
                $templateParams["showCreateEvent"] = false;
            } else {
                $templateParams["isLoggedUserCustomer"] = false;
                $templateParams["showCart"] = false;
                $templateParams["showMyEvents"] = $dbh->getUsersManager()->isPromoter($_SESSION["email"]);
                $templateParams["showCreateEvent"] = $dbh->getUsersManager()->isPromoter($_SESSION["email"]);
            }
        }
        require "template/base.php";
    } else {
        header("location: search.php?keyword=\"\"");
    }
} catch(\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $templateParams["name"] = "template/error.php";
    $templateParams["title"] = "Uh oh! Si è verificato un errore!";
    require "template/error_base.php";
}
?>