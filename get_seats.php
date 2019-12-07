<?php
require_once "bootstrap.php";

$id = $_GET["id"];
if (!isset($id)) {
    die("No id was passed as argument");
}
echo $dbh->getEventsManager()->getEventSeatcategories($id);
header("Content-type: application/json");
?>
