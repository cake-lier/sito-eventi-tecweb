<?php
require_once "bootstrap.php";

header("Content-type: application/json");
$data = ["result" => false];
try {
    if (isset($_GET["type"])) {
        $type = intval($_GET["type"]);
        $data["count"] = $type === 1
                         ? $dbh->getEventsManager()->getEventsCount()
                         : ($type === 2
                            ? $dbh->getEventsManager()->getPurchasedEventsCount()
                            : ($type === 3
                               ? $dbh->getEventsManager()->getCreatedEventsCount()
                               : 0));
        $data["result"] = true;
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
};
echo json_encode($data);
?>
