<?php
require_once "bootstrap.php";

if (isset($_SESSION["email"])) {
    $templateParams["title"] = "SeatHeat - Home";
    $templateParams["name"] = "home.php";
} else {
    $templateParams["title"] = "SeatHeat - Login";
    $templateParams["name"] = "register_login.php";
}

require "template/base.php";
?>
