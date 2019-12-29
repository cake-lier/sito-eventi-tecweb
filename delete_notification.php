<?php
require_once "bootstrap.php";

header("Content-Type: application/json");
$data = ["result" => false];
if (isset($_GET["dateTime"]) && isset($_GET["id"])) {
    try {
        $dbh->getNotificationsManager()->deleteUserNotification($_GET["id"], $_GET["dateTime"]);
        $data["result"] = true;
    } catch (\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($data);
?>