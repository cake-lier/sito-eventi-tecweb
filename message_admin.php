<?php
require_once "bootstrap.php";

try {
    if (isset($_SESSION["email"])) {
        //Base Template
        $templateParams["title"] = "SeatHeat - Contatta gli admin";
        $templateParams["name"] = "admin_message_form.php";
        $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "common.js"];
        $templateParams["user_area_link"] = "user_area.php";
        $templateParams["user_area_alt"] = "Area personale";
        $templateParams["user_area_img"] = getProfileImage($dbh, $_SESSION["email"]);
        $templateParams["user_area_class"] = "profile_icon";
        $templateParams["showCart"] = $dbh->getUsersManager()->isCustomer($_SESSION["email"]);
        $templateParams["showMyEvents"] = !$dbh->getUsersManager()->isAdmin($_SESSION["email"]);
        $templateParams["showCreateEvent"] = $dbh->getUsersManager()->isPromoter($_SESSION["email"]);
        $templateParams["showLogout"] = true;
        require 'template/base.php';
    } else {
        header("location: login_page.php");
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
    $templateParams["title"] = "Uh oh! Si è verificato un errore";
    $templateParams["name"] = "template/error.php";
    require 'template/error_base.php';
}


?>
