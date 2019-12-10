<?php
require_once "bootstrap.php";

$data = ["result" => false];
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    try {
        $data["seatCategories"] = $dbh->getEventsManager()->getEventSeatCategories($id);
        $data["result"] = true;
    } catch(\Exception $e) {
        error_log($e->getMessage(), 3, LOG_FILE);
    }
}
echo json_encode($data);
header("Content-type: application/json");
?>
