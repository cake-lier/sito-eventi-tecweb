<?php

use it\unibo\tecweb\seatheat\DatabaseHelper;

function getProfileImage(DatabaseHelper $dbh, string $userEmail) {
        $userShortData = $dbh->getUsersManager()->getUserShortProfile($userEmail);
        return $userShortData["profilePhoto"]; // TODO: will need to change
    }
?>