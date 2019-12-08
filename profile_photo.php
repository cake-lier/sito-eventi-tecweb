<?php
    require_once "bootstrap.php";

    $user = $_GET["user"];
    $userShortData = $dbh->getUsersManager()->getUserShortProfile($user);

    header("Content-type: image/jpeg");
    echo $userShortData[0]["profilePhoto"];
?>