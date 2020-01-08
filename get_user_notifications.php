<?php
require_once "bootstrap.php";

header("Content-type: application/json");
$data = ["result" => false, "notifications" => array()];
try {
    $data["notifications"] = $dbh->getNotificationsManager()->getLoggedUserNotifications();
    array_walk($data["notifications"], function($notification, $index) use (&$data, $dbh) {
        $eventId = $notification["eventId"];
        unset($data["notifications"][$index]["eventId"]);
        if ($eventId !== null) {
            $data["notifications"][$index]["event"] = $dbh->getEventsManager()->getEventInfo($eventId);
        }
    });
    $data["result"] = true;
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
}
echo json_encode($data);
?>