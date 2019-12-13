<?php
    require_once "bootstrap.php";
    header("Content-Type: application/json");
    if (isset($_GET["dateTime"]) && isset($_GET["id"])) {
        try {
            $dbh->getNotificationsManager()->toggleNotificationView($_GET["id"], $_GET["dateTime"]);
            echo json_encode(array("result" => true));
        } catch (\Exception $e) {
            echo json_encode(array("result" => false));
        }
    }
?>