<?php
    require_once "bootstrap.php";

    if (isset($_SESSION["email"])) {
        //Base Template
        $templateParams["title"] = "SeatHeat - Area personale";
        $templateParams["name"] = "user_area_base.php";
        $templateParams["js"] = ["https://code.jquery.com/jquery-3.4.1.min.js", JS_DIR . "user_area.js"];
    } else {
        header("location: index.php");
    }

    require 'template/base.php';
?>