<?php
require_once "bootstrap.php";

if (isset($_SESSION["email"])) {
    $templateParams["title"] = "SeatHeat - Home";
    $templateParams["name"] = "home.php";
} else {
    $templateParams["title"] = "SeatHeat - Login";
    $templateParams["name"] = "register_login.php";
    $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "login_page.js"];
    $templateParams["location"] = "login_page.php";
}

require "template/base.php";
?>
