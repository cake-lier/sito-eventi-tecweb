<?php
require_once "bootstrap.php";

$data = ["result" => false, "userData" => array()];
header("Content-type: application/json");
try {
    if (isset($_GET["user"])) {
        $data["userData"] = $dbh->getUsersManager()->getUserShortProfile($_GET["user"]);
    } else {
        $data["userData"] = $dbh->getUsersManager()->getLoggedUserLongProfile();        
    }
    $data["result"] = true;
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, LOG_FILE);
}
echo json_encode($data);
?>