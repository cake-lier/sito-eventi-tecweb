<?php
require_once "bootstrap.php";

$id = $_GET["id"];
if (!isset($id)) {
    die("No id was passed as argument");
}
echo json_encode($dbh->getEventsManager()->getEventSeatCategories($id));
header("Content-type: application/json");
?>
