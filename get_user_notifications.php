<?php
    require_once "bootstrap.php";
    header("Content-type: application/json");
    try {
        $notifications = $dbh->getNotificationsManager()->getLoggedUserNotifications();
        echo json_encode($notifications);
    } catch (\Exception $e) {
        echo json_encode(array());
    }
?>