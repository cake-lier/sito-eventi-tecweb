<?php

use it\unibo\tecweb\seatheat\DatabaseHelper;

function getProfileImage(DatabaseHelper $dbh, string $userEmail) {
    $userShortData = $dbh->getUsersManager()->getUserShortProfile($userEmail);
    return $userShortData["profilePhoto"];
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

function encodeImg(string $name, string $tmp) {
    $imgFileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $extensions = array("jpg", "jpeg", "png");
    if (in_array($imgFileType, $extensions)) {
        $imgBase64 = base64_encode(file_get_contents($tmp));
        $img = "data:image/".$imgFileType.";base64,".$imgBase64;
        return $img;
    }
    return false;
}

function unsetMatrixIfEmpty(array &$matrix, int $firstIndex, int $secondIndex) {
    unset($matrix[$firstIndex][$secondIndex]);
    if (empty($matrix[$firstIndex])) {
        unset($matrix[$firstIndex]);
        if (empty($matrix)) {
            unset($matrix);
        }
    }
}

function useTopEventsTemplate(&$templateParams, $dbh) {
    $templateParams["mostRecentEvent"]
        = $dbh->getEventsManager()->getEventInfo($dbh->getEventsManager()->getMostRecentEvent());
    $templateParams["mostRecentEvent"]["dateTime"]
        = convertDateTimeToLocale($templateParams["mostRecentEvent"]["dateTime"]);
    $templateParams["mostRecentEvent"]["isLoggedUserEventOwner"]
        = $dbh->getEventsManager()->isLoggedUserEventOwner($templateParams["mostRecentEvent"]["id"]);
    $templateParams["mostPopularEvent"]
        = $dbh->getEventsManager()->getEventInfo($dbh->getEventsManager()->getMostPopularEvent());
    $templateParams["mostPopularEvent"]["dateTime"]
        = convertDateTimeToLocale($templateParams["mostPopularEvent"]["dateTime"]);
    $templateParams["mostPopularEvent"]["isLoggedUserEventOwner"]
        = $dbh->getEventsManager()->isLoggedUserEventOwner($templateParams["mostPopularEvent"]["id"]);
}

function useEmptyCartTemplate(&$templateParams, $dbh) {
    useTopEventsTemplate($templateParams, $dbh);
    $templateParams["cartBody"] = "empty_cart.php";
}
?>