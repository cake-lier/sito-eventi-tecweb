<?php
    require_once "bootstrap.php";
    header("Content-type: application/json");
    try {
        $events = $dbh->getEventsManager()->getPurchasedEvents();
        echo json_encode($events);
    } catch (\Exception $e) {
        echo json_encode(array());
    }
?>