<?php
require_once "bootstrap.php";

try {
    //Base Template
    $templateParams["title"] = "SeatHeat - Info";
    $templateParams["name"] = "info_section.php";
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
    $templateParams["title"] = "Uh oh! Si è verificato un errore!";
    $templateParams["name"] = "template/error.php";
    require "template/error_base.php";
}


?>
