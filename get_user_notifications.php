<?php
require_once "bootstrap.php";

header("Content-type: application/json");
$data = ["result" => false, "notifications" => array()];
try {
    $data["notifications"] = $dbh->getNotificationsManager()->getLoggedUserNotifications();
    $data["result"] = true;
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
}
echo json_encode($data);
?>