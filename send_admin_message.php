<?php
require_once "bootstrap.php";

if (isset($_SESSION["email"]) && isset($_POST["message"])) {
    try {
        $dbh->getNotificationsManager()->sendNotificationToAdmin($_POST["message"]);
    } catch (\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
header("location: index.php");
?>
