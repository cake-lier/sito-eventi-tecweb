<?php
require_once "bootstrap.php";
    if (isset($_SESSION["email"]) && isset($_POST["message"])) {
        try {
            $dbh->getNotificationsManager()->sendNotificationToAdmin($_POST["message"]);
            header("location: index.php");
        } catch (\Exception $e) {

        }
    }
?>
