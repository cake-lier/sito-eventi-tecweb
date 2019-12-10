<?php
    require_once "bootstrap.php";

    if (isset($_GET["user"])) {
        $user = $_GET["user"];
        $userData = $dbh->getUsersManager()->getUserShortProfile($user);
        header("Content-type: application/json");
        echo json_encode($userData);
    } else {
        try {
            $userData = $dbh->getUsersManager()->getLoggedUserLongProfile();
            header("Content-type: application/json");
            echo json_encode($userData);
        } catch (\Exception $e) {
            echo json_encode("Problema con il database");
        }
    }
?>