<?php
require_once "bootstrap.php";

header("Content-type: application/json");
$data = ["result" => false];
try {
    if (isset($_GET["type"])) {
        $type = intval($_GET["type"]);
        $promoterEmail = "";
        if (isset($_GET["promoter"]) && $_GET["promoter"] !== "") {
            $promoters = $dbh->getUsersManager()->getPromoters();
            array_walk($promoters, function($p) use (&$promoterEmail) {
                if($p["organizationName"] === $_GET["promoter"]) {
                    $promoterEmail = $p["email"];
                }
            });
        }
        if (isset($_GET["tags"]) && $_GET["tags"] !== "") {
            $eventIdsUncategorized = $dbh->getEventsManager()->getEventIdsFiltered(-1,
                                                                                   -1,
                                                                                   isset($_GET["keyword"]) ? $_GET["keyword"] : "",
                                                                                   isset($_GET["free"]) ? $_GET["free"] : true,
                                                                                   isset($_GET["place"]) ? $_GET["place"] : "",
                                                                                   isset($_GET["date"]) ? $_GET["date"] : "",
                                                                                   $promoterEmail);
            $tags = explode(" ", str_replace("#", "", $_GET["tags"]));
            $filteredIds = array();
            array_walk($eventIdsUncategorized, function($id) use ($dbh, $tags, &$filteredIds) {
                if ($dbh->getEventsManager()->hasEventCategories($id, ...$tags)) {
                    array_push($filteredIds, $id);
                }
            });
            $data["count"] = count($filteredIds);
        } else {
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
        }
        $data["result"] = true;
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
};
echo json_encode($data);
?>
