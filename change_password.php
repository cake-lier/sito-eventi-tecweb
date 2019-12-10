<?php
    require_once("bootstrap.php");
    header("Content-Type: application/json");
    if (isset($_SESSION["email"])
        && isset($_POST["old_password"])
        && isset($_POST["new_password"])
        && isset($_POST["new_password_repeat"])
        && $_POST["new_password"] === $_POST["new_password_repeat"]) {
        try {
            $result = $dbh->getUsersManager()->changePassword($_SESSION["email"], $_POST["old_password"], $_POST["new_password"]);
            if ($result) {
                echo json_encode(array("resultMessage" => "Password cambiata correttamente"));
            } else {
                echo json_encode(array("resultMessage" => "Ci sono stati problemi con il database, non é stato possibile cambiare la password!"));
            }
        } catch (\Exception $e) {
            echo json_encode(array("resultMessage" => "Ci sono stati problemi con il database, non é stato possibile cambiare la password!"));
        }
    }
?>