<?php
    require_once "bootstrap.php";

    if (isset($_GET["user"])) {
        $user = $_GET["user"];
        $userShortData = $dbh->getUsersManager()->getUserShortProfile($user);

        header("Content-type: image/jpeg");
        echo $userShortData[0]["profilePhoto"];
    }
?>