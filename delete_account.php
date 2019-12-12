<?php
    require_once "bootstrap.php";
    $result = false;
    try {
        if (isset($_POST["password"])) {
            $result = $dbh->getUsersManager()->deleteLoggedUser($_POST["password"]);
        }
    } catch (\Exception $e) {
    }
    if ($result) {
        session_unset();
        session_destroy();
        $location = "index.php";
    } else {
        $location = "";
    }
    header("Content-Type: application/json");
    echo json_encode(array("new_location" => $location));
?>