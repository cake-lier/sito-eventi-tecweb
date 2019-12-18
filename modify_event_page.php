<?php
require_once "bootstrap.php";

try {
    if (isset($_GET["id"]) && $dbh->getEventsManager()->isLoggedUserEventOwner(intval($_GET["id"]))) {
        $templateParams["title"] = "SeatHeat - Modifica evento";
        $templateParams["name"] = "modify_event_form.php";
        $templateParams["event"] = $dbh->getEventsManager()->getEventInfo(intval($_GET["id"]));
        $templateParams["event"]["dateTime"] = str_replace(" ", "T", $templateParams["event"]["dateTime"]);
        $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "modify_event_page.js"];
        $templateParams["user_area_link"] = "user_area.php";
        $templateParams["user_area_alt"] = "Area personale";
        $templateParams["user_area_img"] = getProfileImage($dbh, $_SESSION["email"]);
        $templateParams["user_area_class"] = "profile_icon";
        $templateParams["showCart"] = false;
        $templateParams["showMyEvents"] = true;
        $templateParams["showCreateEvent"] = true;
        $templateParams["showLogout"] = true;
        require "template/base.php";
    } else {
        header("location: index.php");
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $templateParams["title"] = "Uh oh! Si è verificato un errore!";
    $templateParams["name"] = "template/error.php";
    require "template/base_error.php";
}

?>
