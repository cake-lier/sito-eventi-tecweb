<?php

use it\unibo\tecweb\seatheat\DatabaseHelper;

function getProfileImage(DatabaseHelper $dbh, string $userEmail) {
    $userShortData = $dbh->getUsersManager()->getUserShortProfile($userEmail);
    return $userShortData["profilePhoto"]; // TODO: will need to change
}

function convertDateTimeToLocale(string $dateTime) {
    $date = new \DateTime($dateTime);
    $formatter = new \IntlDateFormatter("it_IT", null, null);
    $formatter->setPattern("d MMMM yyyy");
    return $formatter->format($date) . " ore " . $date->format("H:i");
}

function convertDateToLocale(string $date) {
    $dateObj = new \DateTime($date);
    $formatter = new \IntlDateFormatter("it_IT", null, null);
    $formatter->setPattern("d MMMM yyyy");
    return $formatter->format($dateObj);
}
?>