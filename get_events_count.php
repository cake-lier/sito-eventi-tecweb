<?php
require_once "bootstrap.php";

header("Content-type: application/json");
$data = ["result" => false];
try {
    if (isset($_GET["type"])) {
        $type = intval($_GET["type"]);
        $promoterEmail = "";
        if (isset($_GET["promoter"])) {
            $promoters = $dbh->getUsersManager()->getPromoters();
            $promoter = array_filter($promoters, function($p) {
                return $p["organizationName"] === $_GET["promoter"];
            });
            $promoterEmail = $promoter[0]["email"];
        }
        $data["count"] = $type === 1
                         ? $dbh->getEventsManager()->getEventsCount(isset($_GET["keyword"]) ? $_GET["keyword"] : "",
                                                                    isset($_GET["free"]) ? $_GET["free"] : true,
                                                                    isset($_GET["place"]) ? $_GET["place"] : "",
                                                                    isset($_GET["date"]) ? $_GET["date"] : "",
                                                                    $promoterEmail)
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
