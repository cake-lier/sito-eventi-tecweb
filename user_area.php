<?php
    require_once "bootstrap.php";

    if (isset($_SESSION["email"])) {
        //Base Template
        $templateParams["title"] = "SeatHeat - Area personale";
        $templateParams["name"] = "user_area_base.php";
    } else {
        header("location: index.php");
    }

    require 'template/base.php';
?>