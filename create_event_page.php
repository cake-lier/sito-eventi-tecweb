<?php
require_once "bootstrap.php";

try {
    if (isset($_SESSION["email"]) && $dbh->getUsersManager()->isPromoter($_SESSION["email"])) {
        $templateParams["title"] = "SeatHeat - Nuovo evento";
        $templateParams["name"] = "create_event_form.php";
        $templateParams["js"] = 
            [
                "https://code.jquery.com/jquery-3.4.1.min.js", 
                JS_DIR . "common.js",
                JS_DIR . "create_event_page.js"
            ];
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
    require "template/error_base.php";
}
?>
