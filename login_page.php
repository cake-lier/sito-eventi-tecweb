<?php
require_once "bootstrap.php";

if (isset($_SESSION["email"])) {
    header("location: index.php");
} else {
    $templateParams["title"] = "SeatHeat - Login";
    $templateParams["name"] = "register_login.php";
    $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "common.js", JS_DIR . "login_page.js"];
    $templateParams["location"] = "login_page.php";
    $templateParams["user_area_link"] = "login_page.php";
    $templateParams["user_area_alt"] = "Login";
    $templateParams["user_area_img"] = IMG_DIR . "login.png";
    $templateParams["user_area_class"] = "icon";
    $templateParams["showCart"] = true;
    $templateParams["showMyEvents"] = false;
    $templateParams["showCreateEvent"] = false;
    $templateParams["showLogout"] = false;
}
require "template/base.php";
?>
