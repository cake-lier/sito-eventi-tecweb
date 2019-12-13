<?php
require_once "bootstrap.php";

if (isset($_SESSION["email"])) {
    //Base Template
    $templateParams["title"] = "SeatHeat - Contatta gli admin";
    $templateParams["name"] = "admin_message_form.php";
    require 'template/base.php';
} else {
    header("location: login_page.php");
}

?>
